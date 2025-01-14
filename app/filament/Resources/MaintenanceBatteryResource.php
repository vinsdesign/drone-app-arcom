<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceBatteryResource\Pages;
use App\Filament\Resources\MaintenanceBatteryResource\RelationManagers;
use App\Models\currencie;
use App\Models\battrei;
use App\Models\equidment;
use App\Models\maintence_eq;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;
use App\Helpers\TranslationHelper;
use Filament\Support\Enums\MaxWidth;


class MaintenanceBatteryResource extends Resource
{
    protected static ?string $model = maintence_eq::class;

    // protected static ?string $navigationLabel = 'Maintenance Equipment/Battery';
    protected static ?string $tenantRelationshipName = 'maintence_eqs';
    // protected static ?string $modelLabel = 'Maintenance Equipment/Battery';

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 6;
    // public static ?string $navigationGroup = 'Maintenance';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::where('status','!=','completed')->Where('teams_id',$teamID)->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Maintenance Equipment/Battery');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Maintenance Equipment/Battery');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        $cloneId = request()->query('clone');
        $defaultData = [];

        if ($cloneId) {
            $record = maintence_eq::find($cloneId);
            if ($record) {
                $defaultData = $record->toArray();
                $defaultData['name'] = $record->name . ' - CLONE';
            }
        }
        return $form
            ->schema([
                Forms\Components\Section::make(TranslationHelper::translateIfNeeded('Maintenance Equipment/Battery Overview'))
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')
                        ->label(TranslationHelper::translateIfNeeded('Maintenance Description'))    
                            ->maxLength(255)
                            ->default($defaultData['name'] ?? null),
                        Forms\Components\Select::make('equidment_id')
                        ->label(TranslationHelper::translateIfNeeded('Equipment'))  
                            ->relationship('equidment', 'name')  
                            ->options(equidment::where('teams_id', auth()->user()->teams()->first()->id)
                                ->where('shared', '!=', 0)
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->columnSpan(['sm' => 3 , 'md' => 1 , 'lg' => 1 , 'xl' => 1 , '2xl' => 1])
                            ->default($defaultData['equidment_id'] ?? null),
                            Forms\Components\Select::make('battrei_id')
                            ->label(TranslationHelper::translateIfNeeded('Battery'))
                            ->relationship('battrei', 'name')
                            ->options(battrei::where('teams_id', auth()->user()->teams()->first()->id)
                                ->where('shared', '!=', 0)
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->columnSpan(['sm' => 3 , 'md' => 1 , 'lg' => 1 , 'xl' => 1 , '2xl' => 1])
                            ->default($defaultData['battrei_id'] ?? null),
                        Forms\Components\DatePicker::make('date')
                        ->label(TranslationHelper::translateIfNeeded('Maintenance Date'))      
                        ->columnSpan(['sm' => 3 , 'md' => 1 , 'lg' => 1 , 'xl' => 1 , '2xl' => 1])
                            ->default($defaultData['date'] ?? null),
                        Forms\Components\Select::make('status')
                        ->label(TranslationHelper::translateIfNeeded('Status'))    
                            ->options([
                                'schedule'=> 'Schedule',
                                'in_progress'=> 'In Progress',
                                'completed'=> 'Completed',
                            ])
                            ->default($defaultData['status'] ?? null),
                        Forms\Components\TextInput::make('cost')
                        ->label(TranslationHelper::translateIfNeeded('Expense Cost'))
                        ->default($defaultData['cost'] ?? null),   
                        Forms\Components\Select::make('currencies_id')
                        ->options(currencie::all()->mapWithKeys(function ($currency) {
                            return [$currency->id => "{$currency->name} - {$currency->iso}"];}))
                            ->searchable()
                            ->label(TranslationHelper::translateIfNeeded('Currency'))
                            // ->default(function (){
                            //     $currentTeam = auth()->user()->teams()->first();
                            //     return $currentTeam ? $currentTeam->currencies_id : null;
                            // }),
                            ->default(function (){
                                $cloneId = request()->query('clone');
                                if ($cloneId) {
                                    $clonedRecord = \App\Models\maintence_eq::find($cloneId); 
                                    
                                    if ($clonedRecord && $clonedRecord->currencies) {
                                        return $clonedRecord->currencies_id;
                                    }
                                }
                                $currentTeam = auth()->user()->teams()->first();
                                return $currentTeam ? $currentTeam->currencies_id  : null;
                            }),
                        Forms\Components\TextInput::make('technician')
                        ->label(TranslationHelper::translateIfNeeded('Technician'))    
                            ->maxLength(255)
                            ->columnSpan(['sm' => 3 , 'md' => 1 , 'lg' => 2 , 'xl' => 2 , '2xl' => 2])
                            ->default($defaultData['technician'] ?? null),

                        Forms\Components\TextArea::make('notes')
                        ->label(TranslationHelper::translateIfNeeded('Notes'))    
                            ->columnSpanFull()
                            ->default($defaultData['notes'] ?? null),
                ])->columns(['sm' => 1 , 'md' => 1 , 'lg' => 3]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name'))
                ->searchable(),
            Tables\Columns\TextColumn::make('equidment.name')
                ->label(TranslationHelper::translateIfNeeded('Equipment'))
                // ->url(fn($record) => $record->equidment_id ? route('filament.admin.resources.equidments.view', [
                //     'tenant' => Auth()->user()->teams()->first()->id,
                //     'record' => $record->equidment_id,
                // ]) : null)
                // ->color(Color::Blue)
                ->url(function ($record) {
                    if ($record->equidment && $record->equidment->shared !== 0) {
                        return route('filament.admin.resources.equidments.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->equidment_id,
                        ]);
                    }
                    return null;
                })
                ->color(fn($record) => $record->equidment && $record->equidment->shared !== 0 ? Color::Blue : Color::Gray)
                ->searchable()
                ->placeholder(TranslationHelper::translateIfNeeded('No Equipment selected')),
            Tables\Columns\TextColumn::make('battrei.name')
                ->label(TranslationHelper::translateIfNeeded('Battery'))
                // ->url(fn($record) => $record->battrei_id ? route('filament.admin.resources.battreis.view', [
                //     'tenant' => Auth()->user()->teams()->first()->id,
                //     'record' => $record->battrei_id,
                // ]) : null)
                // ->color(Color::Blue)
                ->url(function ($record) {
                    if ($record->battrei && $record->battrei->shared !== 0) {
                        return route('filament.admin.resources.battreis.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->battrei_id,
                        ]);
                    }
                    return null;
                })
                ->color(fn($record) => $record->battrei && $record->battrei->shared !== 0 ? Color::Blue : Color::Gray)
                ->searchable()
                ->placeholder(TranslationHelper::translateIfNeeded('No Battery selected')),
            Tables\Columns\TextColumn::make('date')
                ->label(TranslationHelper::translateIfNeeded('Date'))
                ->date()
                ->searchable()
                ->formatStateUsing(function ($state, $record) {
                    $daysOverdue = Carbon::parse($state);
                    $now = Carbon::now();
                    $formatDate = $daysOverdue->format('Y-m-d');
        
                    if ($record->status !== 'completed') {
                        $daysOverdueDiff = $now->diffInDays($daysOverdue, false);
        
                        if ($daysOverdueDiff < 0){
                            $daysOverdueDiff = abs(intval($daysOverdueDiff));

                            $overdueLabel = TranslationHelper::translateIfNeeded('Overdue');
                            $daysLabel = TranslationHelper::translateIfNeeded('days');

                            return "<div>{$formatDate}<br><span style='
                                display: inline-block;
                                background-color: red; 
                                color: white; 
                                padding: 3px 6px;
                                border-radius: 5px;
                                font-weight: bold;
                            '>
                                {$overdueLabel} {$daysOverdueDiff} {$daysLabel}
                            </span></div>";
                        }
                    }
                    return $formatDate;
                })
                ->html(),
            Tables\Columns\TextColumn::make('status')
                ->label(TranslationHelper::translateIfNeeded('Status'))
                // ->color(fn ($record) => match ($record->status) {
                //     'completed' => Color::Green,
                //     'schedule' => Color::Red,
                //     'in_progress' => Color::Blue,
                // })
                ->formatStateUsing(function ($state) {
                    $colors = [
                        'completed' => '#28a745',
                        'schedule' => 'red',
                        'in_progress' => 'gray',
                    ];
            
                    $color = $colors[$state] ?? 'gray';
            
                    return "<span style='
                            display: inline-block;
                            width: 10px;
                            height: 10px;
                            background-color: {$color};
                            border-radius: 50%;
                            margin-right: 5px;
                        '></span><span style='color: {$color};'>{$state}</span>";
                })
                ->html()
                ->searchable()
                ->badge(),
            Tables\Columns\TextColumn::make('cost')
                ->label(TranslationHelper::translateIfNeeded('Cost'))
                ->searchable(),
            Tables\Columns\TextColumn::make('currencies.iso')
                ->label(TranslationHelper::translateIfNeeded('Currencies'))
                ->searchable(),
            Tables\Columns\TextColumn::make('technician')
                ->label(TranslationHelper::translateIfNeeded('Technician'))
                ->sortable(),
        ])        
        
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'Schedule' => 'Schedule',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed'
                ])
                ->label(TranslationHelper::translateIfNeeded('Filter by Status')),
                Filter::make('Overdue')
                ->label(TranslationHelper::translateIfNeeded('Overdue'))
                ->query(function ($query) {
                    $query->where('status', '!=', 'completed')
                          ->whereDate('date', '<', Carbon::now());
                }),
                Tables\Filters\SelectFilter::make('battrei_id')
                ->label(TranslationHelper::translateIfNeeded('Filter by Battery'))
                ->relationship('battrei', 'name', function (Builder $query){
                    $currentTeamId = auth()->user()->teams()->first()->id;;
                    $query->where('teams_id', $currentTeamId);
                })
                ->preload()
                ->searchable(),
                Tables\Filters\SelectFilter::make('equidment_id')
                ->label(TranslationHelper::translateIfNeeded('Filter by Equipment'))
                ->relationship('equidment', 'name', function (Builder $query){
                    $currentTeamId = auth()->user()->teams()->first()->id;;
                    $query->where('teams_id', $currentTeamId);
                })
                ->preload()
                ->searchable(),
            ])
            ->filtersFormColumns(2)
            ->filtersFormWidth(MaxWidth::Medium)
            ->actions([
                Tables\Actions\Action::make('resolve')
                    ->label(TranslationHelper::translateIfNeeded('Resolve'))
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record){
                        $record->status = 'completed';
                        $record->save();
                        Notification::make()
                            ->title(TranslationHelper::translateIfNeeded('Task Resolved'))
                            ->body(TranslationHelper::translateIfNeeded('The task has been successfully resolved.'))
                            ->send();
                    })
                    ->button()
                    ->requiresConfirmation()
                    ->visible(function ($record){
                        return $record->status !== 'completed' && auth()->user()->hasRole(['maintenance', 'panel_user']);
                    }),
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\DeleteAction::make(),
                        Tables\Actions\Action::make('clone')
                        ->label('Clone')
                        ->icon('heroicon-s-document-duplicate')
                        ->url(function ($record) {
                            return route('filament.admin.resources.maintenance-batteries.create', [
                                'tenant' => Auth()->user()->teams()->first()->id,
                                'clone' => $record->id,
                            ]);
                        }),
                    ]),
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
            TextEntry::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name')),
            TextEntry::make('equidment.name')
                ->label(TranslationHelper::translateIfNeeded('Equipment'))
                // ->url(fn($record) => $record->equidment_id ? route('filament.admin.resources.equidments.view', [
                //     'tenant' => Auth()->user()->teams()->first()->id,
                //     'record' => $record->equidment_id,
                // ]) : null)
                // ->color(Color::Blue),
                ->url(function ($record) {
                    if ($record->equidment && $record->equidment->shared !== 0) {
                        return route('filament.admin.resources.equidments.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->equidment_id,
                        ]);
                    }
                    return null;
                })
                ->color(fn($record) => $record->equidment && $record->equidment->shared !== 0 ? Color::Blue : Color::Gray),
            TextEntry::make('battrei.name')
                ->label(TranslationHelper::translateIfNeeded('Battery'))
                // ->url(fn($record) => $record->battrei_id ? route('filament.admin.resources.battreis.view', [
                //     'tenant' => Auth()->user()->teams()->first()->id,
                //     'record' => $record->battrei_id,
                // ]) : null)
                // ->color(Color::Blue),
                ->url(function ($record) {
                    if ($record->battrei && $record->battrei->shared !== 0) {
                        return route('filament.admin.resources.battreis.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->battrei_id,
                        ]);
                    }
                    return null;
                })
                ->color(fn($record) => $record->battrei && $record->battrei->shared !== 0 ? Color::Blue : Color::Gray),
            TextEntry::make('date')
                ->label(TranslationHelper::translateIfNeeded('Date')),
            TextEntry::make('status')
                ->label(TranslationHelper::translateIfNeeded('Status'))
                ->color(fn ($record) => match ($record->status) {
                    'completed' => Color::Green,
                    'schedule' => Color::Red,
                    'in_progress' => Color::Zinc
                }),
            TextEntry::make('cost')
                ->label(TranslationHelper::translateIfNeeded('Cost')),
            TextEntry::make('currencies.iso')
                ->label(TranslationHelper::translateIfNeeded('Currency')),
            TextEntry::make('technician')
                ->label(TranslationHelper::translateIfNeeded('Technician')),
            TextEntry::make('notes')
                ->label(TranslationHelper::translateIfNeeded('Notes')),
        ])
        ->columns(3);        
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
            'index' => Pages\ListMaintenanceBatteries::route('/'),
            'create' => Pages\CreateMaintenanceBattery::route('/create'),
            'edit' => Pages\EditMaintenanceBattery::route('/{record}/edit'),
        ];
    }
}
