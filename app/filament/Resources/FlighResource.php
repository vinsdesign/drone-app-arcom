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
use App\Models\User;
use App\Models\Projects;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
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
use Filament\Facades\Filament;
use Filament\Forms\Components\Button;
use Filament\Forms\Components\View;
use Filament\Infolists\Components\View as InfolistView;
use App\Helpers\TranslationHelper;


class FlighResource extends Resource
{
    protected static ?string $model = Fligh::class;

    public static ?string $tenantOwnershipRelationshipName = 'teams';

    // protected static ?string $navigationLabel = 'Flights' ;
    // protected static ?string $modelLabel = 'Flights';
    public static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';
    // public static ?string $navigationGroup = 'flight';
    protected static bool $isLazy = false;
    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::Where('teams_id',$teamID)->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Flights');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Flights');
    }


    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;;

        $cloneId = request()->query('clone');
        $defaultData = [];

        if ($cloneId) {
            $record = Fligh::find($cloneId);
            if ($record) {
                $defaultData = $record->toArray();
                $defaultData['name'] = $record->name . ' - CLONE';
            }
        }
        return $form
            ->schema([
                Forms\Components\Section::make(TranslationHelper::translateIfNeeded('Flight Detail'))
                    ->description('')
                    ->schema([
                Forms\Components\TextInput::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name'))    
                    ->required()
                    ->maxLength(255)
                    ->default($defaultData['name'] ?? null),
                Forms\Components\DateTimePicker::make('start_date_flight')
                ->label(TranslationHelper::translateIfNeeded('Start Date Flight'))
                ->afterStateUpdated(function (callable $get, callable $set) {
                    $start = $get('start_date_flight');
                    $end = $get('end_date_flight');
                    if ($start && $end) {
                        $diffInSeconds = Carbon::parse($start)->diffInSeconds(Carbon::parse($end));
                        $duration = gmdate('H:i:s', $diffInSeconds); // Format menjadi hh:mm:ss
                        $set('duration', $duration);
                    }
                })->reactive()
                    ->required()
                    ->default($defaultData['start_date_flight'] ?? null),
                Forms\Components\DateTimePicker::make('end_date_flight')
                ->label(TranslationHelper::translateIfNeeded('End Date Flight'))
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
                    ->required()
                    ->default($defaultData['end_date_flight'] ?? null),
                Forms\Components\Hidden::make('duration')
                    ->reactive()
                    ->default($defaultData['duration'] ?? null),
                Forms\Components\Select::make('type')
                ->label(TranslationHelper::translateIfNeeded('Flight Type'))    
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
                    // ->default(function (){
                    //     $currentTeam = auth()->user()->teams()->first();
                    //     return $currentTeam ? $currentTeam->flight_type : null;
                    // })
                    ->default(function () {
                        $cloneId = request()->query('clone'); 
                        if ($cloneId) {
                            $clonedRecord = \App\Models\Fligh::find($cloneId); 
                            return $clonedRecord ? $clonedRecord->type : null; 
                        }
                
                        $currentTeam = auth()->user()->teams()->first();
                        return $currentTeam ? $currentTeam->flight_type : null;
                    })
                    ->required(),
                Forms\Components\Select::make('ops')
                ->label(TranslationHelper::translateIfNeeded('Ops'))    
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
                    ->default($defaultData['ops'] ?? null),
                Forms\Components\TextInput::make('landings')
                ->label(TranslationHelper::translateIfNeeded('Landings'))    
                    ->required()
                    ->numeric()
                    ->default($defaultData['landings'] ?? null),
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-project')
                    ->extraAttributes(['class' => 'mr-6 custom-spacing']),
                    Forms\Components\Select::make('projects_id')
                    ->label(TranslationHelper::translateIfNeeded('Projects'))
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
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\Fligh::find($cloneId); 
                            
                            if ($clonedRecord && $clonedRecord->projects) {
                                return $clonedRecord->projects_id;
                            }
                        }

                        $currentTeam = auth()->user()->teams()->first();
                        return $currentTeam ? $currentTeam->id_projects : null;
                    })
                    ->options(Projects::where('teams_id', auth()->user()->teams()->first()->id)
                            ->where('status_visible', '!=', 'archived')
                            ->pluck('case', 'id')
                            )->searchable(),
                ])->columnSpan(1),
                //grid location
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-location'),
                    Forms\Components\Select::make('location_id')
                    // ->relationship('fligh_location', 'name', function (Builder $query) {
                    //     $currentTeamId = auth()->user()->teams()->first()->id;
                    //     $query->whereHas('teams', function (Builder $query) use ($currentTeamId){
                    //         $query->where('teams_id', $currentTeamId);
                    //     });
                    // })
                    ->options(function (callable $get) use ($currentTeamId) {
                        return fligh_location::where('teams_id', $currentTeamId)
                        ->where('status_visible', '!=', 'archived')
                        ->pluck('name', 'id');
                    })
                    ->label(TranslationHelper::translateIfNeeded('Location'))
                    ->searchable()
                    ->required()
                    ->default($defaultData['location_id'] ?? null),
                ])->columnSpan(2),
                //end grid 
                Forms\Components\Hidden::make('customers_id') 
                    ->required()
                    ->default($defaultData['customers_id'] ?? null),
                Forms\Components\TextInput::make('customers_name')
                ->label(TranslationHelper::translateIfNeeded('Customers Name'))    
                    //->relationship('customers', 'name')
                    // ->required()
                    ->disabled()
                    ->afterStateHydrated(function ($state, $component, $record) {
                        if ($record) {
                            $customerId = \DB::table('flighs')
                                ->where('id', $record->id)
                                ->value('customers_id'); 

                            if ($customerId) {
                                $customerName = \DB::table('customers')
                                    ->where('id', $customerId)
                                    ->value('name'); 
                
                                $component->state($customerName);
                            }
                        }
                    })
                    ->default(function (){
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\Fligh::find($cloneId); 
                            
                            if ($clonedRecord && $clonedRecord->customers) {
                                return $clonedRecord->customers->name;
                            }
                        }
                        $currentTeam = auth()->user()->teams()->first();
                        return $currentTeam ? $currentTeam->id_customers  : null;
                    })
                    ->columnSpanFull(),
 
                ])->columns(3),
                Forms\Components\Section::make(TranslationHelper::translateIfNeeded('Personnel'))
                    ->description('')
                    ->schema([
                        Forms\Components\Select::make('users_id')
                        ->label(TranslationHelper::translateIfNeeded('Pilot'))
                        ->relationship('users', 'name', function (Builder $query, callable $get) {
                            $currentTeamId = auth()->user()->teams()->first()->id;
                            $startDate = $get('start_date_flight');
                            $endDate = $get('end_date_flight');
                            $isEdit = $get('id') !== null;
                        
                            if (!$startDate || !$endDate || $isEdit) {
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
                        })->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('instructor', null))
                    ->required()
                    ->options(function () {
                        $currentTeamId = auth()->user()->teams()->first()->id;
                
                        return \App\Models\User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                $query->where('team_id', $currentTeamId);
                            })
                            ->whereHas('roles', function (Builder $query) {
                                $query->where('roles.name', 'Pilot');
                            })
                            ->pluck('name', 'id')
                            ->toArray();

                            if ($startDate && $endDate && $isEdit) {
                                $query->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                                    $query->where(function ($query) use ($startDate, $endDate) {
                                        $query->where('start_date_flight', '<=', $endDate)
                                              ->where('end_date_flight', '>=', $startDate);
                                    });
                                });
                            }
                        
                            if ($selectedUserId) {
                                $query->where('id', '!=', $selectedUserId);
                            }
                        
                            return $query;
                    })
                    ->default(function () {
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\Fligh::find($cloneId); 
                
                            if ($clonedRecord && $clonedRecord->users) {
                                return $clonedRecord->users_id;
                            }
                        }
                    }),
                Forms\Components\Select::make('instructor')
                ->label(TranslationHelper::translateIfNeeded('Instructor (optional)'))
                ->relationship('instructors', 'name', function (Builder $query, callable $get) {
                    $currentTeamId = auth()->user()->teams()->first()->id;
                    $startDate = $get('start_date_flight');
                    $endDate = $get('end_date_flight');
                    $selectedUserId = $get('users_id');
                    $isEdit = $get('id') !== null;
                    
                    dump($startDate, $endDate, $selectedUserId);
                
                    $query->whereHas('teams', function (Builder $query) use ($currentTeamId) {
                        $query->where('team_id', $currentTeamId);
                    })
                    ->whereHas('roles', function (Builder $query) {
                        $query->where('roles.name', 'Pilot');
                    });
                
                    if ($startDate && $endDate && $isEdit) {
                        $query->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                            $query->where(function ($query) use ($startDate, $endDate) {
                                $query->where('start_date_flight', '<=', $endDate)
                                      ->where('end_date_flight', '>=', $startDate);
                            });
                        });
                    }
                
                    if ($selectedUserId) {
                        $query->where('id', '!=', $selectedUserId);
                    }
                
                    return $query;
                })->reactive()
                ->default(function () {
                    $cloneId = request()->query('clone');
                    if ($cloneId) {
                        $clonedRecord = \App\Models\Fligh::find($cloneId); 
            
                        if ($clonedRecord && $clonedRecord->instructors) {
                            return $clonedRecord->instructors->id;
                        }
                    }
                }),
                Forms\Components\TextInput::make('vo')
                ->label(TranslationHelper::translateIfNeeded('VO'))    
                    ->maxLength(255)
                    ->default($defaultData['vo'] ?? null),
                Forms\Components\TextInput::make('po')
                ->label(TranslationHelper::translateIfNeeded('PO'))    
                    ->maxLength(255)
                    ->default($defaultData['po'] ?? null),
                ])->columns(2),
                Forms\Components\Section::make(TranslationHelper::translateIfNeeded('Drone & Equipment'))
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
                    ->label(TranslationHelper::translateIfNeeded('Drones'))
                    // ->relationship('drones', 'name', function (Builder $query, callable $get) {
                    //     $currentTeamId = auth()->user()->teams()->first()->id; 
                    //     $startDate = $get('start_date_flight');
                    //     $endDate = $get('end_date_flight');
                    //     $isEdit = $get('id') !== null; 
                        
                    //     if (!$startDate || !$endDate || $isEdit) {
                    //         return $query->where('teams_id', $currentTeamId)
                    //                  ->where('status', 'airworthy')
                    //                  ->where(function ($query) {
                    //                      $query->doesntHave('maintence_drone')
                    //                            ->orWhereHas('maintence_drone', function ($query) {
                    //                                $query->where('status', 'completed'); 
                    //                            });
                    //                  });
                    //     }
                    
                    //     return $query->where('teams_id', $currentTeamId)
                    //                  ->where('status', 'airworthy')
                    //                  ->where(function ($query) {
                    //                      $query->doesntHave('maintence_drone')
                    //                            ->orWhereHas('maintence_drone', function ($query) {
                    //                                $query->where('status', 'completed'); 
                    //                            });
                    //                  })
                    //                  ->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                    //                      $query->where(function ($query) use ($startDate, $endDate) {
                    //                          $query->where('start_date_flight', '<=', $endDate)
                    //                                ->where('end_date_flight', '>=', $startDate);
                    //                      });
                    //                  });
                    // })
                    ->relationship('drones', 'name')
                    ->saveRelationshipsUsing(function ($state, callable $get) {
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
                                        if ($drone) { // Check if drone exists
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
                    ->preload()
                    ->columnSpanFull()
                    ->options(function (callable $get) {
                        $currentTeamId = auth()->user()->teams()->first()->id;
                        
                        $startDate = $get('start_date_flight');
                        $endDate = $get('end_date_flight');
                        $isEdit = $get('id') !== null;
                        
                        $query = drone::where('teams_id', $currentTeamId)
                            ->where('status', 'airworthy')
                            ->where(function ($query) {
                                $query->doesntHave('maintence_drone')
                                      ->orWhereHas('maintence_drone', function ($query) {
                                          $query->where('status', 'completed');
                                      });
                            });
                        
                        if (!$startDate || !$endDate || $isEdit) {
                            return $query->pluck('name', 'id');
                        }
                
                        return $query->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                            $query->where(function ($query) use ($startDate, $endDate) {
                                $query->where('start_date_flight', '<=', $endDate)
                                      ->where('end_date_flight', '>=', $startDate);
                            });
                        })->pluck('name', 'id');
                    })
                    ->default(function (){
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\Fligh::find($cloneId); 
                            
                            if ($clonedRecord && $clonedRecord->drones) {
                                return $clonedRecord->drones_id;
                            }
                        }
                    }),
                    //end flight
                ])->columnSpan(2), 
                //grid Kits
                Forms\Components\Grid::make(1)->schema([

                Forms\Components\Checkbox::make('show_all_kits') 
                ->label(TranslationHelper::translateIfNeeded('Show All Kits'))
                ->reactive() 
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state){
                        $set('kits_id', null);
                    }
                }),
                    //kits
                Forms\Components\Select::make('kits_id')
                ->label(TranslationHelper::translateIfNeeded('Kits'))
                // ->relationship('kits', 'name', function (Builder $query) {
                //     $currentTeamId = auth()->user()->teams()->first()->id;
                //     $query->whereHas('teams', function (Builder $query) use ($currentTeamId){
                //         $query->where('team_id', $currentTeamId);
                //     });
                // })
                ->relationship('kits', 'name')
                ->options(function (callable $get) use ($currentTeamId) { 
                    $startDate = $get('start_date_flight');
                    $endDate = $get('end_date_flight');
                    $droneId = $get('drones_id');
                    $showAllKits = $get('show_all_kits');
                    $isEdit = $get('id') !== null;

                    if ($showAllKits){
                        return Kits::where('teams_id', $currentTeamId)->pluck('name', 'id');
                    }
                    if (!$startDate || !$endDate || $isEdit) {
                        return Kits::whereHas('teams', function (Builder $query) use ($currentTeamId, $droneId) {
                            $query->where('teams_id', $currentTeamId)
                                  ->when($droneId, function ($query) use ($droneId) {
                                      $query->where('drone_id', $droneId);
                                  });
                        })->pluck('name', 'id'); 
                    }
                    
                    
                    return kits::where('teams_id', $currentTeamId)
                        ->when($droneId, function ($query) use ($droneId){
                            $query->where('drone_id', $droneId);
                        })
                        ->when($startDate && $endDate && $isEdit, function ($query) use ($startDate, $endDate) {
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
                ->default(function () {
                    $cloneId = request()->query('clone');
                    if ($cloneId) {
                        $clonedRecord = \App\Models\Fligh::find($cloneId); 
            
                        if ($clonedRecord && $clonedRecord->kits) {
                            return $clonedRecord->kits_id;
                        }
                    }
                })
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
                        ->label(TranslationHelper::translateIfNeeded('Battery'))
                        ->afterStateHydrated(function ($state, $component, $record) {
                            if ($record) {
                                $kitId = $record->kits_id;
                                $batteriesId = \DB::table('battrei_kits')
                                    ->where('kits_id', $kitId)
                                    ->pluck('battrei_id')
                                    ->toArray(); 

                                if ($batteriesId) {
                                    $batteriesName = \DB::table('battreis')
                                        ->where('id', $batteriesId)
                                        ->pluck('name')
                                        ->toArray(); 
                    
                                    $component->state(implode(', ', $batteriesName));
                                } else {
                                    $component->state(null);
                                }
                            }
                        })        
                        ->helperText(function () {
                            return TranslationHelper::translateIfNeeded('Automatically filled when selecting kits');
                        })                        
                        ->disabled()
                        ->default(function () {
                            $cloneId = request()->query('clone');
                            if ($cloneId) {
                                $clonedRecord = \App\Models\Fligh::find($cloneId);
                    
                                if ($clonedRecord && $clonedRecord->kits) {
                                    $kitId = $clonedRecord->kits_id;
                    
                                    $batteriesId = \DB::table('battrei_kits')
                                        ->where('kits_id', $kitId)
                                        ->pluck('battrei_id')
                                        ->toArray();
                    
                                    if (!empty($batteriesId)) {
                                        $batteriesName = \App\Models\Battrei::whereIn('id', $batteriesId)
                                            ->pluck('name')
                                            ->toArray();
                    
                                        return implode(', ', $batteriesName);
                                    }
                                }
                            }
                    
                            return null;
                        }), 
                Forms\Components\TextInput::make('camera_gimbal')
                ->label(TranslationHelper::translateIfNeeded('Camera/Gimbal')) 
                ->afterStateHydrated(function ($state, $component, $record) {
                    if ($record) {
                        $kitId = $record->kits_id;
                        $equipmentIds = \DB::table('equidment_kits')
                            ->where('kits_id', $kitId)
                            ->pluck('equidment_id')
                            ->toArray(); 

                        if ($equipmentIds) {
                            $eqName = \DB::table('equidments')
                            ->whereIn('id', $equipmentIds)
                                ->whereIn('type', ['camera', 'gimbal'])
                                ->pluck('type')
                                ->toArray(); 
            
                            $component->state(implode(', ', $eqName));
                        } else {
                            $component->state(null);
                        }
                    }
                })               
                        ->helperText(function () {
                            return TranslationHelper::translateIfNeeded('Automatically filled when selecting kits');
                        })                        
                        ->disabled()
                        ->default(function () {
                            $cloneId = request()->query('clone');
                            if ($cloneId) {
                                $clonedRecord = \App\Models\Fligh::find($cloneId);
                    
                                if ($clonedRecord && $clonedRecord->kits) {
                                    $kitId = $clonedRecord->kits_id;
                    
                                    $equipmentIds = \DB::table('equidment_kits')
                                    ->where('kits_id', $kitId)
                                    ->pluck('equidment_id')
                                    ->toArray(); 
                    
                                    if (!empty($equipmentIds)) {
                                        $eqName = \App\Models\Equidment::whereIn('id', $equipmentIds)
                                            ->whereIn('type', ['camera', 'gimbal'])
                                            ->pluck('type')
                                            ->toArray(); 
                    
                                        return implode(', ', $eqName);
                                    }
                                }
                            }
                    
                            return null;
                        }),
                Forms\Components\TextInput::make('others')
                ->afterStateHydrated(function ($state, $component, $record) {
                    if ($record) {
                        $kitId = $record->kits_id;
                        $others = \DB::table('equidment_kits')
                            ->where('kits_id', $kitId)
                            ->pluck('equidment_id')
                            ->toArray(); 

                        if ($others) {
                            $eqothers = \DB::table('equidments')
                            ->whereIn('id', $others)
                                ->whereNotIn('type', ['camera', 'gimbal'])
                                ->pluck('type')
                                ->toArray(); 
            
                            $component->state(implode(', ', $eqothers));
                        } else {
                            $component->state(null);
                        }
                    }
                })          
                        ->helperText(function () {
                            return TranslationHelper::translateIfNeeded('Automatically filled when selecting kits');
                        })                        
                        ->label(TranslationHelper::translateIfNeeded('Others'))
                        ->disabled()
                        ->default(function () {
                            $cloneId = request()->query('clone');
                            if ($cloneId) {
                                $clonedRecord = \App\Models\Fligh::find($cloneId);
                    
                                if ($clonedRecord && $clonedRecord->kits) {
                                    $kitId = $clonedRecord->kits_id;
                    
                                    $equipmentIds = \DB::table('equidment_kits')
                                    ->where('kits_id', $kitId)
                                    ->pluck('equidment_id')
                                    ->toArray(); 
                    
                                    if (!empty($equipmentIds)) {
                                        $eqName = \App\Models\Equidment::whereIn('id', $equipmentIds)
                                            ->whereNotIn('type', ['camera', 'gimbal'])
                                            ->pluck('type')
                                            ->toArray(); 
                    
                                        return implode(', ', $eqName);
                                    }
                                }
                            }
                    
                            return null;
                        }),
                
                //grid battery
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-battery'),


                Forms\Components\Select::make('battreis')
                ->label(TranslationHelper::translateIfNeeded('Battery'))    
                    // ->relationship('battreis', 'name', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;;
                    //     $query->where('teams_id', $currentTeamId);
                    // }),
                    ->relationship('battreis', 'name')
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
                    ->default(function () {
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\Fligh::find($cloneId);
                            if ($clonedRecord && $clonedRecord->battreis) {
                                return $clonedRecord->battreis->pluck('id')->toArray();
                            }
                        }
                        return [];
                    })
                    ->afterStateHydrated(function ($state, $component, $record) {
                        if ($record) {
                            $batteryIds = \DB::table('fligh_battrei')
                                ->where('fligh_id', $record->id)
                                ->pluck('battrei_id')
                                ->toArray();
                            
                            $batteryNames = \DB::table('battreis')
                                ->whereIn('id', $batteryIds)
                                ->pluck('id')
                                ->toArray();
                            $component->state($batteryNames);
                        }
                    })     
                    ->multiple()
                    ->searchable()
                    ->saveRelationshipsUsing(function ($component, $state) {
                        $component->getRecord()->battreis()->sync($state);
                        foreach ($state as $key){
                            battrei::where('id',$key)->increment('initial_Cycle_count');
                        }
                    }),
                    // ->options(function (callable $get) use ($currentTeamId) {
                    //     $flightDate = $get('flight_date'); // Ambil tanggal flight
                
                    //     return battrei::where('teams_id', $currentTeamId)
                    //         ->whereDoesntHave('fligh', function ($query) use ($flightDate) {
                    //             $query->whereDate('date_flight', $flightDate); // Filter kits yang belum digunakan pada tanggal yang sama
                    //         })
                    //         ->pluck('name', 'id');
                    // })
  
                ])->columnSpan(1),                
                //grid equdiment
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-equidment'),
                Forms\Components\Select::make('equidments')
                ->label(TranslationHelper::translateIfNeeded('Equipment'))    
                    // ->relationship('equidments', 'name', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;;
                    //     $query->where('teams_id', $currentTeamId);
                    // }),
                    ->multiple()
                    ->relationship('equidments', 'name')
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
                    ->default(function () {
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\Fligh::find($cloneId);
                            if ($clonedRecord && $clonedRecord->equidments) {
                                return $clonedRecord->equidments->pluck('id')->toArray();
                            }
                        }
                        return [];
                    })
                    ->afterStateHydrated(function ($state, $component, $record) {
                        if ($record) {
                            $equidmentIds = \DB::table('fligh_equidment')
                                ->where('fligh_id', $record->id)
                                ->pluck('equidment_id')
                                ->toArray();
                            
                            // $equidmentNames = \DB::table('equidments')
                            //     ->whereIn('id', $equidmentIds)
                            //     ->pluck('name', 'id')
                            //     ->toArray();

                                // dd($equidmentIds);
                            $component->state($equidmentIds);
                        }
                    })               
                    ->searchable()
                    ->saveRelationshipsUsing(function ($component, $state) {
                        $component->getRecord()->equidments()->sync($state);
                    }),
                  ])->columnSpan(2),

                            // ->whereDoesntHave('fligh', function ($query) use ($flightDate) {
                            //     $query->whereDate('date_flight', $flightDate); // Pastikan equipment tidak digunakan di tanggal flight yang sama
                            // })

                
                
                Forms\Components\TextInput::make('pre_volt')
                ->label(TranslationHelper::translateIfNeeded('Pre Voltage'))    
                    ->numeric()    
                    ->required()
                    ->default($defaultData['pre_volt'] ?? null),
                Forms\Components\TextInput::make('fuel_used')
                ->label(TranslationHelper::translateIfNeeded('Fuel Used'))    
                    ->numeric()    
                    ->required()
                    ->placeholder('0')
                    ->default($defaultData['fuel_used'] ?? null)
                    ->default('1')->columnSpan(2),
                ])->columns(3),
                //Forms\Components\TextInput::make('wheater_id')
                    //->required()
                    //->numeric(),
                Forms\Components\Hidden::make('teams_id')
                    ->default(auth()->user()->teams()->first()->id ?? null),
            ]);
    }

    //filter untuk individual flight
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (request()->get('tableFilters[individual][isActive]') === true) {
            $query->where('users_id', auth()->id());
        }

        return $query;
    }  


    public static function table(Table $table): Table
    { 
        
        return $table
        //edit query untuk action shared un-shared
        ->modifyQueryUsing(function (Builder $query) {
            $userId = auth()->user()->id;
            if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
                return $query;
                
            }else{
                $query->where(function ($query) use ($userId) {
                    $query->where('users_id', $userId);
                })
                ->orWhere(function ($query) use ($userId) {
                    $query->where('users_id', '!=', $userId)->where('shared', 1);
                });
                
                return $query;
                
            }
        })
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name'))
                ->searchable(),
            Tables\Columns\TextColumn::make('start_date_flight')
                ->label(TranslationHelper::translateIfNeeded('Start Flight'))
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('end_date_flight')
                ->label(TranslationHelper::translateIfNeeded('End Flight'))
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('duration')
                ->label(TranslationHelper::translateIfNeeded('Duration')),
            Tables\Columns\TextColumn::make('fligh_location.name')
                ->label(TranslationHelper::translateIfNeeded('Flight Location'))
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('projects.case')
                ->label(TranslationHelper::translateIfNeeded('Projects Case'))
                ->numeric()
                ->url(fn($record) => $record->projects_id ? route('filament.admin.resources.projects.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->projects_id,
                ]) : null)
                ->color(Color::Blue)
                ->sortable(),
            Tables\Columns\TextColumn::make('projects.customers.name')
                ->label(TranslationHelper::translateIfNeeded('Customers Name'))
                ->numeric()
                ->url(fn($record) => $record->customers_id ? route('filament.admin.resources.customers.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->customers_id,
                ]) : null)
                ->color(Color::Blue)
                ->sortable(),
            Tables\Columns\TextColumn::make('users.name')
                ->label(TranslationHelper::translateIfNeeded('Pilot'))
                ->numeric()
                ->url(fn($record) => $record->users_id ? route('filament.admin.resources.users.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->users_id,
                ]) : null)
                ->color(Color::Blue)
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
                Tables\Filters\Filter::make('start_date_flight')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                        Forms\Components\DatePicker::make('from')->label(TranslationHelper::translateIfNeeded('Flight Date From')),
                        Forms\Components\DatePicker::make('until')->label(TranslationHelper::translateIfNeeded('Until')),
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
                    ->label(TranslationHelper::translateIfNeeded('Filter by Project')),
                Tables\Filters\SelectFilter::make('drones_id')
                    ->relationship('drones', 'name', function (Builder $query){
                        $currentTeamId = auth()->user()->teams()->first()->id;;
                        $query->where('teams_id', $currentTeamId);
                    })    
                    ->label(TranslationHelper::translateIfNeeded('Filter by Drones')),
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

                    ->label(TranslationHelper::translateIfNeeded('Filter by Pilot')),
                Tables\Filters\Filter::make('individual')
                    ->query(function (Builder $query) {
                            $query->where('users_id', auth()->id());
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->locked_flight === 'locked'),
                    Tables\Actions\DeleteAction::make(),
                    //Shared action
                    Tables\Actions\Action::make('Shared')->label(TranslationHelper::translateIfNeeded('Shared'))

                    ->hidden(fn ($record) => 
                    ($record->shared == 1) ||
                    !(Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) && 
                    ($record->users_id != Auth()->user()->id))
    
                    ->action(function ($record) {
                        // $record->update(['shared' => 1]);
                        Notification::make()
                        ->title(TranslationHelper::translateIfNeeded('Shared Updated'))
                        ->body(TranslationHelper::translateIfNeeded("Shared successfully changed."))
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
                            ->title(TranslationHelper::translateIfNeeded('Un-Shared Updated '))
                            ->body(TranslationHelper::translateIfNeeded("Un-Shared successfully changed."))
                            ->success()
                            ->send();
                        })->icon('heroicon-m-share'),
                    Tables\Actions\Action::make('lockFlight')
                    ->label(TranslationHelper::translateIfNeeded('Lock'))

                        ->action(function ($record) {
                            $record->update(['locked_flight' => 'locked']);
                            Notification::make()
                                ->title(TranslationHelper::translateIfNeeded('Data Locked'))
                                ->body(TranslationHelper::translateIfNeeded('This record is now locked and cannot be edited.'))
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-s-lock-closed')
                        ->hidden(fn ($record) => $record->locked_flight === 'locked'), 
                    Tables\Actions\Action::make('unlockFlight')->label(TranslationHelper::translateIfNeeded('Unlock'))
                        ->action(function ($record) {
                            $record->update(['locked_flight' => 'unlocked']);
                            Notification::make()
                                ->title(TranslationHelper::translateIfNeeded('Data Un-Locked'))
                                ->body(TranslationHelper::translateIfNeeded('This record is now unlocked and can be edited.'))
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-s-lock-open')
                        ->hidden(fn ($record) => $record->locked_flight === null || $record->locked_flight === 'unlocked')
                        ->visible(fn ($record) => auth()->user()->hasRole(['panel_user'])),  
                    Tables\Actions\Action::make('clone')
                        ->label('Clone')
                        ->icon('heroicon-s-document-duplicate')
                        ->url(function ($record) {
                            return route('filament.admin.resources.flighs.create', [
                                'tenant' => Auth()->user()->teams()->first()->id,
                                'clone' => $record->id,
                            ]);
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
            $table->pagination(function ($query) {
                return $query->appends(request()->query());
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Section::make(TranslationHelper::translateIfNeeded('Flight Detail'))
                ->schema([
                    TextEntry::make('name')->label(TranslationHelper::translateIfNeeded('Name')),
                    TextEntry::make('start_date_flight')->label(TranslationHelper::translateIfNeeded('Date Flight')),
                    TextEntry::make('duration')->label(TranslationHelper::translateIfNeeded('Duration')),
                    TextEntry::make('type')->label(TranslationHelper::translateIfNeeded('Type')),
                    TextEntry::make('ops')->label(TranslationHelper::translateIfNeeded('Ops')),
                    TextEntry::make('landings')->label(TranslationHelper::translateIfNeeded('Landings')),
                    TextEntry::make('fligh_location.name')->label(TranslationHelper::translateIfNeeded('Location')),
                    TextEntry::make('customers.name')->label(TranslationHelper::translateIfNeeded('Customer'))
                        ->url(fn($record) => $record->customers_id ? route('filament.admin.resources.customers.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->customers_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('projects.case')->label(TranslationHelper::translateIfNeeded('Project'))
                        ->url(fn($record) => $record->projects_id ? route('filament.admin.resources.projects.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->projects_id,
                        ]) : null)->color(Color::Blue),
                ])->columns(5),
            Section::make(TranslationHelper::translateIfNeeded('Personnel'))
                ->schema([
                    TextEntry::make('users.name')->label(TranslationHelper::translateIfNeeded('Pilot'))
                        ->url(fn($record) => $record->users_id ? route('filament.admin.resources.users.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->users_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('instructors.name')->label(TranslationHelper::translateIfNeeded('Instructor')),
                    TextEntry::make('vo')->label(TranslationHelper::translateIfNeeded('VO')),
                    TextEntry::make('po')->label(TranslationHelper::translateIfNeeded('PO')),
                ])->columns(4),
            Section::make(TranslationHelper::translateIfNeeded('Drone & Equipments'))
                ->schema([
                    TextEntry::make('kits.name')->label(TranslationHelper::translateIfNeeded('Kits')),
                    TextEntry::make('drones.name')->label(TranslationHelper::translateIfNeeded('Drone'))
                        ->url(fn($record) => $record->users_id ? route('filament.admin.resources.drones.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->users_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('battreis.name')->label(TranslationHelper::translateIfNeeded('Battery'))
                        ->url(fn($record) => $record->users_id ? route('filament.admin.resources.battreis.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->users_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('equidments.name')->label(TranslationHelper::translateIfNeeded('Equipment'))
                        ->url(fn($record) => $record->users_id ? route('filament.admin.resources.equidments.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->users_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('pre_volt')->label(TranslationHelper::translateIfNeeded('Pre-Voltage')),
                    TextEntry::make('fuel_used')->label(TranslationHelper::translateIfNeeded('Fuel Used')),
                ])->columns(4),
            Section::make('')
                ->schema([
                    InfolistView::make('component.tabViewResorce.flight-tab')
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
            'index' => Pages\ListFlighs::route('/'),
            'create' => Pages\CreateFligh::route('/create'),
            'edit' => Pages\EditFligh::route('/{record}/edit'),
            'view' => Pages\ViewFligh::route('/{record}'),
        ];
    }
}
