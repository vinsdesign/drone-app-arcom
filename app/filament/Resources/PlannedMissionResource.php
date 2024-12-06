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
use App\Helpers\TranslationHelper;

class PlannedMissionResource extends Resource
{
    protected static ?string $model = PlannedMission::class;
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static ?string $tenantRelationshipName = 'PlannedMission';
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document';
    // public static ?string $navigationGroup = 'flight';
    public static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::where('status','!=','append')->Where('teams_id',$teamID)->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Planned Mission');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Planned Mission');
    }
    public static function getNavigationItems(): array
    {
        $user = auth()->user();
        if ($user && !$user->hasRole(['super_admin', 'panel_user'])) {
            return [];
        }
        return parent::getNavigationItems();
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;;
        $cloneId = request()->query('clone');
        $defaultData = [];

        if ($cloneId) {
            $record = PlannedMission::find($cloneId);
            if ($record) {
                $defaultData = $record->toArray();
                $defaultData['name'] = $record->name . ' - CLONE';
            }
        }
        return $form
            ->schema([
                Forms\Components\Section::make(TranslationHelper::translateIfNeeded('Mission Details'))
                    ->description('')
                    ->schema([
                Forms\Components\TextInput::make('name')
                ->label(TranslationHelper::translateIfNeeded('Mission Name'))    
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
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
                    ->default(function () {
                        $cloneId = request()->query('clone'); 
                        if ($cloneId) {
                            $clonedRecord = \App\Models\PlannedMission::find($cloneId); 
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
                    ->columnSpan(2)
                    ->default(function () {
                        $cloneId = request()->query('clone'); 
                        if ($cloneId) {
                            $clonedRecord = \App\Models\PlannedMission::find($cloneId); 
                            return $clonedRecord ? $clonedRecord->ops : null; 
                        }
                    }),
                Forms\Components\TextInput::make('landings')
                ->label(TranslationHelper::translateIfNeeded('Landings'))    
                    ->required()
                    ->default('1')
                    ->numeric()
                    ->default($defaultData['landings'] ?? null),
                Forms\Components\Grid::make(1)->schema([
                    view::make('component.button-project')->extraAttributes(['class' => 'mr-6 custom-spacing']),
                    Forms\Components\Select::make('projects_id')
                    ->relationship('projects', 'case')
                    ->label(TranslationHelper::translateIfNeeded('Projects'))
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
                            $clonedRecord = \App\Models\PlannedMission::find($cloneId); 
                            
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
                    view::make('component.button-location'),
                    Forms\Components\Select::make('location_id')
                    ->options(function (callable $get) use ($currentTeamId) {
                        return fligh_location::where('teams_id', $currentTeamId)
                        ->where('status_visible', '!=', 'archived')
                        ->pluck('name', 'id');
                    })
                    ->label(TranslationHelper::translateIfNeeded('Location'))
                    ->searchable()
                    ->required()
                    ->default(function (){
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\PlannedMission::find($cloneId); 
                            
                            if ($clonedRecord && $clonedRecord->fligh_location) {
                                return $clonedRecord->location_id;
                            }
                        }
                    }),
                ])->columnSpan(2),
                //end grid 
                Forms\Components\Hidden::make('customers_id') 
                    ->required()
                    ->default($defaultData['customers_id'] ?? null),
                Forms\Components\TextInput::make('customers_name')
                ->label(TranslationHelper::translateIfNeeded('Customer Name'))
                ->afterStateHydrated(function ($state, $component, $record) {
                    if ($record) {
                        $customerId = \DB::table('planned_missions')
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
                    ->disabled()
                    ->helperText(function () {
                        return TranslationHelper::translateIfNeeded('Automatically filled when selecting projects');
                    }) 
                    ->default(function (){
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\PlannedMission::find($cloneId); 
                            
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
                        })
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
                        
                            // Filter berdasarkan selectedUserId jika ada
                            if ($selectedUserId) {
                                $query->where('id', '!=', $selectedUserId);
                            }
                        
                            return $query;
                    })
                    ->default(function () {
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\PlannedMission::find($cloneId); 
                
                            if ($clonedRecord && $clonedRecord->users) {
                                return $clonedRecord->users_id;
                            }
                        }
                    }),
                ])->columns(2),
                Forms\Components\Section::make(TranslationHelper::translateIfNeeded('Drone & Equipment'))
                    ->description('')
                    ->schema([   
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-drone'),
                    Forms\Components\Select::make('drones_id')
                    ->required()
                    ->label(TranslationHelper::translateIfNeeded('Drones'))
                    // ->options(function (callable $get) use ($currentTeamId) { 
                    //     $startDate = $get('start_date_flight');
                    //     $endDate = $get('end_date_flight');
                    
                    //     return drone::where('teams_id', $currentTeamId)
                    //         ->where('status', 'airworthy')
                    //         ->where(function ($query) {
                    //             $query->doesntHave('maintence_drone')
                    //                 ->orWhereHas('maintence_drone', function ($query) {
                    //                     $query->where('status', 'completed'); 
                    //                 });
                    //         })
                    //         ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    //             $query->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                    //                 $query->where(function ($query) use ($startDate, $endDate) {
                    //                     $query->where(function ($query) use ($startDate, $endDate) {
                    //                         $query->where('start_date_flight', '<=', $endDate)
                    //                               ->where('end_date_flight', '>=', $startDate);
                    //                     });
                    //                 });
                    //             });
                    //         })
                    //         ->pluck('name', 'id');
                    // })
                    ->options(function (callable $get) {
                        // Ambil ID tim saat ini
                        $currentTeamId = auth()->user()->teams()->first()->id;
                        
                        // Ambil start dan end date dari form
                        $startDate = $get('start_date_flight');
                        $endDate = $get('end_date_flight');
                        $isEdit = $get('id') !== null; // Periksa apakah ini dalam mode edit
                        
                        // Mulai query untuk mendapatkan drone yang sesuai
                        $query = drone::where('teams_id', $currentTeamId)
                            ->where('status', 'airworthy')
                            ->where(function ($query) {
                                $query->doesntHave('maintence_drone')
                                      ->orWhereHas('maintence_drone', function ($query) {
                                          $query->where('status', 'completed');
                                      });
                            });
                        
                        // Jika start_date atau end_date kosong, atau ini dalam mode edit, tanpa filter penerbangan
                        if (!$startDate || !$endDate || $isEdit) {
                            return $query->pluck('name', 'id');
                        }
                
                        // Jika ada start_date dan end_date, pastikan drone tidak bertabrakan dengan penerbangan lain
                        return $query->whereDoesntHave('fligh', function ($query) use ($startDate, $endDate) {
                            $query->where(function ($query) use ($startDate, $endDate) {
                                $query->where('start_date_flight', '<=', $endDate)
                                      ->where('end_date_flight', '>=', $startDate);
                            });
                        })->pluck('name', 'id');
                    })
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
                    ->preload()
                    ->columnSpanFull()
                    ->default(function (){
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\PlannedMission::find($cloneId); 
                            
                            if ($clonedRecord && $clonedRecord->drones) {
                                return $clonedRecord->drones_id;
                            }
                        }
                    }),
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
                Forms\Components\Select::make('kits_id')
                ->label(TranslationHelper::translateIfNeeded('Kits'))
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
                ->relationship('kits', 'name')
                ->default(function () {
                    $cloneId = request()->query('clone');
                    if ($cloneId) {
                        $clonedRecord = \App\Models\PlannedMission::find($cloneId); 
            
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
                        $batteriesId = \DB::table('planned_missions')
                            ->where('kits_id', $kitId)
                            ->pluck('id')
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
                                $clonedRecord = \App\Models\PlannedMission::find($cloneId);
                    
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
                        $equipmentIds = \DB::table('planned_missions')
                            ->where('kits_id', $kitId)
                            ->pluck('id')
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
                                $clonedRecord = \App\Models\PlannedMission::find($cloneId);
                    
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
                        $equipmentIds = \DB::table('planned_missions')
                            ->where('kits_id', $kitId)
                            ->pluck('id')
                            ->toArray(); 

                        if ($equipmentIds) {
                            $eqName = \DB::table('equidments')
                            ->whereIn('id', $equipmentIds)
                                ->whereNotIn('type', ['camera', 'gimbal'])
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
                        ->label(TranslationHelper::translateIfNeeded('Others'))
                        ->disabled()
                        ->default(function () {
                            $cloneId = request()->query('clone');
                            if ($cloneId) {
                                $clonedRecord = \App\Models\PlannedMission::find($cloneId);
                    
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
                    })
                    ->relationship('battreis', 'name')
                    ->default(function () {
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\PlannedMission::find($cloneId);
                            if ($clonedRecord && $clonedRecord->battreis) {
                                return $clonedRecord->battreis->pluck('id')->toArray();
                            }
                        }
                        return [];
                    }),
                ])->columnSpan(1),

                //grid equdiment
                Forms\Components\Grid::make(1)->schema([
                    View::make('component.button-equidment'),
                Forms\Components\Select::make('equidments')
                ->label(TranslationHelper::translateIfNeeded('Equipment'))    
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
                    ->relationship('equidments', 'name')      
                    ->default(function () {
                        $cloneId = request()->query('clone');
                        if ($cloneId) {
                            $clonedRecord = \App\Models\PlannedMission::find($cloneId);
                            if ($clonedRecord && $clonedRecord->equidments) {
                                return $clonedRecord->equidments->pluck('id')->toArray();
                            }
                        }
                        return [];
                    })          
                    ->searchable()
                    ->saveRelationshipsUsing(function ($component, $state) {
                        $component->getRecord()->equidments()->sync($state);
                    }),
                  ])->columnSpan(2),

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
                    ->default('1')->columnSpan(2)
                    ->default($defaultData['fuel_used'] ?? null),
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
                ->label(TranslationHelper::translateIfNeeded('Mission Name'))
                ->searchable(),
            Tables\Columns\TextColumn::make('start_date_flight')
                ->label(TranslationHelper::translateIfNeeded('Start Flight'))
                ->dateTime()
                ->sortable()
                ->formatStateUsing(function ($state, $record) {
                    $daysOverdue = Carbon::parse($state);
                    $now = Carbon::now();
                    $formatDate = $daysOverdue->format('Y-m-d H:i:s');
        
                    if ($record->status !== 'completed' && $record->status !== 'append') {
                        $daysOverdueDiff = $now->diffInDays($daysOverdue, false);
        
                        if ($daysOverdueDiff < 0){
                            $daysOverdueDiff = abs(intval($daysOverdueDiff));

                            $overdueLabel = TranslationHelper::translateIfNeeded('Expired');
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
            Tables\Columns\TextColumn::make('end_date_flight')
                ->label(TranslationHelper::translateIfNeeded('End Flight'))
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('duration')->label(TranslationHelper::translateIfNeeded('Duration')),
            Tables\Columns\TextColumn::make('fligh_location.name')
                ->label(TranslationHelper::translateIfNeeded('Location'))
                ->numeric()
                ->url(fn($record) => $record->location_id? route('filament.admin.resources.fligh-locations.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->location_id,
                ]) : null)->color(Color::Blue)
                ->sortable(),
            Tables\Columns\TextColumn::make('projects.case')
                ->label(TranslationHelper::translateIfNeeded('Projects'))
                ->numeric()
                ->url(fn($record) => $record->projects_id? route('filament.admin.resources.projects.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->projects_id,
                ]) : null)->color(Color::Blue)
                ->sortable(),
            Tables\Columns\TextColumn::make('projects.customers.name')
                ->label(TranslationHelper::translateIfNeeded('Customers'))
                ->numeric()
                ->url(fn($record) => $record->customers_id? route('filament.admin.resources.customers.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->customers_id,
                ]) : null)->color(Color::Blue)
                ->sortable(),
            Tables\Columns\TextColumn::make('users.name')
                ->label(TranslationHelper::translateIfNeeded('Pilot'))
                ->numeric()
                ->url(fn($record) => $record->users_id? route('filament.admin.resources.users.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->users_id,
                ]) : null)->color(Color::Blue)
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(TranslationHelper::translateIfNeeded('Created at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label(TranslationHelper::translateIfNeeded('Updated'))
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
                    ->options(function () {
                        $currentTeamId = auth()->user()->teams()->first()->id;
                        return \App\Models\Projects::where('teams_id', $currentTeamId)
                            ->pluck('case', 'id')
                            ->toArray();
                    })    
                    ->searchable()
                    ->label(TranslationHelper::translateIfNeeded('Filter by Project')),
                Tables\Filters\SelectFilter::make('drones_id')
                    ->options(function () {
                        $currentTeamId = auth()->user()->teams()->first()->id;
                        return \App\Models\drone::where('teams_id', $currentTeamId)
                            ->pluck('name', 'id')
                            ->toArray();
                    })    
                    ->searchable()
                    ->label(TranslationHelper::translateIfNeeded('Filter by Drones')),
                Tables\Filters\SelectFilter::make('users_id')
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
                    })
                    ->searchable()
                    ->label(TranslationHelper::translateIfNeeded('Filter by Pilot')),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'append' => 'Append',
                        'planned' => 'Planned',
                        'completed' => 'Completed',
                        'cancel' => 'Cancel',
                    ])
                    ->default('planned')
                    ->label(TranslationHelper::translateIfNeeded('Status'))
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                //append flight
                Action::make('append_flight')
                ->label(fn ($record) => $record->status === 'append' ? TranslationHelper::translateIfNeeded('Already Append') : TranslationHelper::translateIfNeeded('Append Flight'))
                ->modalHeading(TranslationHelper::translateIfNeeded('Append this Planned Mission to Flight'))
                ->modalSubmitActionLabel(TranslationHelper::translateIfNeeded('Append'))
                ->action(function ($record) {
                    $flights = fligh::create([ 
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
                        'kits_id' => $record->kits_id ?: null,
                        'users_id' => $record->users_id,
                        'vo' => null, 
                        'po' => null, 
                        'instructor' => null,
                        'drones_id' => $record->drones_id,
                        'pre_volt' => $record->pre_volt,
                        'fuel_used' => $record->fuel_used,
                        'teams_id' => $record->teams_id,
                    ]);
                    if($flights){
                        $flights->teams()->attach($record->teams_id);
                        $flights->battreis()->attach($record->battreis);
                        $flights->equidments()->attach($record->equidments);
                        
                    }
                    
                    $record->update(['status' => 'append']);
                    Notification::make()
                        ->title(TranslationHelper::translateIfNeeded('Flight Added'))
                        ->body(TranslationHelper::translateIfNeeded("Planned mission successfully added to Flight."))
                        ->success()
                        ->send();
                })
                ->icon('heroicon-s-document-plus')
                ->disabled(fn ($record) => $record->status === 'append')
                ->visible(fn ($record) => $record->status !== 'cancel')
                // ->button()
                ->requiresConfirmation(),
                //end append flight 

                Action::make('finalize')
                ->label(TranslationHelper::translateIfNeeded('Finalize'))
                ->modalHeading(TranslationHelper::translateIfNeeded('Complete or Cancel this Mission'))
                ->modalSubmitActionLabel(TranslationHelper::translateIfNeeded('Submit'))
                ->action(function ($record, array $data) {
                    $record->update(['status' => $data['status']]);

                    Notification::make()
                        ->title(TranslationHelper::translateIfNeeded('Status Updated'))
                        ->body(TranslationHelper::translateIfNeeded("Status successfully changed to {$data['status']}."))
                        ->success()
                        ->send();
                })
                ->form([
                    Forms\Components\Radio::make('status')
                        ->label(TranslationHelper::translateIfNeeded('Choose Status'))
                        ->options([
                            'completed' => 'Completed',
                            'cancel' => 'Cancel',
                        ])
                        
                        ->required(),
                ])
                ->visible(fn ($record) => !in_array($record->status, ['completed', 'cancel', 'append']))
                // ->button(),
                ->icon('heroicon-s-document-check'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('clone')
                        ->label('Clone')
                        ->icon('heroicon-s-document-duplicate')
                        ->url(function ($record) {
                            return route('filament.admin.resources.planned-missions.create', [
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
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist

        ->schema([
            Section::make(TranslationHelper::translateIfNeeded('Mission Details'))
                ->schema([
                    TextEntry::make('name')->label(TranslationHelper::translateIfNeeded('Name')),
                    TextEntry::make('start_date_flight')->label(TranslationHelper::translateIfNeeded('Date Flight')),
                    TextEntry::make('duration')->label(TranslationHelper::translateIfNeeded('Duration')),
                    TextEntry::make('type')->label(TranslationHelper::translateIfNeeded('Type')),
                    TextEntry::make('ops')->label(TranslationHelper::translateIfNeeded('Ops')),
                    TextEntry::make('landings')->label(TranslationHelper::translateIfNeeded('Landings')),
                    TextEntry::make('fligh_location.name')->label(TranslationHelper::translateIfNeeded('Location'))
                        ->url(fn($record) => $record->location_id ? route('filament.admin.resources.fligh-locations.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->location_id,
                        ]) : null)->color(Color::Blue),
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
                ]),
            
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
                    TextEntry::make('status')->label(TranslationHelper::translateIfNeeded('Status'))
                        ->color(fn ($record) => match ($record->status){
                            'completed' => Color::Green,
                            'cancel' => Color::Red,
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
            'view' => Pages\ViewPlannedMission::route('/{record}'),
        ];
    }
}
