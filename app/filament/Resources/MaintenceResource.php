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
use Stichoza\GoogleTranslate\GoogleTranslate;

class MaintenceResource extends Resource
{
    protected static ?string $model = Maintence_drone::class;
    // protected static ?string $navigationLabel = 'Maintenance Drone';
    protected static ?string $tenantRelationshipName = 'maintence_drones';
    // protected static ?string $modelLabel = 'Maintenance Drone';
    public static ?string $navigationGroup = 'Maintenance';

    protected static ?string $navigationIcon = 'heroicon-s-wrench-screwdriver';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 5;
    protected static bool $isLazy = false;
    
    public static function getNavigationBadge(): ?string{
        return static::getModel()::where('status','!=','completed')->count();
    }

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Maintenance Drone', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Maintenance Drone', session('locale') ?? 'en');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(GoogleTranslate::trans('Overview', session('locale') ?? 'en'))
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')
                            ->label(GoogleTranslate::trans('Maintenance Description', session('locale') ?? 'en'))
                            ->maxLength(255),
                        Forms\Components\Select::make('drone_id')
                            // ->relationship('drone','name', function (Builder $query){
                            //     $currentTeamId = auth()->user()->teams()->first()->id;
                            //     $query->where('teams_id', $currentTeamId);
                            // })
                            ->options(function (callable $get) use ($currentTeamId) {
                                return drone::where('teams_id', $currentTeamId)->pluck('name', 'id');
                            })
                            ->label(GoogleTranslate::trans('Drone', session('locale') ?? 'en'))
                            ->searchable()
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('date')
                            ->label(GoogleTranslate::trans('Maintenance Date', session('locale') ?? 'en'))   
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                            ->options([
                                'schedule'=> 'Schedule',
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
                            ->label(GoogleTranslate::trans('Expense Cost', session('locale') ?? 'en')),
                        Forms\Components\Select::make('currencies_id')
                        ->options(currencie::all()->mapWithKeys(function ($currency) {
                            return [$currency->id => "{$currency->name} - {$currency->iso}"];}))
                            ->searchable()
                            ->label(GoogleTranslate::trans('Currency', session('locale') ?? 'en'))
                            ->default(function (){
                                $currentTeam = auth()->user()->teams()->first();
                                return $currentTeam ? $currentTeam->currencies_id : null;
                            }),
                        Forms\Components\TextArea::make('notes')
                            ->label(GoogleTranslate::trans('Notes', session('locale') ?? 'en'))
                            ->columnSpanFull(),
                    ])->columns(3),
                    //and wizard 1
                    Forms\Components\Wizard\Step::make(GoogleTranslate::trans('Add Tasks (Optional)', session('locale') ?? 'en'))
                    ->schema([
                        Forms\Components\Select::make('part')
                            ->label(GoogleTranslate::trans('Part #', session('locale') ?? 'en'))
                            ->options([
                                'part 1'=> 'Part 1',
                                'part 2'=> 'Part 2',
                                'part 3'=> 'Part 3',
                            ]),
                        Forms\Components\TextInput::make('part_name')
                            ->label(GoogleTranslate::trans('Part Name', session('locale') ?? 'en'))
                            ->maxLength(255),
                        Forms\Components\Select::make('status_part')
                            ->label(GoogleTranslate::trans('Status Part', session('locale') ?? 'en'))
                            ->options([
                                'partial'=> 'Partial',
                                'open'=> 'Open',
                                'done'=> 'Done',
                            ]),
                        Forms\Components\TextInput::make('technician')
                            ->label(GoogleTranslate::trans('Technician', session('locale') ?? 'en'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('new_part_serial')
                            ->label(GoogleTranslate::trans('New Part Serial #', session('locale') ?? 'en'))
                            ->maxLength(255),
                        Forms\Components\Checkbox::make('replaced')->label(GoogleTranslate::trans('Replaced', session('locale') ?? 'en')),
                        Forms\Components\Textarea::make('description_part')
                            ->label(GoogleTranslate::trans('Description', session('locale') ?? 'en'))
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
                Tables\Columns\TextColumn::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('drone.name')
                ->label(GoogleTranslate::trans('Drone', session('locale') ?? 'en'))
                ->url(fn($record) =>$record->drone_id? route('filament.admin.resources.drones.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->drone_id,
                ]):null)->color(Color::Blue)
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(GoogleTranslate::trans('Date', session('locale') ?? 'en'))
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
                Tables\Columns\TextColumn::make('cost')->label(GoogleTranslate::trans('Cost', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('currencies.iso')->label(GoogleTranslate::trans('Currencies', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('technician')->label(GoogleTranslate::trans('Technician', session('locale') ?? 'en'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                    ->color(fn ($record) => match ($record->status){
                        'completed' => Color::Green,
                        'schedule' =>Color::Red,
                        'in_progress' => Color::Blue
                    })
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'schedule' => 'Schedule',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed'
                ])
                ->label('Filter by Status'),
                Filter::make('Overdue')
                ->query(function ($query) {
                    $query->where('status', '!=', 'completed')
                          ->whereDate('date', '<', Carbon::now());
                }),
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
                    ->button()
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
                    TextEntry::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en')),
                    TextEntry::make('drone.name')->label(GoogleTranslate::trans('Drone', session('locale') ?? 'en'))
                    ->url(fn($record) =>$record->drone_id? route('filament.admin.resources.drones.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->drone_id,
                    ]):null)->color(Color::Blue),
                    TextEntry::make('date')->label(GoogleTranslate::trans('Date', session('locale') ?? 'en')),
                    TextEntry::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en')),
                    TextEntry::make('cost')->label(GoogleTranslate::trans('Cost', session('locale') ?? 'en')),
                    TextEntry::make('currencies.iso')->label(GoogleTranslate::trans('Currency', session('locale') ?? 'en')),
                    TextEntry::make('notes')->label(GoogleTranslate::trans('Notes', session('locale') ?? 'en'))
                ])->columns(4),
            Section::make('Add Tasks (Optional)')
                ->schema([
                    TextEntry::make('part')->label(GoogleTranslate::trans('Part', session('locale') ?? 'en')),
                    TextEntry::make('part_name')->label(GoogleTranslate::trans('Part Name', session('locale') ?? 'en')),
                    TextEntry::make('status_part')->label(GoogleTranslate::trans('Status Part', session('locale') ?? 'en')),
                    TextEntry::make('technician')->label(GoogleTranslate::trans('Technician', session('locale') ?? 'en')),
                    IconEntry::make('replaced')->boolean()->label(GoogleTranslate::trans('Replaced', session('locale') ?? 'en')),
                    TextEntry::make('new_part_serial')->label(GoogleTranslate::trans('New Part Serial', session('locale') ?? 'en')),
                    TextEntry::make('description_part')->label(GoogleTranslate::trans('Description Part', session('locale') ?? 'en'))
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
