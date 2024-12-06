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
                            ->options(function (callable $get) use ($currentTeamId) {
                                return equidment::where('teams_id', $currentTeamId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->columnSpan(1)
                            ->default($defaultData['equidment_id'] ?? null),
                            Forms\Components\Select::make('battrei_id')
                            ->label(TranslationHelper::translateIfNeeded('Battery'))
                            ->options(function (callable $get) use ($currentTeamId) {
                                return battrei::where('teams_id', $currentTeamId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->columnSpan(1)
                            ->default($defaultData['battrei_id'] ?? null),
                        Forms\Components\DatePicker::make('date')
                        ->label(TranslationHelper::translateIfNeeded('Maintenance Date'))      
                            ->columnSpan(1)
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
                            ->columnSpan(2)
                            ->default($defaultData['technician'] ?? null),

                        Forms\Components\TextArea::make('notes')
                        ->label(TranslationHelper::translateIfNeeded('Notes'))    
                            ->columnSpanFull()
                            ->default($defaultData['notes'] ?? null),
                ])->columns(3),
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
                ->url(fn($record) => $record->equidment_id ? route('filament.admin.resources.equidments.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->equidment_id,
                ]) : null)
                ->color(Color::Blue)
                ->searchable(),
            Tables\Columns\TextColumn::make('battrei.name')
                ->label(TranslationHelper::translateIfNeeded('Battery'))
                ->url(fn($record) => $record->battrei_id ? route('filament.admin.resources.battreis.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->battrei_id,
                ]) : null)
                ->color(Color::Blue)
                ->searchable(),
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
                ->options(
                    battrei::pluck('name', 'id')->toArray()
                )
                ->searchable(),
                Tables\Filters\SelectFilter::make('equidment_id')
                ->label(TranslationHelper::translateIfNeeded('Filter by Equipment'))
                ->options(
                    equidment::pluck('name', 'id')->toArray()
                )
                ->searchable(),
            ])
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
                ->url(fn($record) => $record->equidment_id ? route('filament.admin.resources.equidments.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->equidment_id,
                ]) : null)
                ->color(Color::Blue),
            TextEntry::make('battrei.name')
                ->label(TranslationHelper::translateIfNeeded('Battery'))
                ->url(fn($record) => $record->battrei_id ? route('filament.admin.resources.battreis.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->battrei_id,
                ]) : null)
                ->color(Color::Blue),
            TextEntry::make('date')
                ->label(TranslationHelper::translateIfNeeded('Date')),
            TextEntry::make('status')
                ->label(TranslationHelper::translateIfNeeded('Status')),
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
