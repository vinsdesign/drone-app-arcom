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
$flight = App\Models\fligh::whereHas('teams', function ($query) use ($currentTeamId){
    $query->where('id', $currentTeamId);
})->orderBy('created_at', 'desc')
->limit(20)
->get();
//end flight
//inventory
$inventory = App\Models\fligh::whereHas('teams', function ($query) use ($currentTeamId){
    $query->where('id', $currentTeamId);
})->orderBy('created_at', 'desc')->where('kits_id',null)
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
    <x-filament::section>
<div class="container tab-border-warna">
    <!-- Tab headers -->
    <div class="container mx-auto p-5">
        <div class="flex space-x-4 border border-gray-300 rounded-lg p-2 bg-black">
            <button id="tab0" class="tab-button active text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded">Summary</button>
            <button id="tab1" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded">Flights</button>
            <button id="tab2" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded">Maintenance</button>
            <button id="tab3" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded">Inventory</button>
            <button id="tab4" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded">Documents</button>
            <button id="tab5" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded">Incidents</button>
        </div>

    <!-- Tab content -->
        <div class="content">

            <div id="content0" class="tab-content active">
                <h2 class="text-2xl font-bold mb-4">Your Operations Overview</h2>
            
                {{-- untuk tampilan 2 atau lebih widgets --}}
                <div class="space-y-4">
                    <div class="flex flex-wrap justify-center gap-6">
                        <div class="bg-white rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-full">
                            @livewire(App\Livewire\FlightDurationChart::class)
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-full">
                            @livewire(App\Livewire\FlightChart::class)
                        </div>
                    </div>
                    {{-- buttom --}}
                    <div class=""></div>
                    {{-- end buttom --}}
                </div>
            </div>

                <div id="content1" class="tab-content">
                    <h2 class="text-2xl font-bold mb-4">Flight Overview</h2>
                
                    {{-- untuk tampilan 2 atau lebih widgets --}}
                    <div class="space-y-4">
                        <div class="flex flex-wrap justify-center gap-6">
                            <div class="bg-white rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-sm">
                                @livewire(App\Livewire\TabFlight::class)
                            </div>
                            <div class="bg-white rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-sm">
                                @livewire(App\Livewire\TabFlightDrone::class)
                            </div>
                            <div class="bg-white rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-full">
                                @livewire(App\Livewire\TabFlightChartBar::class)
                            </div>
                        </div>
                            {{-- tabel Flight --}}
                            <div class="container mx-auto p-4">
                            <h2 class="text-2xl font-bold mb-4">Latest Flights (Last 20)</h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg">
                                    <thead>
                                        <tr class="w-full bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            <th class="py-3 px-4 text-left">Name</th>
                                            <th class="py-3 px-4 text-left">Duration</th>
                                            <th class="py-3 px-4 text-left">Date</th>
                                            <th class="py-3 px-4 text-left">Drone</th>
                                            <th class="py-3 px-4 text-left">Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($flight as $item)
                                        <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                            <td class="py-2 px-4">{{$item->name ?? null}}</td>
                                            <td class="py-2 px-4">{{$item->duration ?? null}}</td>
                                            <td class="py-2 px-4">{{$item->start_date_flight?? null}}</td>
                                            <td class="py-2 px-4">{{$item->drones->name?? null}}</td>
                                            <td class="py-2 px-4">{{$item->fligh_location->name?? null}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                <a href="#" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                            </div>
                            </div>
                        {{-- end tabel --}}
                    </div>
                </div>
                
                <div id="content2" class="tab-content">
                    <h1 class="text-2xl font-bold mb-4">Maintenance Overview</h1>
                    <div class="space-y-4">
                        <div class="flex flex-wrap justify-center gap-6">
                            <div class="bg-white rounded-lg shadow-lg p-4 flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-sm">
                                @livewire(App\Livewire\TabMaintenance::class)
                            </div>
                               {{-- tabel --}}
                               <div class="container mx-auto p-4">
                                <h2 class="text-2xl font-bold mb-4">Maintenance Overdue</h2>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg">
                                        <thead>
                                            <tr class="w-full bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                <th class="py-3 px-4 text-left">Name</th>
                                                <th class="py-3 px-4 text-left">Next Scheduled</th>
                                                <th class="py-3 px-4 text-left">Details</th>
                                                <th class="py-3 px-4 text-left">Items</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($maintenance_drone as $item)
                                            <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                                <td class="py-2 px-4">{{$item->name ?? null}}</td>
                                                <td class="py-2 px-4">{{$item->date ?? null}}</td>
                                                <td class="py-2 px-4">
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
                                                </td>
                                                <td class="py-2 px-4">
                                                    @if($item->equipment == null)
                                                        {{ $item->battrei->name ?? null }}
                                                    @else
                                                        {{ $item->equipment->name ?? null }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @foreach($maintenance_eq as $item)
                                            <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                                <td class="py-2 px-4">{{$item->name ?? null}}</td>
                                                <td class="py-2 px-4">{{$item->date ?? null}}</td>
                                                <td class="py-2 px-4">
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
                                                </td>
                                                <td class="py-2 px-4">
                                                    @if($item->equipment == null)
                                                        {{ $item->battrei->name ?? null }}
                                                    @else
                                                        {{ $item->equipment->name ?? null }}
                                                    @endif
                                                </td>
                                                
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    <a href="#" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                                </div>
                            </div>
                            {{-- end tabel --}}
                        </div>
                    </div>
                </div>
                
                
                <div id="content3" class="tab-content">
                    <h1 class="text-2xl font-bold mb-4">Inventory Overview</h1>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 justify-center">
                            <div class="bg-white rounded-lg shadow-lg p-4 max-w-full">
                                @livewire(App\Livewire\TabInventoryDrone::class)
                            </div>
                            <div class="bg-white rounded-lg shadow-lg p-4 max-w-full">
                                @livewire(App\Livewire\TabInventoryBatteri::class)
                            </div>
                            <div class="bg-white rounded-lg shadow-lg p-4 max-w-full">
                                @livewire(App\Livewire\TabInventoryEquidment::class)
                            </div>
                            <div class="bg-white rounded-lg shadow-lg p-4 max-w-full">
                                @livewire(App\Livewire\TabInventoryLocation::class)
                            </div>
                        </div>
                    </div>
                        {{-- tabel Inventory --}}
                        <div class="container mx-auto p-4">
                        <h2 class="text-2xl font-bold mb-4">Latest Battery / Equipment Used (Last 20)</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg">
                                <thead>
                                    <tr class="w-full bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        <th class="py-3 px-4 text-left">Name</th>
                                        <th class="py-3 px-4 text-left">Flight</th>
                                        <th class="py-3 px-4 text-left">Project</th>
                                        <th class="py-3 px-4 text-left">Duration</th>
                                        <th class="py-3 px-4 text-left">Drone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventory as $item)
                                    <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                        <td class="py-2 px-4">
                                            @if ($item->equidments->isEmpty())
                                            @foreach ($item->battries as $battery)
                                                {{ $battery->name ?? 'No Battery' }}
                                            @endforeach
                                        @else
                                            @foreach ($item->equidments as $equipment)
                                                {{ $equipment->name ?? 'No Equipment' }}
                                            @endforeach
                                        @endif
                                        </td>
                                        <td class="py-2 px-4">{{$item->name ?? null}}</td>
                                        <td class="py-2 px-4">{{$item->projects->case?? null}}</td>
                                        <td class="py-2 px-4">{{$item->duration?? null}}</td>
                                        <td class="py-2 px-4">{{$item->drones->name?? null}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                        </div>
                        </div>
                    {{-- end tabel --}}      
                </div>

                <div id="content4" class="tab-content">
                    {{-- tabel Inventory --}}
                    <div class="container mx-auto p-4">
                        <h2 class="text-2xl font-bold mb-4">Document Overview</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg">
                                <thead>
                                    <tr class="w-full bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        <th class="py-3 px-4 text-left">Name</th>
                                        <th class="py-3 px-4 text-left">Owner</th>
                                        <th class="py-3 px-4 text-left">Scope</th>
                                        <th class="py-3 px-4 text-left">Type</th>
                                        <th class="py-3 px-4 text-left">Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($document as $item)
                                    <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                        <td class="py-2 px-4">{{$item->name}}</td>
                                        <td class="py-2 px-4">{{$item->users->name ?? null}}</td>
                                        <td class="py-2 px-4">{{$item->scope?? null}}</td>
                                        <td class="py-2 px-4">{{$item->type?? null}}</td>
                                        <td class="py-2 px-4"><a href='/storage/{{$item->doc}}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>Open Document</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                        </div>
                        </div>
                    {{-- end tabel --}}  
                </div>

                <div id="content5" class="tab-content">
                    {{-- tabel Inventory --}}
                    <div class="container mx-auto p-4">
                    <h2 class="text-2xl font-bold mb-4">Recent Incidents (Last 10)</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-gray-100 dark:bg-gray-800 rounded-lg shadow-lg">
                            <thead>
                                <tr class="w-full bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                    <th class="py-3 px-4 text-left">Name</th>
                                    <th class="py-3 px-4 text-left">Item</th>
                                    <th class="py-3 px-4 text-left">Personnel Involved</th>
                                    <th class="py-3 px-4 text-left">Location</th>
                                    <th class="py-3 px-4 text-left">Project</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incident as $item)
                                <tr class="hover:bg-gray-200 dark:hover:bg-gray-700">
                                    <td class="py-2 px-4">
                                        {{$item->cause}}<br>
                                        <span class="bg-gray-300">{{$item->incident_date}}</span>
                                        @if($item->status == "closed")
                                        <span class="bg-green-400">{{$item->status}}</span><br>
                                        @else
                                            <span class="bg-red-400">{{$item->status}}</span><br>
                                        @endif
                                    
                                    </td>
                                    <td class="py-2 px-4">{{$item->drone->name ?? null}}</td>
                                    <td class="py-2 px-4">{{$item->users->name ?? null}}</td>
                                    <td class="py-2 px-4">{{$item->fligh_locations->name?? null}}</td>
                                    <td class="py-2 px-4">{{$item->project->case}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="#" class="inline-block px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">View All</a>
                    </div>
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
</x-filament::section>
</x-filament-widgets::widget>

