<?php
            use App\Helpers\TranslationHelper;
            $currentTeamId = Auth()->user()->teams()->first()->id;
            //maintenance
            $maintenance_eq = App\Models\maintence_eq::whereHas('teams', function ($query) use ($currentTeamId) {
                $query->where('id', $currentTeamId);
            })
            ->whereNotIn('status', ['completed'])
            ->whereRaw('DATE(date) < ?', [Carbon\Carbon::now()->format('Y-m-d')])
            ->get();


            $maintenance_drone = App\Models\maintence_drone::whereHas('teams', function ($query) use ($currentTeamId){
            $query->where('id', $currentTeamId);
            })->whereNotIn('status', ['completed'])
            ->whereRaw('DATE(date) < ?', [Carbon\Carbon::now()->format('Y-m-d')])
            ->get();

            //end maintenance

            //flight
            $flight = App\Models\fligh::Where('teams_id',$currentTeamId)->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
            //end flight
            //inventory
            $inventory = App\Models\fligh::Where('teams_id',$currentTeamId)
            ->with(['battreis','equidments'])
            ->limit(20)
            ->get();
            //end inventory

            //document
            $document = App\Models\document::whereHas('teams', function ($query) use ($currentTeamId){
            $query->where('teams.id', $currentTeamId)->where('status_visible', '!=', 'archived');
            })->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
            //end document
            //incident
            $incident = App\Models\incident::whereHas('teams', function ($query) use ($currentTeamId){
            $query->where('id', $currentTeamId);
            })->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            //end incident

            //edi inventory data

            $LastBattrei = DB::table('fligh_battrei')
                    ->select('battrei_id')
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();
                $uniqId = $LastBattrei->pluck('battrei_id')->unique();
                $battreiUsage = App\Models\Battrei::whereHas('teams', function ($query) use ($currentTeamId){
                    $query->where('teams_id', $currentTeamId);
                })->whereIn('id', $uniqId)
                    ->withCount('fligh as usage_count')
                    ->with('fligh')
                    ->get();

            $lastEquipment = DB::table('fligh_equidment')
                    ->select('equidment_id')
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();
                $uniqIdEquipment = $lastEquipment->pluck('equidment_id')->unique();
                $equipmentUsage = App\Models\Equidment::whereHas('teams', function ($query) use ($currentTeamId){
                    $query->where('teams_id', $currentTeamId);
                })->whereIn('id', $uniqIdEquipment)
                    ->withCount('fligh as usage_count')
                    ->with('fligh')
                    ->get();
            //count
            $flightCount = $flight->count();
            $maintenaceDroneCount =$maintenance_drone->count();
            $maintenaceEqCount = $maintenance_eq->count();
            $battreiCount = $battreiUsage->count();
            $equipmentCount = $equipmentUsage->count();
            $documentCount  = $document->count();
            $incidentCount = $incident->count();

            //end count
                        

                // Debugging output
                // dd($battreiUsage);

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Tab buttons styling */
        /* .tab-button {
            background-color: #e2e8f0;
            color: #333;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        } */
        .tab-button:hover {
            background-color: #cbd5e1;
            color: #000;
        }
        .tab-button.active {
            background-color: #3b82f6;
            color: white;
        }

        /* Tab content styling */
        .tab-content {
            display: none;
            padding: 20px;
            margin-top: 10px;
            animation: fadeIn 0.5s ease;
        }
        .tab-content.active {
            display: block;
        }
        .dark-mode {
            background-color: #1a1a1a; /* Dark background */
            color: white; /* Light text color */
        }
        .light-mode {
            background-color: white; /* Light background */
            color: black; /* Dark text color */
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<x-filament-widgets::widget>
<div class="container tab-border-warna">
    <!-- Tab headers -->
    <div class="container mx-auto p-5">
        <div class="flex flex-wrap gap-2 border border-gray-300 rounded-lg p-2 bg-black dark:bg-gray-900">
            <button id="tab0" class="tab-button active text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">{!! TranslationHelper::translateIfNeeded('Summary') !!}</button>
            <button id="tab1" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">{!! TranslationHelper::translateIfNeeded('Flights') !!}</button>
            <button id="tab2" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">{!! TranslationHelper::translateIfNeeded('Maintenance') !!}</button>
            <button id="tab3" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">{!! TranslationHelper::translateIfNeeded('Inventory') !!}</button>
            <button id="tab4" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">{!! TranslationHelper::translateIfNeeded('Documents') !!}</button>
            <button id="tab5" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">{!! TranslationHelper::translateIfNeeded('Incidents') !!}</button>
        </div>

    <!-- Tab content -->
        <div class="content">

                <div id="content0" class="tab-content active">
                    <h2 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('Your Operations Overview') !!}</h2>
                
                    {{-- untuk tampilan 2 atau lebih widgets --}}
                    <div class="space-y-4">
                        <div class="flex flex-wrap justify-center gap-6">
                            <div class="rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-full">
                                @livewire(App\Livewire\FlightDurationChart::class)
                            </div>
                            <div class="rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-full">
                                @livewire(App\Livewire\FlightChart::class)
                            </div>
                        </div>
                        {{-- buttom --}}
                        <div class=""></div>
                        {{-- end buttom --}}
                    </div>
                    {{-- buttom Summary --}}
                    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-yellow-200 dark:bg-gray-900">
                        <h1 class="text-2xl font-bold mb-4 w-full dark:text-gray-700">{!! TranslationHelper::translateIfNeeded('Quick Actions') !!}</h1>
                        <a href="{{route('filament.admin.resources.flighs.create',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-primary-500 dark::bg-primary-600 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base">{!! TranslationHelper::translateIfNeeded('Add New') !!} <br>{!! TranslationHelper::translateIfNeeded('Flights') !!}</button></a>
                        <a href="{{route('filament.admin.resources.users.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-primary-500 dark::bg-primary-600 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base">{!! TranslationHelper::translateIfNeeded('View My') !!} <br>{!! TranslationHelper::translateIfNeeded('Personnel Page') !!}</button></a>
                        <a href="{{route('filament.admin.resources.incidents.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-primary-500 dark::bg-primary-600 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base"{!! TranslationHelper::translateIfNeeded('Log An') !!}> <br>{!! TranslationHelper::translateIfNeeded('Incident') !!}</button></a>
                        <a href="{{route('filament.admin.resources.maintences.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-primary-500 dark::bg-primary-600 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base">{!! TranslationHelper::translateIfNeeded('Check') !!} <br>{!! TranslationHelper::translateIfNeeded('Maintenance Drone') !!}</button></a>
                        <a href="{{route('filament.admin.resources.maintenance-batteries.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-primary-500 dark::bg-primary-600 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base">{!! TranslationHelper::translateIfNeeded('Check') !!} <br>{!! TranslationHelper::translateIfNeeded('Maintenance Equipment') !!}</button></a>
                    </div>
                    {{-- end buttom Summary --}}

                    {{--Summary content--}}

                        @livewire(App\Livewire\Summary::class)

                    {{--End summary content--}}
                </div>

                <div id="content1" class="tab-content">
                    <h2 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('Flight Overview')!!}</h2>
                
                    {{-- untuk tampilan 2 atau lebih widgets --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="rounded-lg shadow-lg p-2 sm:p-4 flex-1 max-w-xs sm:max-w-[100px] md:max-w-[150px] lg:max-w-[200px] w-full dark:bg-gray-900">
                                    @livewire(App\Livewire\TabFlight::class)
                                </div>
                                <div class="rounded-lg shadow-lg p-2 sm:p-4 flex-1 max-w-xs sm:max-w-[100px] md:max-w-[150px] lg:max-w-[200px] w-full dark:bg-gray-900">
                                    @livewire(App\Livewire\TabFlightDrone::class)
                                </div>
                            </div>
                            <div class="rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-full dark:bg-gray-900">
                                @livewire(App\Livewire\TabFlightChartBar::class)
                            </div>
                        </div>
                                {{-- tabel Flight --}}
                            <div class="container mx-auto p-4">
                                <h2 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('Latest Flights (Last 20)')!!}</h2>
                                @if($flightCount < 1)
                                    <h3 class="text font-bold mb-4">{!! TranslationHelper::translateIfNeeded('No Flight History Available.') !!}</h3>
                                @endif

                                @if($flightCount>0)
                                    <div class="mt-4 flex justify-end mb-4">
                                        <a href="{{route('filament.admin.resources.flighs.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">{!! TranslationHelper::translateIfNeeded('View All')!!}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if ($flightCount > 0)
                            @foreach($flight as $item)
                                <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg"">
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Flight Name')!!}</p>
                                        <a href="{{route('filament.admin.resources.flighs.view',
                                            ['tenant' => Auth()->user()->teams()->first()->id,
                                            'record' => $item->id,])}}"><p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name}}</p>
                                        </a>
                                        <div class="flex justify-between items-center rounded">
                                            <p class="text-sm text-gray-700 dark:text-gray-400 border border-gray-300 dark:border-gray-700">{{ $item->duration ?? null }}</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->start_date_flight ?? null }}</p>
                                        </div>
                                        <a href="{{route('filament.admin.resources.users.view',
                                            ['tenant' => Auth()->user()->teams()->first()->id,
                                            'record' => $item->users->id ?? 0])}}"><p class="text-sm text-gray-700 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Pilot :')!!} {{$item->users->name??null}}</p>
                                        </a>
                                    </div>
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone') !!}</p>
                                        <a href="{{route('drone.statistik', ['drone_id' => $item->drones->id ?? null])}}">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->name?? null}}</p>
                                        </a>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->brand?? null}} / {{$item->drones->model??null}}</p>
                                        
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Flight Type')!!}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->type??null}}</p>
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Flight Location') !!}</p>
                                        <a href="{{route('filament.admin.resources.fligh-locations.edit',
                                        ['tenant' => Auth()->user()->teams()->first()->id,
                                        'record' => $item->fligh_location->id ?? 0])}}"><p class="text-sm text-gray-700 dark:text-gray-400">{{$item->fligh_location->name?? null}}</p>
                                    </a>
                                    </div>
                            

                                </div>
                            @endforeach
                        @endif
                        {{-- end tabel --}}
                </div>
                
                <div id="content2" class="tab-content">
                    <h1 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('Maintenance Overview')!!}</h1>
                    <div class="space-y-4">
                        <div class="flex flex-wrap justify-center gap-6">
                            <div class="rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-sm">
                                @livewire(App\Livewire\TabMaintenance::class)
                            </div>
                               {{-- tabel Maintenance --}}
                               <div class="container mx-auto p-4">

                                <h2 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('Maintenance Overdue')!!}</h2>
                                @if(($maintenaceDroneCount + $maintenaceEqCount) < 1)
                                    <h3 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('No Maintenance Overdue Available.') !!}</h3>
                                @endif
                                @if(($maintenaceDroneCount + $maintenaceEqCount) > 0)
                                    <div class="mt-4 flex justify-end mb-4">
                                        <a href="{{route('filament.admin.resources.maintences.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">{!! TranslationHelper::translateIfNeeded('View All')!!}</a>
                                    </div>
                                @endif
                                @if($maintenaceDroneCount > 0)
                                    @foreach($maintenance_drone as $item)
                                    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                                        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Maintenance Name')!!}</p>
                                            <a href="{{route('filament.admin.resources.maintences.edit',
                                                ['tenant' => Auth()->user()->teams()->first()->id,
                                                'record' => $item->id,])}}"><p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name??null}}</p>
                                            </a>
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->date??null}}</p>
                                        </div>
                                    
                                        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Next Scheduled:')!!} <span class="text-sm text-gray-700 dark:text-gray-400">{{$item->date?? null}}</span></p>
                                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                                @php
                                                    $now = Carbon\Carbon::now();
                                                    $formatDate = \Carbon\Carbon::parse($item->date)->format('Y-m-d');
                                                    $daysOverdueDiff = $now->diffInDays($item->date, false);
                                                @endphp

                                                    @if($daysOverdueDiff < 0) 
                                                    @php
                                                        $daysOverdueDiff = abs(intval($daysOverdueDiff));
                                                    @endphp
                                                        <span style="
                                                            display: inline-block;
                                                            background-color: red; 
                                                            color: white; 
                                                            padding: 3px 6px;
                                                            border-radius: 5px;
                                                            font-weight: bold;
                                                        ">
                                                            {!! TranslationHelper::translateIfNeeded('Overdue:')!!} {{ $daysOverdueDiff }} {!! TranslationHelper::translateIfNeeded('days')!!}
                                                        </span>
                                                    </span>
                                                @else
                                                    <span class="bg-green-600 text-white py-1 px-2 rounded">{{ $formatDate }}</span>
                                                @endif
                                            </p>    
                                        </div>
                                    
                                        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone')!!}</p>
                                            <a href="{{route('drone.statistik', ['drone_id' => $item->drone->id ?? 0])}}">
                                                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->name??null}}</p>
                                            </a>
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->brand?? null}}/{{$item->drone->model??null}}</p>    
                                        </div>
                                    
                                        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Technician') !!}</p>
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->technician??null}}</p>
                                        </div>
                                    </div> 
                                    @endforeach
                                @endif
                                @if($maintenaceEqCount > 0)
                                    @foreach($maintenance_eq as $item)
                                        <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Maintenance Name') !!}</p>
                                                <a href="{{route('filament.admin.resources.maintenance-batteries.edit',
                                                ['tenant' => Auth()->user()->teams()->first()->id,
                                                'record' =>$item->id])}}">
                                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name??null}}</p>
                                                </a>
                                                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->date??null}}</p>
                                            </div>
                                        
                                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Next Scheduled:') !!} <span class="text-sm text-gray-700 dark:text-gray-400">{{$item->date?? null}}</span></p>
                                                <p class="text-sm text-gray-700 dark:text-gray-400">
                                                    @php
                                                        $now = Carbon\Carbon::now();
                                                        $formatDate = \Carbon\Carbon::parse($item->date)->format('Y-m-d');
                                                        $daysOverdueDiff = $now->diffInDays($item->date, false);
                                                    @endphp
                                                        @if($daysOverdueDiff < 0) 
                                                        @php
                                                            $daysOverdueDiff = abs(intval($daysOverdueDiff));
                                                        @endphp
                                                            <span style="
                                                                display: inline-block;
                                                                background-color: red; 
                                                                color: white; 
                                                                padding: 3px 6px;
                                                                border-radius: 5px;
                                                                font-weight: bold;
                                                            ">
                                                                {!! TranslationHelper::translateIfNeeded('Overdue:')!!} {{ $daysOverdueDiff }} {!! TranslationHelper::translateIfNeeded('days')!!}
                                                            </span>
                                                        </span>
                                                    @else
                                                        <span class="bg-green-600 text-white py-1 px-2 rounded">{{ $formatDate }}</span>
                                                    @endif
                                                </p>    
                                            </div>
                                        

                                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Battery / Equipment') !!}</p>
                                                    @if($item->equidment_id == null)
                                                        <a href="{{route('battery.statistik', ['battery_id' => $item->battrei_id])}}">
                                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->battrei->name ?? null }}</p>
                                                        </a>
                                                    @else
                                                        <a href="{{route('equipment.statistik', ['equipment_id' => $item->equidment_id])}}">
                                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->equidment->name ?? null }}</p>
                                                        </a>
                                                    @endif
                                                <p class="text-sm text-gray-700 dark:text-gray-400">
                                                    @if($item->equidment == false)
                                                        {!! TranslationHelper::translateIfNeeded('Battery') !!}
                                                    @else
                                                        {{ $item->equidment->type?? null }}
                                                    @endif
                                                </p>
                                            </div>
                                        
                                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Technician')!!}</p>
                                                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->technician??null}}</p>
                                            </div>
                                        </div> 
                                    @endforeach
                                @endif
                            </div>
                            {{-- end tabel Maintenance --}}
                        </div>
                    </div>
                </div>
                
                
                <div id="content3" class="tab-content">
                    <h1 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('Inventory Overview')!!}</h1>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 justify-center">
                            <div class="rounded-lg shadow-lg p-4 max-w-full">
                                @livewire(App\Livewire\TabInventoryDrone::class)
                            </div>
                            <div class="rounded-lg shadow-lg p-4 max-w-full">
                                @livewire(App\Livewire\TabInventoryBatteri::class)
                            </div>
                            <div class="rounded-lg shadow-lg p-4 max-w-full">
                                @livewire(App\Livewire\TabInventoryEquidment::class)
                            </div>
                            <div class="rounded-lg shadow-lg p-4 max-w-full">
                                @livewire(App\Livewire\TabInventoryLocation::class)
                            </div>
                        </div>
                    </div>
                        {{-- tabel Inventory --}}
                        <div class="container mx-auto p-4">

                        <h2 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('Latest Battery / Equipment Used (Last 20)') !!}</h2>
                        @if(($battreiCount + $equipmentCount) < 1)
                            <h3 class="text font-bold mb-4">{!! TranslationHelper::translateIfNeeded('No Battery/Equipment Usage History') !!}</h3>
                        @endif
                        @if(($battreiCount + $equipmentCount) > 0)
                            <div class="mt-4 flex justify-end mb-4">
                                <a href="{{route('filament.admin.resources.battreis.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">{!! TranslationHelper::translateIfNeeded('View All') !!}</a>
                            </div>
                        @endif
                        @if($battreiCount > 0)
                            @foreach($battreiUsage as $item)
                                <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Item:') !!}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">
                                        <a href="{{route('battery.statistik', ['battery_id' => $item->id])}}">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->name ?? 'No Battery' }} <br> {!! TranslationHelper::translateIfNeeded('Batteries') !!}</p>
                                        </a>
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Flight') !!}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400"></p>
                                        <div class="flex justify-between items-center rounded">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->usage_count}} {!! TranslationHelper::translateIfNeeded('Flight') !!}</p> 
                        
                                            @if($item->fligh->isNotEmpty())
                                            
                                            @php
                                                // Menghitung total durasi dalam detik
                                                $totalDurationInSeconds = $item->fligh->sum(function ($flight) {
                                                    list($hours, $minutes, $seconds) = explode(':', $flight->duration);
                                                    return ($hours * 3600) + ($minutes * 60) + $seconds;
                                                });
                        
                                                // Menghitung total jam, menit, dan detik
                                                $totalHours = floor($totalDurationInSeconds / 3600);
                                                $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
                                                $totalSeconds = $totalDurationInSeconds % 60;
                                                // Format total durasi
                                                $formattedTotalDuration = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);
                                            @endphp
                        
                                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                            {!! TranslationHelper::translateIfNeeded('Total Duration:') !!} {{ $formattedTotalDuration }} <!-- Menampilkan durasi yang diformat -->
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-500">null</p>
                                        @endif
                                        </div>      
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Serial #')!!}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">
                                                {{ $item->serial_I ?? 'N/A' }}
                                        </p>
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('For Drone')!!}</p>
                                            <a href="{{route('drone.statistik', ['drone_id' => $item->drone->id ?? 0])}}">
                                                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->name??null}}</p>
                                            </a>
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->brand??null}} - {{$item->drone->model??null}}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        @if($equipmentCount > 0)
                            @foreach($equipmentUsage  as $item)
                                <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Item:')!!}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">
                                            <a href="{{route('equipment.statistik', ['equipment_id' => $item->id])}}">
                                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->name ?? 'No Equipment' }} <br> {{$item->type ?? 'test'}}</p>
                                            </a>
                                        </p>
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Flight')!!}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400"></p>
                                        <div class="flex justify-between items-center rounded">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->usage_count}} {!! TranslationHelper::translateIfNeeded('Flight')!!}</p> 
                        
                                            @if($item->fligh->isNotEmpty())
                                            
                                            @php
                                                // Menghitung total durasi dalam detik
                                                $totalDurationInSeconds = $item->fligh->sum(function ($flight) {
                                                    list($hours, $minutes, $seconds) = explode(':', $flight->duration);
                                                    return ($hours * 3600) + ($minutes * 60) + $seconds;
                                                });
                        
                                                // Menghitung total jam, menit, dan detik
                                                $totalHours = floor($totalDurationInSeconds / 3600);
                                                $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
                                                $totalSeconds = $totalDurationInSeconds % 60;
                        
                                                // Format total durasi
                                                $formattedTotalDuration = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);
                                            @endphp
                        
                                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                            {!! TranslationHelper::translateIfNeeded('Total Duration:')!!} {{ $formattedTotalDuration }} <!-- Menampilkan durasi yang diformat -->
                                            </p>
                                        @else
                                            <p class="text-sm text-gray-500">null</p>
                                        @endif
                                        </div>      
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Serial #')!!}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">
                                                {{ $item->serial ?? 'N/A' }}
                                        </p>
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('For Drone')!!}</p>
                                            <a href="{{route('drone.statistik', ['drone_id' => $item->drones->id ?? 0])}}">
                                                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->name??null}}</p>
                                            </a>                 
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->brand??null}} - {{$item->drones->model??null}}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        </div>
                    {{-- end tabel --}}      
                </div>

                <div id="content4" class="tab-content">
                    {{-- tabel Document --}}

                    <div class="container mx-auto p-4">
                        <h2 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('Document Overview')!!}</h2>
                        @if($documentCount < 1)
                            <h3 class="text font-bold mb-4">{!! TranslationHelper::translateIfNeeded('No document found')!!}</h3>
                        @endif
                        @if($documentCount > 0)
                            <div class="mt-4 flex justify-end mb-4">
                                <a href="{{route('filament.admin.resources.documents.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">{!! TranslationHelper::translateIfNeeded('View All')!!}</a>
                            </div>
                        @endif
                        @if($documentCount > 0)
                            @foreach($document as $item)
                                <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                                    
                                    <!-- Kolom Name -->
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Name:')!!}</p>
                                        <a href="{{route('filament.admin.resources.documents.edit',['tenant' => Auth()->user()->teams()->first()->id , 'record' => $item->id])}}">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->name ?? TranslationHelper::translateIfNeeded('No Name') }}</p>
                                        </a>
                                    </div>
                            
                                    <!-- Kolom Owner -->
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Owner:')!!}</p>
                                        <a href="{{route('filament.admin.resources.users.view',['tenant' => Auth()->user()->teams()->first()->id, 'record'=>$item->users->id])}}">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->users->name ??  TranslationHelper::translateIfNeeded('No Owner') }}</p>
                                        </a>
                                    </div>
                            
                                    <!-- Kolom Scope -->
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Scope:')!!}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->scope ??  TranslationHelper::translateIfNeeded('No Scope') }}</p>
                                    </div>
                            
                                    <!-- Kolom Type -->
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Type:')!!}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->type ??  TranslationHelper::translateIfNeeded('No Type') }}</p>
                                    </div>
                            
                                    <!-- Kolom Link -->
                                    <div class="flex-1 min-w-[150px] mb-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Link:')!!}</p>
                                        <a href="/storage/{{ $item->doc }}" target="_blank" rel="noopener noreferrer" 
                                        class="inline-block text-sm bg-orange-500 text-white rounded px-3 py-2 hover:bg-orange-600"
                                        style="background-color:#ff8303;">
                                            {!! TranslationHelper::translateIfNeeded('Open Document')!!}
                                        </a>
                                    </div>
                            
                                </div>
                            @endforeach
                        @endif
                        </div>
                    {{-- end tabel --}}  
                </div>

                <div id="content5" class="tab-content">
                    {{-- tabel INcident --}}
                    <div class="container mx-auto p-4">
                        <h2 class="text-2xl font-bold mb-4">{!! TranslationHelper::translateIfNeeded('Recent Incidents (Last 10)')!!}</h2>
                        @if($incidentCount < 1)
                            <h3 class="text font-bold mb-4">{!! TranslationHelper::translateIfNeeded('No incidents found')!!}</h3>
                        @endif
                        @if($incidentCount > 0)
                            <div class="mt-4 flex justify-end mb-4">
                                <a href="{{route('filament.admin.resources.incidents.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">{!! TranslationHelper::translateIfNeeded('View All')!!}</a>
                            </div>
                        @endif
                        @if($incidentCount > 0)
                            @foreach($incident as $item)

                                <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-4 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                                    
                                    <!-- Kolom Cause dan Status -->
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Cause:')!!}</p>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <a href="{{route('filament.admin.resources.incidents.edit',['tenant'=>Auth()->user()->teams()->first()->id,'record'=>$item->id])}}">
                                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->cause }}</p>
                                                </a>
                                                <p class="text-xs text-gray-500 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Date:')!!} {{ $item->incident_date }}</p>
                                            </div>
                                            <span class="px-2 py-1 rounded text-white text-xs {{ $item->status == 0 ? 'bg-green-500' : 'bg-red-500' }}">
                                                @if($item->status == 0)
                                                    Closed
                                                @else
                                                    Under Review
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                            
                                    <!-- Kolom Drone Name -->
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone:')!!}</p>
                                        <a href="{{route('drone.statistik', ['drone_id' => $item->drone->id ?? 0])}}">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->drone->name ??  TranslationHelper::translateIfNeeded('No Drone') }}</p>
                                        </a>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->drone->brand ??  TranslationHelper::translateIfNeeded('No Drone') }} / {{$item->drone->model}}</p>
                                    </div>
                            
                                    <!-- Kolom Personnel Involved -->
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Personnel Involved:')!!}</p>
                                        <a href="{{route('filament.admin.resources.users.view',['tenant'=>Auth()->user()->teams()->first()->id, 'record'=> $item->users->id ?? 0])}}">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->users->name ??  TranslationHelper::translateIfNeeded('No Personnel') }}</p>
                                        </a>
                                    </div>
                            
                                    <!-- Kolom Location -->
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Location:')!!}</p>
                                        <a href="{{route('filament.admin.resources.fligh-locations.edit',['tenant' =>Auth()->user()->teams()->first()->id,'record'=>$item->fligh_locations->id ?? 0])}}">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->fligh_locations->name ??  TranslationHelper::translateIfNeeded('No Location') }}</p>
                                        </a>
                                    </div>
                            
                                    <!-- Kolom Project -->
                                    <div class="flex-1 min-w-[150px] mb-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Project:')!!}</p>
                                        <a href="{{route('filament.admin.resources.projects.view',['tenant'=>Auth()->user()->teams()->first()->id,'record'=>$item->project->id ?? 0])}}">
                                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->project->case ??  TranslationHelper::translateIfNeeded('No Project') }}</p>
                                        </a>
                                    </div>
                            
                                </div>
                                
                            @endforeach
                        @endif
                    </div>
                    {{-- end tabel --}}  
                </div>
        </div>
    </div>
</div>

<script>
    // JavaScript untuk handle content tab aktif dan tidak
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {

            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        
            button.classList.add('active');
            const contentId = `content${button.id.charAt(button.id.length - 1)}`;
            document.getElementById(contentId).classList.add('active');
        });
    });
</script>
</x-filament-widgets::widget>

