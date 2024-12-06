<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenceResource\Pages;
use App\Filament\Resources\MaintenceResource\RelationManagers;
use App\Models\currencie;
use App\Models\drone;
use App\Models\Maintence_drone;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;
use App\Helpers\TranslationHelper;

class MaintenceResource extends Resource
{
    protected static ?string $model = Maintence_drone::class;
    // protected static ?string $navigationLabel = 'Maintenance Drone';
    protected static ?string $tenantRelationshipName = 'maintence_drones';
    // protected static ?string $modelLabel = 'Maintenance Drone';
    // public static ?string $navigationGroup = 'Maintenance';

    protected static ?string $navigationIcon = 'heroicon-s-wrench-screwdriver';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 5;
    protected static bool $isLazy = false;
    
    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::where('status','!=','completed')->Where('teams_id',$teamID)->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Maintenance Drone');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Maintenance Drone');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        $cloneId = request()->query('clone');
        $defaultData = [];

        if ($cloneId) {
            $record = Maintence_drone::find($cloneId);
            if ($record) {
                $defaultData = $record->toArray();
                $defaultData['name'] = $record->name . ' - CLONE';
            }
        }
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(TranslationHelper::translateIfNeeded('Overview'))
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')
                        ->label(TranslationHelper::translateIfNeeded('Maintenance Description'))    
                            ->maxLength(255)
                            ->default($defaultData['name'] ?? null),
                        Forms\Components\Select::make('drone_id')
                            // ->relationship('drone','name', function (Builder $query){
                            //     $currentTeamId = auth()->user()->teams()->first()->id;
                            //     $query->where('teams_id', $currentTeamId);
                            // })
                            ->options(function (callable $get) use ($currentTeamId) {
                                return drone::where('teams_id', $currentTeamId)->pluck('name', 'id');
                            })
                            ->label(TranslationHelper::translateIfNeeded('Drone'))
                            ->searchable()
                            ->columnSpan(1)
                            ->default($defaultData['drone_id'] ?? null),
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
                            // ->reactive()
                            // ->afterStateUpdated(function (callable $set, callable $get) {
                            //     if ($get('status') === 'complete'){
                            //         $drone = drone::find($get('drone_id'));
                            //         if ($drone && $drone->status === 'maintenance'){
                            //             $drone->update(['status'=>'airworthy']);
                            //         }
                            //     }
                            // }),
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
                            // })
                            ->default(function (){
                                $cloneId = request()->query('clone');
                                if ($cloneId) {
                                    $clonedRecord = \App\Models\Maintence_drone::find($cloneId); 
                                    
                                    if ($clonedRecord && $clonedRecord->currencies) {
                                        return $clonedRecord->currencies_id;
                                    }
                                }
                                $currentTeam = auth()->user()->teams()->first();
                                return $currentTeam ? $currentTeam->currencies_id  : null;
                            }),
                        Forms\Components\TextArea::make('notes')
                        ->label(TranslationHelper::translateIfNeeded('Notes'))    
                            ->columnSpanFull()
                            ->default($defaultData['notes'] ?? null),
                    ])->columns(3),
                    //and wizard 1
                    Forms\Components\Wizard\Step::make(TranslationHelper::translateIfNeeded('Add Task (Optional)'))
                    ->schema([
                        Forms\Components\Select::make('part')
                        ->label(TranslationHelper::translateIfNeeded('Part #'))    
                            ->options([
                                'part 1'=> 'Part 1',
                                'part 2'=> 'Part 2',
                                'part 3'=> 'Part 3',
                            ])
                            ->default($defaultData['part'] ?? null),
                        Forms\Components\TextInput::make('part_name')
                        ->label(TranslationHelper::translateIfNeeded('Part Name'))    
                            ->maxLength(255)
                            ->default($defaultData['part_name'] ?? null),
                        Forms\Components\Select::make('status_part')
                        ->label(TranslationHelper::translateIfNeeded('Status Part'))    
                            ->options([
                                'partial'=> 'Partial',
                                'open'=> 'Open',
                                'done'=> 'Done',
                            ])
                            ->default($defaultData['status_part'] ?? null),
                        Forms\Components\TextInput::make('technician')
                        ->label(TranslationHelper::translateIfNeeded('Technician'))    
                            ->maxLength(255)
                            ->default($defaultData['technician'] ?? null),
                        Forms\Components\TextInput::make('new_part_serial')
                        ->label(TranslationHelper::translateIfNeeded('New Part Serial #'))    
                            ->maxLength(255)
                            ->default($defaultData['new_part_serial'] ?? null),
                        Forms\Components\Checkbox::make('replaced')
                        ->label(TranslationHelper::translateIfNeeded('Replaced'))
                        ->default($defaultData['replaced'] ?? null),
                        Forms\Components\Textarea::make('description_part')
                        ->label(TranslationHelper::translateIfNeeded('Description'))    
                            ->maxLength(255)->columnSpanFull()
                            ->default($defaultData['description_part'] ?? null),
                    ])->columns(2),
                    //and wizard 2
                ])->columnSpanFull(),
                //end wizard
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('drone.name')
                ->label(TranslationHelper::translateIfNeeded('Drone'))
                ->url(fn($record) =>$record->drone_id? route('filament.admin.resources.drones.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->drone_id,
                ]):null)->color(Color::Blue)
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
                                {$overdueLabel}: {$daysOverdueDiff} {$daysLabel}
                            </span>
                        </div>";
                            }
                        }
                        // return $daysOverdue->format('Y-m-d');
                        return $formatDate;
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('cost')
                ->label(TranslationHelper::translateIfNeeded('Cost'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('currencies.iso')
                ->label(TranslationHelper::translateIfNeeded('Currencies'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('technician')
                ->label(TranslationHelper::translateIfNeeded('Technician'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->label(TranslationHelper::translateIfNeeded('Status'))
                    // ->color(fn ($record) => match ($record->status){
                    //     'completed' => Color::Green,
                    //     'schedule' =>Color::Red,
                    //     'in_progress' => Color::Blue
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'schedule' => 'Schedule',
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
                Tables\Filters\SelectFilter::make('drone_id')
                ->label(TranslationHelper::translateIfNeeded('Filter by Drone'))
                ->options(
                    Drone::pluck('name', 'id')->toArray()
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
                    ->visible(function ($record) {
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
                            return route('filament.admin.resources.maintences.create', [
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
            Section::make(TranslationHelper::translateIfNeeded('Overview'))
                ->schema([
                    TextEntry::make('name')->label(TranslationHelper::translateIfNeeded('Name')),
                    TextEntry::make('drone.name')->label(TranslationHelper::translateIfNeeded('Drone'))
                        ->url(fn($record) => $record->drone_id ? route('filament.admin.resources.drones.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->drone_id,
                        ]) : null)
                        ->color(Color::Blue),
                    TextEntry::make('date')->label(TranslationHelper::translateIfNeeded('Date')),
                    TextEntry::make('status')->label(TranslationHelper::translateIfNeeded('Status')),
                    TextEntry::make('cost')->label(TranslationHelper::translateIfNeeded('Cost')),
                    TextEntry::make('currencies.iso')->label(TranslationHelper::translateIfNeeded('Currency')),
                    TextEntry::make('notes')->label(TranslationHelper::translateIfNeeded('Notes')),
                ])->columns(4),
        
            Section::make(TranslationHelper::translateIfNeeded('Add Tasks (Optional)'))
                ->schema([
                    TextEntry::make('part')->label(TranslationHelper::translateIfNeeded('Part')),
                    TextEntry::make('part_name')->label(TranslationHelper::translateIfNeeded('Part Name')),
                    TextEntry::make('status_part')->label(TranslationHelper::translateIfNeeded('Status Part')),
                    TextEntry::make('technician')->label(TranslationHelper::translateIfNeeded('Technician')),
                    IconEntry::make('replaced')->boolean()->label(TranslationHelper::translateIfNeeded('Replaced')),
                    TextEntry::make('new_part_serial')->label(TranslationHelper::translateIfNeeded('New Part Serial')),
                    TextEntry::make('description_part')->label(TranslationHelper::translateIfNeeded('Description Part')),
                ])->columns(4),
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
            'index' => Pages\ListMaintences::route('/'),
            'create' => Pages\CreateMaintence::route('/create'),
            'edit' => Pages\EditMaintence::route('/{record}/edit'),
        ];
    }
}
