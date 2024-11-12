<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BattreiResource\Pages;
use App\Filament\Resources\BattreiResource\RelationManagers;
use App\Models\Battrei;
use App\Models\drone;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Carbon\Carbon;
use Stichoza\GoogleTranslate\GoogleTranslate;


class BattreiResource extends Resource
{
    protected static ?string $model = Battrei::class;
    protected static ?string $navigationGroup = 'Inventory';
    // protected static ?string $navigationLabel = 'Batteries';
    // protected static ?string $modelLabel = 'Batteries';
    protected static ?string $navigationIcon = 'heroicon-s-battery-100';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static bool $isLazy = false;
    
    public static function getNavigationBadge(): ?string{
        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Batteries', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Batteries', session('locale') ?? 'en');
    }
    // public static function getNavigationGroup(): string
    // {
    //     return GoogleTranslate::trans('Inventory', session('locale') ?? 'en');
    // }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make(GoogleTranslate::trans('Overview', session('locale') ?? 'en'))
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                            ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')->label(GoogleTranslate::trans('Model', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                            ->options([
                                'airworthy' => 'Airworthy',
                                'maintenance' => 'Maintenance',
                                'retired' => 'Retired'
                            ])
                            ->required(),
                        Forms\Components\Select::make('asset_inventory')->label(GoogleTranslate::trans('Inventory / Asset', session('locale') ?? 'en'))
                            ->options([
                                'asset' => 'Asset',
                                'inventory' => 'Inventory',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('serial_P')->label(GoogleTranslate::trans('Serial #(Printed)', session('locale') ?? 'en'))
                            ->required()->columnSpan(2),
                        Forms\Components\TextInput::make('serial_I')->label(GoogleTranslate::trans('Serial #(Internal)', session('locale') ?? 'en'))
                            ->required()->columnSpan(2),
                        Forms\Components\BelongsToSelect::make('for_drone')->label(GoogleTranslate::trans('For Drone (Optional)', session('locale') ?? 'en'))

                            // ->relationship('drone', 'name', function (Builder $query){
                            //     $currentTeamId = auth()->user()->teams()->first()->id;
                            //     $query->where('teams_id', $currentTeamId);
                            // })


                            ->searchable()
                            ->options(function (callable $get) use ($currentTeamId) {
                                return drone::where('teams_id', $currentTeamId)->pluck('name', 'id');
                            })
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('cellCount')->label(GoogleTranslate::trans('Cell Count', session('locale') ?? 'en'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('nominal_voltage')->label(GoogleTranslate::trans('Nominal Voltage (V)', session('locale') ?? 'en'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('capacity')->label(GoogleTranslate::trans('Capacity (mAh)', session('locale') ?? 'en'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('initial_Cycle_count')->label(GoogleTranslate::trans('Initial Cycle Count', session('locale') ?? 'en'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('life_span')->label(GoogleTranslate::trans('Life Span', session('locale') ?? 'en'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('flaight_count')->label(GoogleTranslate::trans('Flight Count', session('locale') ?? 'en'))
                            ->required()
                            ->numeric()->columnSpan(1),
                        ])->columns(4),
                        //end wizard 1
                    Tabs\Tab::make(GoogleTranslate::trans('Extra Information', session('locale') ?? 'en'))
                    ->schema([
                        Forms\Components\Select::make('users_id')->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en'))
                            //->relationship('users', 'name')
                            ->options(function () {
                                $currentTeamId = auth()->user()->teams()->first()->id; 
                        
                                return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                    $query->where('team_user.team_id', $currentTeamId); 
                                })->pluck('name', 'id'); 
                            }) 
                            ->required(),
                        Forms\Components\DatePicker::make('purchase_date')->label(GoogleTranslate::trans('Purchase Date', session('locale') ?? 'en'))
                            ->required(),
                        Forms\Components\TextInput::make('insurable_value')->label(GoogleTranslate::trans('Insurable Value', session('locale') ?? 'en'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('wight')->label(GoogleTranslate::trans('Weight', session('locale') ?? 'en'))
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('firmware_version')->label(GoogleTranslate::trans('Firmware Version', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hardware_version')->label(GoogleTranslate::trans('Hardware Version', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_loaner')->label(GoogleTranslate::trans('Loaner Battery', session('locale') ?? 'en'))
                            ->required(),
                        Forms\Components\TextInput::make('description')->label(GoogleTranslate::trans('Description', session('locale') ?? 'en'))
                            ->required()
                            ->maxLength(255)->columnSpanFull(),

                        ])->columns(3),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')->label(GoogleTranslate::trans('Model', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                    ->color(fn ($record) => match ($record->status){
                        'airworthy' => Color::Green,
                       'maintenance' =>Color::Red,
                       'retired' => Color::Zinc
                     })
                    ->searchable(),
                Tables\Columns\TextColumn::make('flight_time')
                    ->label(GoogleTranslate::trans('Flights & Flying Time', session('locale') ?? 'en'))
                    ->getStateUsing(function ($record) {
                       $flights = $record->fligh()
                           ->whereHas('teams', function ($query) {
                               $query->where('teams.id', auth()->user()->teams()->first()->id);
                           })
                           ->get()
                           ->merge(
                               $record->kits()->with(['fligh' => function ($query) {
                                   $query->whereHas('teams', function ($query) {
                                       $query->where('teams.id', auth()->user()->teams()->first()->id);
                                   });
                               }])->get()->pluck('fligh')->flatten()
                           );
                   
                       $totalSeconds = 0;
                       foreach ($flights as $flight) {
                           $start = $flight->start_date_flight;
                           $end = $flight->end_date_flight;
                           if ($start && $end) {
                               $totalSeconds += Carbon::parse($start)->diffInSeconds(Carbon::parse($end));
                           }
                       }
                   
                       $hours = floor($totalSeconds / 3600);
                       $minutes = floor(($totalSeconds % 3600) / 60);
                       $seconds = $totalSeconds % 60;
                       $totalDuration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                       $totalFlights = $flights->unique('id')->count();
                    return "<div> {$totalFlights} Flight(s) <div style='border: 1px solid #ccc; padding: 3px; display: inline-block; border-radius: 5px; background-color: #D4D4D4;'>
                            <strong>{$totalDuration}</strong></div>";
                   })
                    ->sortable()
                    ->html(),
                // Tables\Columns\TextColumn::make('asset_inventory')->label('Inventory/Asset')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('serial_P')->label('Serial Printed')
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('serial_I')->label('Serial Internal')
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('cellCount')->label('Cell Count')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('nominal_voltage')->label('Voltage')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('capacity')->label('Capacity')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('initial_Cycle_count')->label('Initial Cycles Count')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('life_span')->label(GoogleTranslate::trans('Life Span', session('locale') ?? 'en'))
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('flaight_count')->label('Flaight Count')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('drone.name')->label(GoogleTranslate::trans('For Drone', session('locale') ?? 'en'))
                    ->numeric()->url(fn($record) => $record->for_drone ? route('filament.admin.resources.drones.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->for_drone,
                    ]): null)->color(Color::Blue)
                    ->sortable(),
                // Tables\Columns\TextColumn::make('purchase_date')->label('Purchase Date')
                //     ->date()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('insurable_value')->label('Insurable Value')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('wight')->label('weight')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('firmware_version')->label('Firmware Version')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('hardware_version')->label('Hardware Version')
                //     ->searchable(),
                // Tables\Columns\IconColumn::make('is_loaner')->label('Is Loaner')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('description')->label('Description')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('users.name')->label(GoogleTranslate::trans('Owners', session('locale') ?? 'en'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(GoogleTranslate::trans('Created at', session('locale') ?? 'en'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(GoogleTranslate::trans('Updated at', session('locale') ?? 'en'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'airworthy' => 'Airworthy',
                   'maintenance' => 'Maintenance',
                   'retired' => 'Retired'
                ])
                ->label('Filter by Status'),
            ])
            ->actions([
                Tables\Actions\Action::make('showBattrey')
                ->url(fn ($record) => route('battery.statistik', ['battery_id' => $record->id]))->label('View')
                ->icon('heroicon-s-eye'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
//infolist battery
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
    
    ->schema([
        Section::make('Overview')
                ->schema([
        TextEntry::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en')),
        TextEntry::make('model')->label(GoogleTranslate::trans('Model', session('locale') ?? 'en')),
        TextEntry::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
        ->color(fn ($record) => match ($record->status){
            'airworthy' => Color::Green,
           'maintenance' =>Color::Red,
           'retired' => Color::Zinc
         }),
        TextEntry::make('asset_inventory')->label(GoogleTranslate::trans('Asset Inventory', session('locale') ?? 'en')),
        TextEntry::make('serial_P')->label(GoogleTranslate::trans('Serial Printed', session('locale') ?? 'en')),
        TextEntry::make('serial_I')->label(GoogleTranslate::trans('Serial Internal', session('locale') ?? 'en')),
        TextEntry::make('cellCount')->label(GoogleTranslate::trans('Cell Count', session('locale') ?? 'en')),
        TextEntry::make('nominal_voltage')->label(GoogleTranslate::trans('Voltage', session('locale') ?? 'en')),
        TextEntry::make('capacity')->label(GoogleTranslate::trans('Capacity', session('locale') ?? 'en')),
        TextEntry::make('initial_Cycle_count')->label(GoogleTranslate::trans('Initial Cycles Count', session('locale') ?? 'en')),
        TextEntry::make('life_span')->label(GoogleTranslate::trans('Life Span', session('locale') ?? 'en')),
        TextEntry::make('flaight_count')->label(GoogleTranslate::trans('Flight Count', session('locale') ?? 'en')),
        TextEntry::make('drone.name')->label(GoogleTranslate::trans('For Drone (Optional)', session('locale') ?? 'en'))
        ->url(fn($record) => $record->for_drone ? route('filament.admin.resources.drones.view', [
            'tenant' => Auth()->user()->teams()->first()->id,
            'record' => $record->for_drone,
        ]): null)->color(Color::Blue),
                ])->columns(5),
            Section::make('Extra Information')
                ->schema([
                    TextEntry::make('users.name')->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en'))
                    ->url(fn($record) => $record->for_drone ? route('filament.admin.resources.users.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->users_id,
                    ]): null)->color(Color::Blue),
                    TextEntry::make('purchase_date')->label(GoogleTranslate::trans('Purchase date', session('locale') ?? 'en')),
                TextEntry::make('insurable_value')->label(GoogleTranslate::trans('Insurable Value', session('locale') ?? 'en')),
                TextEntry::make('wight')->label(GoogleTranslate::trans('Weight', session('locale') ?? 'en')),
                TextEntry::make('firmware_version')->label(GoogleTranslate::trans('Firmware Version', session('locale') ?? 'en')),
                TextEntry::make('hardware_version')->label(GoogleTranslate::trans('Hardware Version', session('locale') ?? 'en')),
                IconEntry::make('is_loaner')->boolean()->label(GoogleTranslate::trans('Loaner Battery', session('locale') ?? 'en')),
                TextEntry::make('description')->label(GoogleTranslate::trans('Description', session('locale') ?? 'en')),
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
            'index' => Pages\ListBattreis::route('/'),
            'create' => Pages\CreateBattrei::route('/create'),
            'view' => Pages\ViewBattrei::route('/{record}'),
            'edit' => Pages\EditBattrei::route('/{record}/edit'),
        ];
    }
}
