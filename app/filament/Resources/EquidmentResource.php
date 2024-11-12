<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquidmentResource\Pages;
use App\Filament\Resources\EquidmentResource\RelationManagers;
use App\Models\drone;
use App\Models\Equidment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Infolists\Components\Section;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;
use Filament\Infolists\Components\IconEntry;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Stichoza\GoogleTranslate\GoogleTranslate;

class EquidmentResource extends Resource
{
    protected static ?string $model = Equidment::class;
    // protected static ?string $navigationLabel = 'Equipment' ;
    protected static ?string $navigationIcon = 'heroicon-m-cube';
    protected static?string $navigationGroup = 'Inventory';

    // protected static?string $modelLabel = 'Equipment';

    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Equipment', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Equipment', session('locale') ?? 'en');
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
                        Tabs\Tab::make('Overview')
                        ->schema([
                            Forms\Components\Hidden::make('teams_id')
                                ->default(auth()->user()->teams()->first()->id ?? null),
                            Forms\Components\TextInput::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('model')->label(GoogleTranslate::trans('Model', session('locale') ?? 'en'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\select::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                                ->options([
                                    'airworthy' => 'Airworthy',
                                    'maintenance' => 'Maintenance',
                                    'retired' => 'Retired',
                                ])
                                ->required(),
                            Forms\Components\Select::make('inventory_asset')->label(GoogleTranslate::trans('Inventory / Asset', session('locale') ?? 'en'))
                                ->options([
                                    'asset'=> 'Assets',
                                    'inventory'=> 'Inventory',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('serial')->label(GoogleTranslate::trans('Serial', session('locale') ?? 'en'))
                                ->required()->columnSpan(2),
                            Forms\Components\select::make('type')->label(GoogleTranslate::trans('Type', session('locale') ?? 'en'))
                                ->options([
                                    'airframe' => 'Airframe',
                                    'anenometer' => 'Anenometer',
                                    'battery' => 'Battery',
                                    'battery_charger' => 'Battery Charger',
                                    'camera' => 'Camera',
                                    'charger' => 'Charger',
                                    'cradle' => 'Cradle',
                                    'drives(disc, flash)' => 'Drives(disc, flash)',
                                    'flight_controller' => 'Flight controller',
                                    'fvp_glasses' => 'FVP Glasses',
                                    'gimbal' => 'Gimbal',
                                    'gps' => 'GPS',
                                    'lens' => 'Lens',
                                    'lights' => 'Lights',
                                    'monitor' => 'Monitor',
                                    'motor' => 'Motor',
                                    'parachute' => 'Parachute',
                                    'phone/tablet' => 'Phone/Tablet',
                                    'power_supply' => 'Power Supply',
                                    'prop_guards' => 'Prop Guards',
                                    'propeller' => 'Propeller',
                                    'radio_receiver' => 'Radio Receiver',
                                    'radio_transmitter' => 'Radio Transmitter',
                                    'radio_extender' => 'Radio Extender',
                                    'radio_finder(laser)' => 'Radio Finder(laser)',
                                    'remote_controller' => 'Remote Controller',
                                    'sensors' => 'Sensors',
                                    'spreader' => 'Spreader',
                                    'telementry_radio' => 'Telementry Radio',
                                    'tripod' => 'Tripod',
                                    'video_transmitter' => 'Video Transmitter'
                                ])->searchable()
                                ->required()->columnSpan(2),
                            Forms\Components\Select::make('drones_id')->label(GoogleTranslate::trans('For Drone (Optional)', session('locale') ?? 'en'))
                                // ->relationship('drones', 'name', function (Builder $query){
                                //     $currentTeamId = auth()->user()->teams()->first()->id;
                                //     $query->where('teams_id', $currentTeamId);
                                // }),
                                ->searchable()
                                ->options(function (callable $get) use ($currentTeamId) {
                                    return drone::where('teams_id', $currentTeamId)->pluck('name', 'id');
                                })
                        ])->columns(4),
                        Tabs\Tab::make('Extra Information')
                        ->schema([
                            Forms\Components\Select::make('users_id')->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en'))
                                //->relationship('users', 'name')
                                ->searchable()
                                ->options(function () {
                                    $currentTeamId = auth()->user()->teams()->first()->id; 
                            
                                    return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                        $query->where('team_user.team_id', $currentTeamId); 
                                    })->pluck('name', 'id'); 
                                }),
                            Forms\Components\DatePicker::make('purchase_date')->label(GoogleTranslate::trans('Purchase Date', session('locale') ?? 'en'))
                                ->required(),
                            Forms\Components\TextInput::make('insurable_value')->label(GoogleTranslate::trans('Insurable Value', session('locale') ?? 'en'))
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('weight')->label(GoogleTranslate::trans('Weight', session('locale') ?? 'en'))
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('firmware_v')->label(GoogleTranslate::trans('Firmware Version', session('locale') ?? 'en'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('hardware_v')->label(GoogleTranslate::trans('Hardware Version', session('locale') ?? 'en'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Checkbox::make('is_loaner')->label(GoogleTranslate::trans('Loaner Equipment', session('locale') ?? 'en')),
                            Forms\Components\TextArea::make('description')->label(GoogleTranslate::trans('Description', session('locale') ?? 'en'))
                                ->maxLength(255),
                            ])->columns(3),
                    ])->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(GoogleTranslate::trans('Name', session('locale') ?? 'en'))
                     ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->label(GoogleTranslate::trans('Model', session('locale') ?? 'en'))
                     ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(GoogleTranslate::trans('Type', session('locale') ?? 'en'))
                     ->searchable(),
                Tables\Columns\TextColumn::make('flight_time')
                    //  ->label('Flights & Flying Time')
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
                Tables\Columns\TextColumn::make('status')
                    ->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                     ->color(fn ($record) => match ($record->status){
                         'airworthy' => Color::Green,
                        'maintenance' =>Color::Red,
                        'retired' => Color::Zinc,
                    })
                    ->searchable(),
                 Tables\Columns\TextColumn::make('drones.name')
                    ->label(GoogleTranslate::trans('For Drone', session('locale') ?? 'en'))
                     ->numeric()
                     ->url(fn($record) =>$record->for_drone? route('filament.admin.resources.drones.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->for_drone,
                    ]):null)->color(Color::Blue)
                     ->sortable(),
                 Tables\Columns\TextColumn::make('users.name')
                    ->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en'))
                    ->url(fn($record) => $record->users_id? route('filament.admin.resources.users.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->users_id,
                    ]):null)->color(Color::Blue)
                     ->numeric()
                     ->sortable(),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
                TextEntry::make('model')->label(GoogleTranslate::trans('Model', session('locale') ?? 'en')),
                TextEntry::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                    ->color(fn ($record) => match ($record->status){
                    'airworthy' => Color::Green,
                    'maintenance' =>Color::Red,
                    'retired' => Color::Zinc
                }),
                TextEntry::make('inventory_asset')->label(GoogleTranslate::trans('Inventory/Asset', session('locale') ?? 'en')),
                TextEntry::make('serial')->label(GoogleTranslate::trans('Serial', session('locale') ?? 'en')),
                TextEntry::make('type')->label(GoogleTranslate::trans('Type', session('locale') ?? 'en')),
                TextEntry::make('drones.name')->label(GoogleTranslate::trans('Drones', session('locale') ?? 'en'))
                    ->url(fn($record) =>$record->for_drone? route('filament.admin.resources.drones.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->for_drone,
                    ]):null)->color(Color::Blue),
                    ])->columns(4),
            Section::make('Extra Information')
            ->schema([
                TextEntry::make('users.name')->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en'))
                    ->url(fn($record) => $record->users_id? route('filament.admin.resources.users.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->users_id,
                    ]):null)->color(Color::Blue),
                TextEntry::make('purchase_date')->label(GoogleTranslate::trans('Purchase Date', session('locale') ?? 'en'))->date('Y-m-d'),
                TextEntry::make('insurable_value')->label(GoogleTranslate::trans('Insurable Value', session('locale') ?? 'en')),
                TextEntry::make('weight')->label(GoogleTranslate::trans('Weight', session('locale') ?? 'en')),
                TextEntry::make('firmware_v')->label(GoogleTranslate::trans('Firmware Version', session('locale') ?? 'en')),
                TextEntry::make('hardware_v')->label(GoogleTranslate::trans('Hardware Version', session('locale') ?? 'en')),
                IconEntry::make('is_loaner')->boolean()->label(GoogleTranslate::trans('Loaner Equipment', session('locale') ?? 'en')),
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
            'index' => Pages\ListEquidments::route('/'),
            'create' => Pages\CreateEquidment::route('/create'),
            'edit' => Pages\EditEquidment::route('/{record}/edit'),
            'view' => Pages\ViewEquidment::route('/{record}')
        ];
    }
}
