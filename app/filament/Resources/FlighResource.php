<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlighResource\Pages;
use App\Filament\Resources\FlighResource\RelationManagers;
use App\Models\battrei;
use App\Models\customer;
use App\Models\drone;
use App\Models\equidment;
use App\Models\Fligh;
use App\Models\fligh_location;
use App\Models\kits;
use App\Models\Projects;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Filament\Widgets\FlightChart;
use Carbon\Carbon;
use Filament\Forms\Components\Button;
use Filament\Forms\Components\View;


class FlighResource extends Resource
{
    protected static ?string $model = Fligh::class;



    public static ?string $tenantOwnershipRelationshipName = 'teams';

    protected static ?string $navigationLabel = 'Flights' ;

    Protected static ?string $modelLabel = 'Flights';
    public static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';
    public static ?string $navigationGroup = 'flight';


    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;;
        return $form
            ->schema([
                Forms\Components\Section::make('Flight Detail')
                    ->description('')
                    ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('start_date_flight')->label('Start Date Flight')
                ->afterStateUpdated(function (callable $get, callable $set) {
                    $start = $get('start_date_flight');
                    $end = $get('end_date_flight');
                    if ($start && $end) {
                        $diffInSeconds = Carbon::parse($start)->diffInSeconds(Carbon::parse($end));
                        $duration = gmdate('H:i:s', $diffInSeconds); // Format menjadi hh:mm:ss
                        $set('duration', $duration);
                    }
                })->reactive()
                    ->required(),
                Forms\Components\DateTimePicker::make('end_date_flight')->label('End Date Flight')
                ->afterStateUpdated(function (callable $get, callable $set) {
                    $start = $get('start_date_flight');
                    $end = $get('end_date_flight');
                    if ($start && $end) {
                        $diffInSeconds = Carbon::parse($start)->diffInSeconds(Carbon::parse($end));
                        $duration = gmdate('H:i:s', $diffInSeconds); // Format menjadi hh:mm:ss
                        $set('duration', $duration);
                    }
                })->reactive()
                    ->required(),
                Forms\Components\Hidden::make('duration')
                    ->reactive(),
                Forms\Components\Select::make('type')->label('Flight Type')
                    ->options([
                        'commercial-agriculture' => 'Commercial-Agriculture',
                        'commercial-inspection' => 'Commercial-Inspection',
                        'commercial-mapping/survey' => 'Commercial-Mapping/Survey',
                        'commercial-other' => 'Commercial-Other',
                        'commercial-photo/video' => 'Commercial-Photo/Video',
                        'emergency' => 'Emergency',
                        'hobby-entertainment' => 'Hobby-Entertainment',
                        'maintenance' => 'Maintenance',
                        'mapping_hr' => 'Mapping HR',
                        'mapping_uhr' => 'Mapping UHR',
                        'photogrammetry' => 'Photogrammetry',
                        'science' => 'Science',
                        'search_rescue' => 'Seach and Rescue',
                        'simulator' => 'Simulator',
                        'situational_awareness' => 'Situational Awareness',
                        'spreading' => 'Spreading',
                        'surveillance/patrol' => 'Surveillance or Patrol',
                        'survey' => 'Survey',
                        'test_flight' => 'Test Flight',
                        'training_flight' => 'Training Flight',
                    ])->searchable()
                    ->required(),
                Forms\Components\Select::make('ops')->label('Ops')
                    ->options([
                        'vlos(manual)' => 'VLOS(Manual)',
                        'vlos_autonomous' => 'VLOS Autonomous',
                        'automatic' => 'Automatic',
                        'vlos_lts' => 'VLOS LTS',
                        'evlos' => 'EVLOS',
                        'bvlos/blos' => 'BVLOS/BLOS',
                        'tethered' => 'Tethered',
                        'fvp' => 'FVP',
                        'over_people' => 'Over People',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('landings')
                    ->required()
                    ->numeric(),
                Forms\Components\Grid::make(1)->schema([
                    view::make('component.button-project')->extraAttributes(['class' => 'mr-6 custom-spacing']),
                    Forms\Components\Select::make('projects_id')
                    ->relationship('projects', 'case')
                    ->required()
                    ->reactive()
                    ->searchable()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $project = Projects::find($state);
                            $set('customers_id', $project ? $project->customers_id : null);
                            $set('customers_name', $project && $project->customers ? $project->customers->name : null);
                        } else {
                            $set('customers_id', null);
                            $set('customers_name', null);
                        }
                    })
                    ->options(Projects::where('teams_id', auth()->user()->teams()->first()->id)
                            ->pluck('case', 'id')
                            )->searchable(),
                ])->columnSpan(1),
                //grid location
                Forms\Components\Grid::make(1)->schema([
                    view::make('component.button-location'),
                    Forms\Components\Select::make('location_id')
                    // ->relationship('fligh_location', 'name', function (Builder $query) {
                    //     $currentTeamId = auth()->user()->teams()->first()->id;
                    //     $query->whereHas('teams', function (Builder $query) use ($currentTeamId){
                    //         $query->where('teams_id', $currentTeamId);
                    //     });
                    // })
                    ->options(function (callable $get) use ($currentTeamId) {
                        return fligh_location::where('teams_id', $currentTeamId)->pluck('name', 'id');
                    })
                    ->label('Location')
                    ->searchable()
                    ->required(),
                ])->columnSpan(2),
                //end grid 
                Forms\Components\Hidden::make('customers_id') 
                    ->required(),
                Forms\Components\TextInput::make('customers_name')
                    ->label('Customer Name')
                    //->relationship('customers', 'name')
                    ->required()
                    ->disabled()
                    ->columnSpanFull(),
 
                ])->columns(3),
                Forms\Components\Section::make('Personnel')
                    ->description('')
                    ->schema([
                        Forms\Components\Select::make('users_id')
                        ->label('Pilot')
                        ->relationship('users', 'name', function (Builder $query) {
                            $currentTeamId = auth()->user()->teams()->first()->id;
                            $query->whereHas('teams', function (Builder $query) use ($currentTeamId){
                                $query->where('team_id', $currentTeamId);
                            })
                            ->whereHas('roles', function (Builder $query) {
                                $query->where('roles.name', 'Pilot');
                            });
                        })  
                    ->required(),
                Forms\Components\TextInput::make('instructor')->label('Instructor')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('vo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('po')
                    ->required()
                    ->maxLength(255),
                ])->columns(2),
                Forms\Components\Section::make('Drone & Equipments')
                    ->description('')
                    ->schema([   
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-drone'),
                    Forms\Components\Select::make('drones_id')
                    // ->relationship('drones', 'name', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;;
                    //     $query->where('teams_id', $currentTeamId);
                    // })    
                    ->required()
                    ->label('Drones')
                    ->options(function (callable $get) use ($currentTeamId) {
                        //$flightDate = $get('flight_date');
                        return drone::where('teams_id', $currentTeamId)->where('status', 'airworthy')->whereHas('maintence_drone', function ($query) {
                            $query->orWhere('status', 'completed'); 
                        })
                        // ->whereDoesntHave('fligh', function ($query) use ($flightDate) {
                        //     $query->whereDate('date_flight', $flightDate); // Pastikan equipment tidak digunakan di tanggal flight yang sama
                        // })
                        ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->columnSpanFull(),

                ]), 
                //kits
                Forms\Components\Select::make('kits_id')
                    ->label('Kits')
                    // ->relationship('kits', 'name', function (Builder $query) {
                    //     $currentTeamId = auth()->user()->teams()->first()->id;
                    //     $query->whereHas('teams', function (Builder $query) use ($currentTeamId){
                    //         $query->where('team_id', $currentTeamId);
                    //     });
                    // })
                    // ->options(function (callable $get) use ($currentTeamId) {
                    //     //$flightDate = $get('flight_date'); // Ambil tanggal flight
                
                    //     return kits::where('teams_id', $currentTeamId)
                    //         ->whereDoesntHave('fligh', function ($query) use ($flightDate) {
                    //             $query->whereDate('date_flight', $flightDate); // Filter kits yang belum digunakan pada tanggal yang sama
                    //         })
                    //         ->pluck('name', 'id');
                    // })
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $kit = kits::find($state);
                            if ($kit) {
                                if ($kit->type === 'battery') {
                                    $batteries = $kit->battrei()->pluck('name')->join(', ');
                                    $set('battery_name', $batteries);
                                    $set('camera_gimbal', null); 
                                    $set('others', null); 
                                }
                
                                if ($kit->type === 'mix') {
                                    $battery = $kit->equidment()->where('type', 'battery')->pluck('type')->join(', ');
                                    $cameraGimbal = $kit->equidment()->where('type', 'camera')->pluck('type')->join(', ');
                                    $others = $kit->equidment()->whereNotIn('type', ['camera'])->pluck('type')->join(', ');
                
                                    $set('camera_gimbal', $cameraGimbal);
                                    $set('others', $others);
                                    $set('battery_name', $battery);
                                }
                            } else {
                                $set('battery_name', null);
                                $set('camera_gimbal', null);
                                $set('others', null);
                            }
                        }
                    })
                    ->options(function (callable $get) use ($currentTeamId) {
                        return kits::where('teams_id', $currentTeamId)->pluck('name', 'id');
                    }),
                Forms\Components\TextInput::make('battery_name')
                        ->label('Battery')
                        ->helperText('Automatically filled when selecting kits')
                        ->disabled(), 
                Forms\Components\TextInput::make('camera_gimbal')
                        ->label('Camera/Gimbal')
                        ->helperText('Automatically filled when selecting kits')
                        ->disabled(), 
                Forms\Components\TextInput::make('others')
                        ->helperText('Automatically filled when selecting kits')
                        ->label('Others')
                        ->disabled(),
                
            
                //grid battery
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-battery'),
                    Forms\Components\Select::make('battreis_id')->label('Battery')
                    // ->relationship('battreis', 'name', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;;
                    //     $query->where('teams_id', $currentTeamId);
                    // }),
                    ->options(function (callable $get) use ($currentTeamId) {
                        return battrei::where('teams_id', $currentTeamId)->pluck('name', 'id');
                    })
                    // ->options(function (callable $get) use ($currentTeamId) {
                    //     $flightDate = $get('flight_date'); // Ambil tanggal flight
                
                    //     return battrei::where('teams_id', $currentTeamId)
                    //         ->whereDoesntHave('fligh', function ($query) use ($flightDate) {
                    //             $query->whereDate('date_flight', $flightDate); // Filter kits yang belum digunakan pada tanggal yang sama
                    //         })
                    //         ->pluck('name', 'id');
                    // })
                    ->searchable(),
                ])->columnSpan(1),
                //grid equdiment
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-equidment'),
                    Forms\Components\Select::make('equidments_id')->label('Equipment')
                    // ->relationship('equidments', 'name', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;;
                    //     $query->where('teams_id', $currentTeamId);
                    // }),
                    ->options(function (callable $get) use ($currentTeamId) {
                        $flightDate = $get('flight_date'); // Ambil tanggal flight
                        
                        return equidment::where('teams_id', $currentTeamId)
                            ->where('status', 'airworthy')
                            ->whereHas('maintence_eq', function ($query) {
                                $query->orWhere('status', 'completed'); // Saring equipment yang maintenance-nya sudah selesai
                            })
                            // ->whereDoesntHave('fligh', function ($query) use ($flightDate) {
                            //     $query->whereDate('date_flight', $flightDate); // Pastikan equipment tidak digunakan di tanggal flight yang sama
                            // })
                            ->pluck('name', 'id'); // Mengambil nama dan ID equipment yang sesuai dengan kriteria
                    })
                    ->searchable(),
                ])->columnSpan(1),
                
                Forms\Components\TextInput::make('pre_volt')->label('Pre Voltage')
                    ->numeric()    
                    ->required(),
                Forms\Components\TextInput::make('fuel_used')
                    ->numeric()    
                    ->required()
                    ->default('1'),
                    ])->columns(2),
                //Forms\Components\TextInput::make('wheater_id')
                    //->required()
                    //->numeric(),
                Forms\Components\Hidden::make('teams_id')
                    ->default(auth()->user()->teams()->first()->id ?? null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date_flight')->label('Date Flight')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration'),
                Tables\Columns\TextColumn::make('fligh_location.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('projects.case')
                    ->numeric()
                    ->url(fn($record) => $record->projects_id?route('filament.admin.resources.projects.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->projects_id,
                    ]):null)->color(Color::Blue)
                    ->sortable(),
                Tables\Columns\TextColumn::make('projects.customers.name')
                    ->numeric()
                    ->url(fn($record) => $record->customers_id?route('filament.admin.resources.customers.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->customers_id,
                    ]):null)->color(Color::Blue)
                    ->sortable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Pilot')
                    ->numeric()
                    ->url(fn($record) => $record->users_id?route('filament.admin.resources.users.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->users_id,
                    ]):null)->color(Color::Blue)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('start_date_flight')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                        Forms\Components\DatePicker::make('from')->label('Flight Date From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data){
                        $query->when($data['from'], fn ($q) => $q->whereDate('start_date_flight', '>=', $data['from']));
                        $query->when($data['until'], fn ($q) => $q->whereDate('start_date_flight', '<=', $data['until']));
                    }),
                Tables\Filters\SelectFilter::make('projects_id')
                    ->relationship('projects', 'case', function (Builder $query){
                        $currentTeamId = auth()->user()->teams()->first()->id;;
                        $query->where('teams_id', $currentTeamId);
                    })    
                    ->label('Filter by Project'),
                Tables\Filters\SelectFilter::make('drones_id')
                    ->relationship('drones', 'name', function (Builder $query){
                        $currentTeamId = auth()->user()->teams()->first()->id;;
                        $query->where('teams_id', $currentTeamId);
                    })    
                    ->label('Filter by Drones'),
                Tables\Filters\SelectFilter::make('users_id')
                    ->relationship('users', 'name', function (Builder $query) {
                        $currentTeamId = auth()->user()->teams()->first()->id;
                        $query->whereHas('teams', function (Builder $query) use ($currentTeamId){
                            $query->where('team_id', $currentTeamId);
                        })
                        ->whereHas('roles', function (Builder $query) {
                            $query->where('roles.name', 'Pilot');
                        });
                    })
                    ->label('Filter by Pilot')
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
            Section::make('Flight Detail')
                ->schema([
                TextEntry::make('name')->label('Name'),
                TextEntry::make('start_date_flight')->label('Date Flight'),
                TextEntry::make('duration')->label('Duration'),
                TextEntry::make('type')->label('Type'),
                TextEntry::make('ops')->label('Ops'),
                TextEntry::make('landings')->label('Landings'),
                TextEntry::make('fligh_location.name')->label('Location'),
                TextEntry::make('customers.name')->label('Customer')
                    ->url(fn($record) => $record->customers_id?route('filament.admin.resources.customers.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->customers_id,
                    ]):null)->color(Color::Blue),
                TextEntry::make('projects.case')->label('Project')
                    ->url(fn($record) => $record->projects_id?route('filament.admin.resources.projects.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->projects_id,
                    ]):null)->color(Color::Blue),
                ])->columns(5),
            Section::make('Personnel')
                ->schema([
                TextEntry::make('users.name')->label('Pilot')
                    ->url(fn($record) => $record->users_id?route('filament.admin.resources.users.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->users_id,
                    ]):null)->color(Color::Blue),
                TextEntry::make('instructor')->label('Instructor'),
                TextEntry::make('vo')->label('VO'),
                TextEntry::make('po')->label('PO'),
                ])->columns(4),
            Section::make('Drone & Equipments')
                ->schema([
                TextEntry::make('kits.name')->label('Kits'),
                //TextEntry::make('battery_name')->label('Kits Battery'),
                // TextEntry::make('kits.equidment.type')->label('Kits Equipments (Camera) '),
                TextEntry::make('drones.name')->label('Drone')
                ->url(fn($record) => $record->users_id?route('filament.admin.resources.drones.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->users_id,
                ]):null)->color(Color::Blue),
                TextEntry::make('battreis.name')->label('Battery')
                ->url(fn($record) => $record->users_id?route('filament.admin.resources.battreis.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->users_id,
                ]):null)->color(Color::Blue),
                TextEntry::make('equidments.name')->label('Equipment')->url(fn($record) => $record->users_id?route('filament.admin.resources.equidments.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->users_id,
                ]):null)->color(Color::Blue),
                TextEntry::make('pre_volt')->label('Pre-Voltage'),
                TextEntry::make('fuel_used')->label('Fuel Used'),
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
            'index' => Pages\ListFlighs::route('/'),
            'create' => Pages\CreateFligh::route('/create'),
            'edit' => Pages\EditFligh::route('/{record}/edit'),
        ];
    }
}
