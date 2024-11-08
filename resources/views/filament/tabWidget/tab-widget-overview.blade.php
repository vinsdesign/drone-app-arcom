<?php
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
            $query->where('id', $currentTeamId);
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
                $battreiUsage = App\Models\Battrei::whereIn('id', $uniqId)
                    ->withCount('fligh as usage_count')
                    ->with('fligh')
                    ->get();

            $lastEquipment = DB::table('fligh_equidment')
                    ->select('equidment_id')
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();
                $uniqIdEquipment = $lastEquipment->pluck('equidment_id')->unique();
                $equipmentUsage = App\Models\Equidment::whereIn('id', $uniqIdEquipment)
                    ->withCount('fligh as usage_count')
                    ->with('fligh')
                    ->get();
                        

                // Debugging output
                // dd($battreiUsage);

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Tab buttons styling */
        .tab-button {
            background-color: #e2e8f0;
            color: #333;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
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
        <div class="flex flex-wrap gap-2 border border-gray-300 rounded-lg p-2 bg-black">
            <button id="tab0" class="tab-button active text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">Summary</button>
            <button id="tab1" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">Flights</button>
            <button id="tab2" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">Maintenance</button>
            <button id="tab3" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">Inventory</button>
            <button id="tab4" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">Documents</button>
            <button id="tab5" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto mb-2 mt-2">Incidents</button>
        </div>

    <!-- Tab content -->
        <div class="content">

                <div id="content0" class="tab-content active">
                    <h2 class="text-2xl font-bold mb-4">Your Operations Overview</h2>
                
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
                    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-yellow-200">
                        <h1 class="text-2xl font-bold mb-4 w-full dark:text-gray-700">Quick Actions</h1>
                        <a href="{{route('filament.admin.resources.flighs.create',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-yellow-500 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base">Add New <br>Flights</button></a>
                        <a href="{{route('filament.admin.resources.users.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-yellow-500 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base">View My <br>Personnel Page</button></a>
                        <a href="{{route('filament.admin.resources.incidents.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-yellow-500 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base">Log An <br>Incident</button></a>
                        <a href="{{route('filament.admin.resources.maintences.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-yellow-500 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base">Check <br>Maintenance Drone</button></a>
                        <a href="{{route('filament.admin.resources.maintenance-batteries.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="mb-2"><button id="" class="text-white bg-yellow-500 hover:bg-yellow-400 px-4 py-2 rounded text-sm sm:text-base">Check <br>Maintenance Equipment</button></a>
                    </div>
                    
                    {{-- end buttom Summary --}}
                </div>

                <div id="content1" class="tab-content">
                    <h2 class="text-2xl font-bold mb-4">Flight Overview</h2>
                
                    {{-- untuk tampilan 2 atau lebih widgets --}}
                    <div class="space-y-4">
                        <div class="flex flex-col md:flex-row md:flex-nowrap justify-center gap-4 md:gap-6">
                            <div class="rounded-lg shadow-lg p-2 sm:p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-sm w-full">
                                @livewire(App\Livewire\TabFlight::class)
                            </div>
                            <div class="rounded-lg shadow-lg p-2 sm:p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-sm w-full">
                                @livewire(App\Livewire\TabFlightDrone::class)
                            </div>
                            <div class="rounded-lg shadow-lg p-2 sm:p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-sm w-full">
                                @livewire(App\Livewire\TabFlightChartBar::class)
                            </div>
                        </div>
                        
                        
                                {{-- tabel Flight --}}
                                <div class="container mx-auto p-4">
                                <h2 class="text-2xl font-bold mb-4">Latest Flights (Last 20)</h2>
                                <div class="mt-4 flex justify-end mb-4">
                                    <a href="{{route('filament.admin.resources.flighs.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                                </div>
                            </div>
                        </div>
                        @foreach($flight as $item)
                            <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg"">
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Flight Name</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name}}</p>
                                    <div class="flex justify-between items-center rounded">
                                        <p class="text-sm text-gray-700 dark:text-gray-400 border border-gray-300 dark:border-gray-700">{{ $item->duration ?? null }}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->start_date_flight ?? null }}</p>
                                    </div>
                                    
                                    <p class="text-sm text-gray-700 dark:text-gray-400">Pilot : {{$item->users->name??null}}</p>
                                </div>
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Drone</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->name?? null}}</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->brand?? null}} / {{$item->drones->model??null}}</p>
                                    
                                </div>
                            
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Flight Type</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->type??null}}</p>
                                </div>
                            
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Flight Location</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->fligh_location->name?? null}}</p>
                                </div>
                        
                            </div>
                        @endforeach
                        {{-- end tabel --}}
                </div>
                
                <div id="content2" class="tab-content">
                    <h1 class="text-2xl font-bold mb-4">Maintenance Overview</h1>
                    <div class="space-y-4">
                        <div class="flex flex-wrap justify-center gap-6">
                            <div class="rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-sm">
                                @livewire(App\Livewire\TabMaintenance::class)
                            </div>
                               {{-- tabel Maintenance --}}
                               <div class="container mx-auto p-4">
                                <h2 class="text-2xl font-bold mb-4">Maintenance Overdue</h2>
                                <div class="mt-4 flex justify-end mb-4">
                                    <a href="{{route('filament.admin.resources.maintences.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                                </div>
                                @foreach($maintenance_drone as $item)
                                <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Maintenance Name</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name??null}}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->date??null}}</p>
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Next Scheduled: <span class="text-sm text-gray-700 dark:text-gray-400">{{$item->date?? null}}</span></p>
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
                                                        Overdue: {{ $daysOverdueDiff }} days
                                                    </span>
                                                </span>
                                            @else
                                                <span class="bg-green-600 text-white py-1 px-2 rounded">{{ $formatDate }}</span>
                                            @endif
                                        </p>    
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Drone</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->name??null}}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->brand?? null}}/{{$item->drone->model??null}}</p>
                                        
                                        
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Technician</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->technician??null}}</p>
                                    </div>
                                </div> 
                                @endforeach
                                @foreach($maintenance_eq as $item)
                                <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg"">
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Maintenance Name</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name??null}}</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->date??null}}</p>
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Next Scheduled: <span class="text-sm text-gray-700 dark:text-gray-400">{{$item->date?? null}}</span></p>
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
                                                        Overdue: {{ $daysOverdueDiff }} days
                                                    </span>
                                                </span>
                                            @else
                                                <span class="bg-green-600 text-white py-1 px-2 rounded">{{ $formatDate }}</span>
                                            @endif
                                        </p>    
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Batteri / Equipment</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">
                                            @if($item->equipment == null)
                                                {{ $item->battrei->name ?? null }}
                                            @else
                                                {{ $item->equipment->name ?? null }}
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">
                                            @if($item->equipment == null)
                                                {{ $item->battrei->model ?? null }}
                                            @else
                                                {{ $item->equipment->model?? null }}
                                            @endif
                                        </p>
                                    </div>
                                
                                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Technician</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->technician??null}}</p>
                                    </div>
                                </div> 
                                @endforeach
                            </div>
                            {{-- end tabel Maintenance --}}
                        </div>
                    </div>
                </div>
                
                
                <div id="content3" class="tab-content">
                    <h1 class="text-2xl font-bold mb-4">Inventory Overview</h1>
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
                        <h2 class="text-2xl font-bold mb-4">Latest Battery / Equipment Used (Last 20)</h2>
                        <div class="mt-4 flex justify-end mb-4">
                            <a href="{{route('filament.admin.resources.battreis.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                        </div>
                        @foreach($battreiUsage as $item)
                        <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Item:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">
                                <a href="{{route('filament.admin.resources.battreis.view', [
                                    'tenant' => Auth()->user()->teams()->first()->id,
                                    'record' => $item,])}}">
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->name ?? 'No Battery' }} <br> Battereis</p>
                                    </p>
                                    </a>
                            </div>
                        
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Flight</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400"></p>
                                <div class="flex justify-between items-center rounded">
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->usage_count}} Flight</p> 
                
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
                                       Total Duration: {{ $formattedTotalDuration }} <!-- Menampilkan durasi yang diformat -->
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">null</p>
                                @endif
                                </div>      
                            </div>
                        
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Serial #</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">
                                        {{ $item->serial_I ?? 'N/A' }}
                                </p>
                            </div>
                        
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">For Drone</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->name??null}}</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->brand??null}} - {{$item->drone->model??null}}</p>
                            </div>
                        </div>
                        @endforeach
                        @foreach($equipmentUsage  as $item)
                        <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Item:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->name ?? 'No Equipment' }} <br> {{$item->type ?? 'test'}}</p>
                                </p>
                            </div>
                        
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Flight</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400"></p>
                                <div class="flex justify-between items-center rounded">
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->usage_count}} Flight</p> 
                
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
                                       Total Duration: {{ $formattedTotalDuration }} <!-- Menampilkan durasi yang diformat -->
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">null</p>
                                @endif
                                </div>      
                            </div>
                        
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Serial #</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">
                                        {{ $item->serial ?? 'N/A' }}
                                </p>
                            </div>
                        
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">For Drone</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->name??null}}</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->brand??null}} - {{$item->drones->model??null}}</p>
                            </div>
                        </div>
                        @endforeach
                        </div>
                    {{-- end tabel --}}      
                </div>

                <div id="content4" class="tab-content">
                    {{-- tabel Document --}}

                    <div class="container mx-auto p-4">
                        <h2 class="text-2xl font-bold mb-4">Document Overview</h2>
                        <div class="mt-4 flex justify-end mb-4">
                            <a href="{{route('filament.admin.resources.documents.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                        </div>
                        @foreach($document as $item)
                        <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                            
                            <!-- Kolom Name -->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Name:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->name ?? 'No Name' }}</p>
                            </div>
                    
                            <!-- Kolom Owner -->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Owner:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->users->name ?? 'No Owner' }}</p>
                            </div>
                    
                            <!-- Kolom Scope -->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Scope:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->scope ?? 'No Scope' }}</p>
                            </div>
                    
                            <!-- Kolom Type -->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Type:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->type ?? 'No Type' }}</p>
                            </div>
                    
                            <!-- Kolom Link -->
                            <div class="flex-1 min-w-[150px] mb-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Link:</p>
                                <a href="/storage/{{ $item->doc }}" target="_blank" rel="noopener noreferrer" 
                                   class="inline-block text-sm bg-orange-500 text-white rounded px-3 py-2 hover:bg-orange-600"
                                   style="background-color:#ff8303;">
                                    Open Document
                                </a>
                            </div>
                    
                        </div>
                        @endforeach
                        </div>
                    {{-- end tabel --}}  
                </div>

                <div id="content5" class="tab-content">
                    {{-- tabel INcident --}}
                    <div class="container mx-auto p-4">
                        <h2 class="text-2xl font-bold mb-4">Recent Incidents (Last 10)</h2>
                        <div class="mt-4 flex justify-end mb-4">
                            <a href="{{route('filament.admin.resources.incidents.index',['tenant' => auth()->user()->teams()->first()->id])}}" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                        </div>
                        @foreach($incident as $item)
                        <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-4 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
                            
                            <!-- Kolom Cause dan Status -->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Cause:</p>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->cause }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-300">Date: {{ $item->incident_date }}</p>
                                    </div>
                                    <span class="px-2 py-1 rounded text-white text-xs {{ $item->status == 'closed' ? 'bg-green-500' : 'bg-red-500' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                            </div>
                    
                            <!-- Kolom Drone Name -->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Drone:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->drone->name ?? 'No Drone' }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->drone->brand ?? 'No Drone' }} / {{$item->drone->model}}</p>
                            </div>
                    
                            <!-- Kolom Personnel Involved -->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Personnel Involved:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->users->name ?? 'No Personnel' }}</p>
                            </div>
                    
                            <!-- Kolom Location -->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Location:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->fligh_locations->name ?? 'No Location' }}</p>
                            </div>
                    
                            <!-- Kolom Project -->
                            <div class="flex-1 min-w-[150px] mb-2">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Project:</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->project->case ?? 'No Project' }}</p>
                            </div>
                    
                        </div>
                        @endforeach
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

