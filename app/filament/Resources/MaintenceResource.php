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
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;

class MaintenceResource extends Resource
{
    protected static ?string $model = Maintence_drone::class;
    protected static ?string $navigationLabel = 'Maintenance Drone';
    protected static ?string $tenantRelationshipName = 'maintence_drones';
    protected static ?string $modelLabel = 'Maintenance Drone';
    public static ?string $navigationGroup = 'Maintenance';

    protected static ?string $navigationIcon = 'heroicon-s-wrench-screwdriver';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 5;
    protected static bool $isLazy = false;

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Overview')
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')
                            ->label('Maintenance Description')
                            ->maxLength(255),
                        Forms\Components\Select::make('drone_id')
                            // ->relationship('drone','name', function (Builder $query){
                            //     $currentTeamId = auth()->user()->teams()->first()->id;
                            //     $query->where('teams_id', $currentTeamId);
                            // })
                            ->options(function (callable $get) use ($currentTeamId) {
                                return drone::where('teams_id', $currentTeamId)->pluck('name', 'id');
                            })
                            ->label('Drone')
                            ->searchable()
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('date')
                            ->label('Maintenance Date')   
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Schedule'=> 'Schedule',
                                'in_progress'=> 'In Progress',
                                'completed'=> 'Completed',
                            ]),
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
                            ->label('Expense Cost'),
                        Forms\Components\Select::make('currencies_id')
                        ->options(currencie::all()->mapWithKeys(function ($currency) {
                            return [$currency->id => "{$currency->name} - {$currency->iso}"];}))
                            ->searchable()
                            ->label('Currency'),
                        Forms\Components\TextArea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ])->columns(3),
                    //and wizard 1
                    Forms\Components\Wizard\Step::make('Add Tasks (Optional)')
                    ->schema([
                        Forms\Components\Select::make('part')
                            ->label('Part #')
                            ->options([
                                'part 1'=> 'Part 1',
                                'part 2'=> 'Part 2',
                                'part 3'=> 'Part 3',
                            ]),
                        Forms\Components\TextInput::make('part_name')
                            ->label('Part Name')
                            ->maxLength(255),
                        Forms\Components\Select::make('status_part')
                            ->label('Status Part')
                            ->options([
                                'partial'=> 'Partial',
                                'open'=> 'Open',
                                'done'=> 'Done',
                            ]),
                        Forms\Components\TextInput::make('technician')
                            ->label('Technician')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('new_part_serial')
                            ->label('New Part Serial #')
                            ->maxLength(255),
                        Forms\Components\Checkbox::make('replaced')->label('Replaced'),
                        Forms\Components\Textarea::make('description_part')
                        ->label('Description')
                            ->maxLength(255)->columnSpanFull(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('drone.name')
                ->url(fn($record) =>$record->drone_id? route('filament.admin.resources.drones.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->drone_id,
                ]):null)->color(Color::Blue)
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
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
                                return "<div>{$formatDate}<br><span style='
                                display: inline-block;
                                background-color: red; 
                                color: white; 
                                padding: 3px 6px;
                                border-radius: 5px;
                                font-weight: bold;
                            '>
                                Overdue: {$daysOverdueDiff} days
                            </span>
                        </div>";
                            }
                        }
                        // return $daysOverdue->format('Y-m-d');
                        return $formatDate;
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('cost')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currencies.iso')
                    ->searchable(),
                Tables\Columns\TextColumn::make('technician')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->color(fn ($record) => match ($record->status){
                        'completed' => Color::Green,
                        'Schedule' =>Color::Red,
                        'in_progress' => Color::Zinc
                    })
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'Schedule' => 'Schedule',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed'
                ])
                ->label('Filter by Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record){
                        $record->status = 'completed';
                        $record->save();
                        Notification::make()
                            ->title('Task Resolved')
                            ->body('The task has been successfully resolved.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(function ($record) {
                        return $record->status !== 'completed' && auth()->user()->hasRole(['maintenance', 'panel_user']);
                    })
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
            Section::make('Overview')
                ->schema([
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('drone.name')->label('Drone')
                    ->url(fn($record) =>$record->drone_id? route('filament.admin.resources.drones.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->drone_id,
                    ]):null)->color(Color::Blue),
                    TextEntry::make('date')->label('Date'),
                    TextEntry::make('status')->label('Status'),
                    TextEntry::make('cost')->label('Cost'),
                    TextEntry::make('currencies.iso')->label('Currency'),
                    TextEntry::make('notes')->label('Notes')
                ])->columns(4),
            Section::make('Add Tasks (Optional)')
                ->schema([
                    TextEntry::make('part')->label('Part'),
                    TextEntry::make('part_name')->label('Part Name'),
                    TextEntry::make('status_part')->label('Status Part'),
                    TextEntry::make('technician')->label('Technician'),
                    IconEntry::make('replaced')->boolean()->label('Replaced'),
                    TextEntry::make('new_part_serial')->label('New Part Serial'),
                    TextEntry::make('description_part')->label('Description Part')
                ])->columns(4)
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
