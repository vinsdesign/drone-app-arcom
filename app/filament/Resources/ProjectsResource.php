<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectsResource\Pages;
use App\Filament\Resources\ProjectsResource\RelationManagers;
use App\Models\currencie;
use App\Models\customer;
use App\Models\Projects;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\View as InfolistView;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;
use View;
use App\Helpers\TranslationHelper;

class ProjectsResource extends Resource
{
    protected static ?string $model = Projects::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 4;
    public static ?string $navigationGroup = ' ';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::Where('teams_id',$teamID)->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Projects');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Projects');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->current_teams_id;
        return $form
            ->schema([
                Forms\Components\Section::make(TranslationHelper::translateIfNeeded('Projects'))
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('case')
                        ->label(TranslationHelper::translateIfNeeded('Case'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('revenue')
                        ->label(TranslationHelper::translateIfNeeded('Revenue'))
                            ->required()
                            ->numeric(),
                        Forms\Components\Select::make('currencies_id')
                        ->label(TranslationHelper::translateIfNeeded('Currency'))
                        ->options(currencie::all()->mapWithKeys(function ($currency) {
                            return [$currency->id => "{$currency->name} - {$currency->iso}"];}))
                            ->searchable()
                            ->required()
                            ->default(function (){
                                $currentTeam = auth()->user()->teams()->first();
                                return $currentTeam ? $currentTeam->currencies_id : null;
                            }),
                        Forms\Components\Select::make('customers_id')
                        ->label(TranslationHelper::translateIfNeeded('Customer'))
                            ->options(customer::where('teams_id', auth()->user()->teams()->first()->id)
                            ->pluck('name', 'id')
                            )->searchable()
                            ->placeholder(TranslationHelper::translateIfNeeded('Select an Customer'))
                            ->required(),
                        Forms\Components\TextArea::make('description')
                        ->label(TranslationHelper::translateIfNeeded('Description'))
                            ->required()
                            ->maxLength(255)->columnSpanFull(),

                ])->columns(2),
               
            ]);
    }

//edit query untuk action shared un-shared
    public static function getEloquentQuery(): Builder
    {
        $userId = auth()->user()->id;
        $query = parent::getEloquentQuery();
        
        if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
            return $query;
        }else{
            $query->where(function ($query) use ($userId) {
                $query->where('shared', 1);
            });
            return $query;
        }

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('case')
                ->label(TranslationHelper::translateIfNeeded('Case'))    
                    ->searchable(),
                Tables\Columns\TextColumn::make('flight_date')
                ->label(TranslationHelper::translateIfNeeded('Last Flight Date'))    
                    ->getStateUsing(function ($record) {
                        $lastFlight = $record->flighs()->orderBy('start_date_flight', 'desc')->first();
                        $totalFlights = $record->flighs()->count();
                        $lastFlightDate = optional($lastFlight)->start_date_flight ? $lastFlight->start_date_flight : '';
                        return "({$totalFlights}) Flights<br> {$lastFlightDate}";
                    })
                    ->sortable()
                    ->html(),
                Tables\Columns\TextColumn::make('revenue')
                ->label(TranslationHelper::translateIfNeeded('Revenue'))    
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currencies.iso')
                ->label(TranslationHelper::translateIfNeeded('Currencies'))    
                    ->searchable(),
                Tables\Columns\TextColumn::make('customers.name')
                ->label(TranslationHelper::translateIfNeeded('Customers'))    
                    ->numeric()
                    ->url(fn($record) => $record->customers_id ? route('filament.admin.resources.customers.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->customers_id,
                    ]):null)->color(Color::Blue)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                ->label(TranslationHelper::translateIfNeeded('Created at'))    
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                ->label(TranslationHelper::translateIfNeeded('Updated at'))    
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_visible')
                ->label('')
                ->options([
                    'current' => 'Current',
                    'archived' => 'Archived',
                ])
                ->default('current'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('views')  ->action(function ($record) {
                        session(['project_id' => $record->id]);
                        return redirect()->route('flight-peroject', ['project_id' => $record->id]);
                    })->label(TranslationHelper::translateIfNeeded('View'))->icon('heroicon-s-eye'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('Archive')->label(TranslationHelper::translateIfNeeded('Archive'))
                    ->hidden(fn ($record) => $record->status_visible == 'archived')
                            ->action(function ($record) {
                             $record->update(['status_visible' => 'archived']);
                             Notification::make()
                             ->title('Status Updated')
                             ->body("Status successfully changed.")
                             ->success()
                             ->send();
                        })->icon('heroicon-s-archive-box-arrow-down'),
                    Tables\Actions\Action::make('Un-Archive')->label(TranslationHelper::translateIfNeeded(' Un-Archive'))
                    ->hidden(fn ($record) => $record->status_visible == 'current')
                            ->action(function ($record) {
                             $record->update(['status_visible' => 'current']);
                                Notification::make()
                                ->title('Status Updated')
                                ->body("Status successfully changed.")
                                ->success()
                                ->send();
                        })->icon('heroicon-s-archive-box'),
                    //Shared action
                    Tables\Actions\Action::make('Shared')->label(TranslationHelper::translateIfNeeded('Shared'))
                        ->hidden(fn ($record) => 
                        ($record->shared == 1) ||
                        !(Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) && 
                        ($record->users_id != Auth()->user()->id))

                        ->action(function ($record) {
                            $record->update(['shared' => 1]);
                            Notification::make()
                            ->title('Shared Updated')
                            ->body("Shared successfully changed.")
                            ->success()
                            ->send();
                        })->icon('heroicon-m-share'),
                    //Un-Shared action
                    Tables\Actions\Action::make('Un-Shared')->label(TranslationHelper::translateIfNeeded('Un-Shared'))
                        ->hidden(fn ($record) => 
                        ($record->shared == 0) ||
                        !(Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user')))&&
                        ($record->users_id != Auth()->user()->id))
                        ->action(function ($record) {
                            $record->update(['shared' => 0]);
                            Notification::make()
                            ->title('Un-Shared Updated ')
                            ->body("Un-Shared successfully changed.")
                            ->success()
                            ->send();
                        })->icon('heroicon-m-share'),
                ])
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Section::make('')
                ->schema([
                    TextEntry::make('case')->label(TranslationHelper::translateIfNeeded('Case')),
                    TextEntry::make('flight_date')->label(TranslationHelper::translateIfNeeded('Last Flight Date'))
                        ->getStateUsing(function ($record) {
                            $lastFlight = $record->flighs()->orderBy('start_date_flight', 'desc')->first();
                            $totalFlights = $record->flighs()->count();
                            $lastFlightDate = optional($lastFlight)->start_date_flight ? $lastFlight->start_date_flight : '';
                            return "({$totalFlights}) Flights {$lastFlightDate}";
                        }),
                    TextEntry::make('revenue')->label(TranslationHelper::translateIfNeeded('Revenue')),
                    TextEntry::make('currencies.iso')->label(TranslationHelper::translateIfNeeded('Currency')),
                    TextEntry::make('customers.name')->label(TranslationHelper::translateIfNeeded('Customers'))
                        ->url(fn($record) => $record->customers_id ? route('filament.admin.resources.customers.index', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->customers_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('description')->label(TranslationHelper::translateIfNeeded('Description')),
                ])->columns(2),
            Section::make('')
                ->schema([
                    InfolistView::make('component.flight-project'),
                ])
        ]);        
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProjects::route('/create'),
            'edit' => Pages\EditProjects::route('/{record}/edit'),
            'view' => Pages\ViewProjects::route('/{record}'),
        ];
    }
}
