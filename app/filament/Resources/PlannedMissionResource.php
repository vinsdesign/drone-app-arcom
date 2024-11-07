<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlannedMissionResource\Pages;
use App\Filament\Resources\PlannedMissionResource\RelationManagers;
use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh;
use App\Models\fligh_location;
use App\Models\kits;
use App\Models\PlannedMission;
use App\Models\Projects;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Filament\Forms\Components\View;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class PlannedMissionResource extends Resource
{
    protected static ?string $model = PlannedMission::class;
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static ?string $tenantRelationshipName = 'PlannedMission';
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document';
    public static ?string $navigationGroup = 'flight';
    public static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;;
        return $form
            ->schema([
                Forms\Components\Section::make('Mission Details')
                    ->description('')
                    ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Mission Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
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
                        $hours = floor($diffInSeconds / 3600);
                        $minutes = floor(($diffInSeconds % 3600) / 60);
                        $seconds = $diffInSeconds % 60;
                        $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
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
                    ->default(function (){
                        $currentTeam = auth()->user()->teams()->first();
                        return $currentTeam ? $currentTeam->flight_type : null;
                    })
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
                    ->required()
                    ->columnSpan(2),
                Forms\Components\TextInput::make('landings')
                    ->required()
                    ->default('1')
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
                    ->default(function (){
                        $currentTeam = auth()->user()->teams()->first();
                        return $currentTeam ? $currentTeam->id_projects : null;
                    })
                    ->options(Projects::where('teams_id', auth()->user()->teams()->first()->id)
                            ->pluck('case', 'id')
                            )->searchable(),
                ])->columnSpan(1),
                //grid location
                Forms\Components\Grid::make(1)->schema([
                    view::make('component.button-location'),
                    Forms\Components\Select::make('location_id')
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
                    ->required()
                    ->disabled()
                    ->helperText('Automatically filled when selecting projects')
                    ->default(function (){
                        $currentTeam = auth()->user()->teams()->first();
                        return $currentTeam ? $currentTeam->id_customers  : null;
                    })
                    ->columnSpanFull(),
                ])->columns(3),
                Forms\Components\Section::make('Personnel')
                    ->description('')
                    ->schema([
                        Forms\Components\Select::make('users_id')
                        ->label('Pilot')
                        ->relationship('users', 'name', function (Builder $query, callable $get) {
                            $currentTeamId = auth()->user()->teams()->first()->id;
                            $startDate = $get('start_date_flight');
                            $endDate = $get('end_date_flight');
                        
                            if (!$startDate || !$endDate) {
                                return $query->whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                    $query->where('team_id', $currentTeamId);
                                    })->whereHas('roles', function (Builder $query){
                                        $query->where('roles.name', 'Pilot');
                                });
                            }
                        
                            return $query->whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                    $query->where('team_id', $currentTeamId);
                                })
                                ->whereHas('roles', function (Builder $query) {
                                    $query->where('roles.name', 'Pilot');
                                })
                                ->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                                    $query->where(function ($query) use ($startDate, $endDate) {
                                        $query->where(function ($query) use ($startDate, $endDate) {
                                            $query->where('start_date_flight', '<=', $endDate)
                                                  ->where('end_date_flight', '>=', $startDate);
                                        });
                                    });
                                });
                        })
                    ->required(),
                ])->columns(2),
                Forms\Components\Section::make('Drone & Equipments')
                    ->description('')
                    ->schema([   
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-drone'),
                    Forms\Components\Select::make('drones_id')
                    ->required()
                    ->label('Drones')
                    ->options(function (callable $get) use ($currentTeamId) { 
                        $startDate = $get('start_date_flight');
                        $endDate = $get('end_date_flight');
                    
                        return drone::where('teams_id', $currentTeamId)
                            ->where('status', 'airworthy')
                            ->where(function ($query) {
                                $query->doesntHave('maintence_drone')
                                    ->orWhereHas('maintence_drone', function ($query) {
                                        $query->where('status', 'completed'); 
                                    });
                            })
                            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                                $query->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                                    $query->where(function ($query) use ($startDate, $endDate) {
                                        $query->where(function ($query) use ($startDate, $endDate) {
                                            $query->where('start_date_flight', '<=', $endDate)
                                                  ->where('end_date_flight', '>=', $startDate);
                                        });
                                    });
                                });
                            })
                            ->pluck('name', 'id');
                    })->saveRelationshipsUsing(function ($state, callable $get) {
                        $start = $get('start_date_flight');
                        $end = $get('end_date_flight');
                        $state = is_array($state) ? $state : [$state];
                        foreach ($state as $key) {
                            drone::where('id', $key)->increment('initial_flight');
                        };
                                if ($start && $end) {
                                    $diffInSeconds = Carbon::parse($start)->diffInSeconds(Carbon::parse($end));
                                    $duration = gmdate('H:i:s', $diffInSeconds);
                                    $durationArray = is_array($duration) ? $duration : [$duration];
                                    
                                    foreach ($state as $key) {
                                        $drone = drone::find($key);
                                        if ($drone) { 
                                            $currentFlightTime = Carbon::parse($drone->initial_flight_time)->secondsSinceMidnight();
                                            $newDurationInSeconds = Carbon::parse($durationArray[0])->secondsSinceMidnight();
                                            $totalFlightTimeInSeconds = $currentFlightTime + $newDurationInSeconds;
                                            $totalFlightTime = gmdate('H:i:s', $totalFlightTimeInSeconds);
                                            $drone->update(['initial_flight_time' => $totalFlightTime]);
                                        }
                                    }
                                };
  
                    })
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $kit = kits::where('drone_id', $state)->get();
                            
                            if ($kit->isNotEmpty()) {
                                $firstDroneKits = $kit->first();

                                if ($firstDroneKits){
                                    $set('kits_id', $firstDroneKits->id);
                                
                                if ($firstDroneKits->type === 'battery') {
                                    $batteries = $firstDroneKits->battrei()->pluck('name')->join(', ');
                                    $set('battery_name', $batteries);
                                    $set('camera_gimbal', null); 
                                    $set('others', null); 
                                }
                
                                if ($firstDroneKits->type === 'mix') {
                                    $battery = $firstDroneKits->battrei()->pluck('name')->join(', ');
                                    $cameraGimbal = $firstDroneKits->equidment()->whereIn('type', ['camera', 'gimbal'])->pluck('type')->join(', ');
                                    $others = $firstDroneKits->equidment()->whereNotIn('type', ['camera', 'gimbal'])->pluck('type')->join(', ');
                
                                    $set('battery_name', $battery);
                                    $set('camera_gimbal', $cameraGimbal);
                                    $set('others', $others);
                                }
                            } else {
                                $set('kits_id', null);
                                $set('battery_name', null);
                                $set('camera_gimbal', null);
                                $set('others', null);
                            }
                            }
                        }
                    })
                    ->searchable()
                    ->columnSpanFull(),
                ])->columnSpan(2), 
                //grid Kits
                Forms\Components\Grid::make(1)->schema([

                Forms\Components\Checkbox::make('show_all_kits') 
                ->label('Show All Kits')
                ->reactive() 
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state){
                        $set('kits_id', null);
                    }
                }),
                Forms\Components\Select::make('kits_id')
                ->label('Kits')
                ->options(function (callable $get) use ($currentTeamId) { 
                    $startDate = $get('start_date_flight');
                    $endDate = $get('end_date_flight');
                    $droneId = $get('drones_id');
                    $showAllKits = $get('show_all_kits');

                    if ($showAllKits){
                        return Kits::where('teams_id', $currentTeamId)->pluck('name', 'id');
                    }
                    
                    return kits::where('teams_id', $currentTeamId)
                        ->when($droneId, function ($query) use ($droneId){
                            $query->where('drone_id', $droneId);
                        })
                        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                            $query->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                                $query->where(function ($query) use ($startDate, $endDate) {
                                    $query->where(function ($query) use ($startDate, $endDate) {
                                        $query->where('start_date_flight', '<=', $endDate)
                                              ->where('end_date_flight', '>=', $startDate);
                                    });
                                });
                            });
                        })
                        ->pluck('name', 'id'); 
                })                                   
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
                                $battery = $kit->battrei()->pluck('name')->join(', ');
                                $cameraGimbal = $kit->equidment()->whereIn('type', ['camera', 'gimbal'])->pluck('type')->join(', ');
                                $others = $kit->equidment()->whereNotIn('type', ['camera', 'gimbal'])->pluck('type')->join(', ');
            
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
                }),
                ])->columnSpan(1),
                //end grid Kits

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
                Forms\Components\Select::make('battreis')->label('Battery')
                    ->options(function (callable $get) use ($currentTeamId) { 
                        $startDate = $get('start_date_flight');
                        $endDate = $get('end_date_flight');
                        
                        return battrei::where('teams_id', $currentTeamId)
                            ->whereDoesntHave('kits', function ($query) {
                                $query->whereNotNull('kits.id');
                            })
                            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                                $query->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                                    $query->where(function ($query) use ($startDate, $endDate) {
                                        $query->where(function ($query) use ($startDate, $endDate) {
                                            $query->where('start_date_flight', '<=', $endDate)
                                                  ->where('end_date_flight', '>=', $startDate);
                                        });
                                    });
                                });
                            })
                            ->pluck('name', 'id'); 
                    })   
                    ->multiple()
                    ->searchable()
                    ->saveRelationshipsUsing(function ($component, $state) {
                        $component->getRecord()->battreis()->sync($state);
                        foreach ($state as $key){
                            battrei::where('id',$key)->increment('initial_Cycle_count');
                        }
                    }),
                ])->columnSpan(1),

                //grid equdiment
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-equidment'),
                Forms\Components\Select::make('equidments')->label('Equipment')
                    ->multiple()
                    ->options(function (callable $get) use ($currentTeamId) { 
                        $startDate = $get('start_date_flight');
                        $endDate = $get('end_date_flight');
                                        
                        return equidment::where('teams_id', $currentTeamId)
                            ->where('status', 'airworthy')
                            ->where(function ($query) {
                                $query->doesntHave('maintence_eq')
                                    ->orWhereHas('maintence_eq', function ($query) {
                                        $query->where('status', 'completed'); 
                                    });
                            })
                          ->whereDoesntHave('kits', function ($query) {
                                $query->whereNotNull('kits.id');
                            })
                            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                                $query->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                                    $query->where('start_date_flight', '<=', $endDate)
                                          ->where('end_date_flight', '>=', $startDate);
                                });
                            })
                            ->pluck('name', 'id');
                    })                    
                    ->searchable()
                    ->saveRelationshipsUsing(function ($component, $state) {
                        $component->getRecord()->equidments()->sync($state);
                    }),
                  ])->columnSpan(2),

                Forms\Components\TextInput::make('pre_volt')->label('Pre Voltage')
                    ->numeric()    
                    ->required(),
                Forms\Components\TextInput::make('fuel_used')
                    ->numeric()    
                    ->required()
                    ->placeholder('0')
                    ->default('1')->columnSpan(2),
                ])->columns(3),
                Forms\Components\Hidden::make('status')
                    ->default('planned'),
                Forms\Components\Hidden::make('teams_id')
                    ->default(auth()->user()->teams()->first()->id ?? null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Mission Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date_flight')
                    ->label('Start Flight')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date_flight')
                    ->label('End Flight')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration'),
                Tables\Columns\TextColumn::make('fligh_location.name')
                    ->label('Location')
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'append' => 'Append',
                        'planned' => 'Planned',
                        'completed' => 'Completed',
                        'cancel' => 'Cancel',
                    ])
                    ->default('planned')
            ])
            ->actions([
                //append flight
                Action::make('append_flight')
                ->label(fn ($record) => $record->status === 'append' ? 'Already Append' : 'Append Flight')
                ->modalHeading('Append this Planned Mission to Flight')
                ->modalSubmitActionLabel('Append')
                ->action(function ($record) {
                    fligh::create([ 
                        'name' => $record->name, 
                        'start_date_flight' => $record->start_date_flight,
                        'end_date_flight' => $record->end_date_flight,
                        'duration' => $record->duration, 
                        'type' => $record->type,
                        'ops' => $record->ops,
                        'landings' => $record->landings,
                        'customers_id' => $record->customers_id,
                        'location_id' => $record->location_id,
                        'projects_id' => $record->projects_id,
                        'kits_id' => $record->kits_id,
                        'users_id' => $record->users_id,
                        'vo' => null, 
                        'po' => null, 
                        'instructor' => null,
                        'drones_id' => $record->drones_id,
                        'pre_volt' => $record->pre_volt,
                        'fuel_used' => $record->fuel_used,
                        'teams_id' => $record->teams_id,
                    ]);
                    $record->update(['status' => 'append']);
                    Notification::make()
                        ->title('Flight Added')
                        ->body("Planned mission successfully added to Flight.")
                        ->success()
                        ->send();
                })
                ->disabled(fn ($record) => $record->status === 'append')
                ->visible(fn ($record) => $record->status !== 'cancel')
                ->button()
                ->requiresConfirmation(),
                //end append flight 

                Action::make('finalize')
                ->label('Finalize')
                ->modalHeading('Complete or Cancel this Mission')
                ->modalSubmitActionLabel('Submit')
                ->action(function ($record, array $data) {
                    $record->update(['status' => $data['status']]);

                    Notification::make()
                        ->title('Status Updated')
                        ->body("Status successfully changed to {$data['status']}.")
                        ->success()
                        ->send();
                })
                ->form([
                    Forms\Components\Radio::make('status')
                        ->label('Pilih Status')
                        ->options([
                            'completed' => 'Completed',
                            'cancel' => 'Cancel',
                        ])
                        
                        ->required(),
                ])
                ->visible(fn ($record) => !in_array($record->status, ['completed', 'cancel', 'append']))
                ->button(),
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
            Section::make('Mission Details')
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
                ]),
            Section::make('Drone & Equipments')
                ->schema([
                TextEntry::make('kits.name')->label('Kits'),
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
                TextEntry::make('status')->label('Status')
                    ->color(fn ($record) => match ($record->status){
                        'completed' => Color::Green,
                        'cancel' =>Color::Red,
                        'planned' => Color::Zinc,
                        'append' => Color::Green,
                    }),
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
            'index' => Pages\ListPlannedMissions::route('/'),
            'create' => Pages\CreatePlannedMission::route('/create'),
            'edit' => Pages\EditPlannedMission::route('/{record}/edit'),
        ];
    }
}
