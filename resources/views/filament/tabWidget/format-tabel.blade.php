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
<x-filament-widgets::widget>
    {{-- tabel di filight --}}
    @foreach($flight as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-500 dark:bg-black max-w-[800px] mx-auto mb-4">
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-white dark:text-gray-600">{{$item->cause}}</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->start_date_flight??null}}</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->duration??null}}</p>
            <p class="text-sm text-white dark:text-gray-600">Pilot : {{$item->users->name??null}}</p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-white dark:text-gray-600">{{$item->drones->name?? null}}</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->drones->brand?? null}}/{{$item->drones->model??null}}</p>
            
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-white dark:text-gray-600">{{$item->type??null}}</p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-white dark:text-gray-600">{{$item->fligh_location->name?? null}}</p>
        </div>

    </div>
    @endforeach
    {{-- end tabel Flight --}}

    {{-- Tabel Maintenance--}}
    @foreach($maintenance_drone as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-500 dark:bg-black max-w-[800px] mx-auto mb-4">
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-white dark:text-gray-600">{{$item->name??null}}</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->date??null}}</p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-lg text-white dark:text-gray-600">Next Scheduled: <span class="text-sm">{{$item->date?? null}}</span></p>
            <p class="text-sm text-white dark:text-gray-600">
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
            <p class="text-sm text-white dark:text-gray-600">{{$item->drone->name??null}}</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->drone->brand?? null}}/{{$item->drone->model??null}}</p>
            <p class="text-sm font-bold text-gray-800 dark:text-gray-600">Drone</p>
            
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm font-bold text-white dark:text-gray-600">Technician</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->technician??null}}</p>
        </div>
    </div> 
    @endforeach
    @foreach($maintenance_eq as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-500 dark:bg-black max-w-[800px] mx-auto mb-4">
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-white dark:text-gray-600">{{$item->name??null}}</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->date??null}}</p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-lg text-white dark:text-gray-600">Next Scheduled: <span class="text-sm">{{$item->date?? null}}</span></p>
            <p class="text-sm text-white dark:text-gray-600">
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
            <p class="text-sm text-white dark:text-gray-600">
                @if($item->equipment == null)
                    {{ $item->battrei->name ?? null }}
                @else
                    {{ $item->equipment->name ?? null }}
                @endif
            </p>
            <p class="text-sm text-white dark:text-gray-600">
                @if($item->equipment == null)
                    {{ $item->battrei->model ?? null }}
                @else
                    {{ $item->equipment->model?? null }}
                @endif
            </p>
            <p class="text-sm font-bold text-gray-800 dark:text-gray-600">Batteri / Equipment</p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm font-bold text-white dark:text-gray-600">Technician</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->technician??null}}</p>
        </div>
    </div> 
    @endforeach
    {{-- End Tabel Maintenance --}}

    {{--Tabel Inventory--}}
    @foreach($inventory as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-500 dark:bg-black max-w-[800px] mx-auto mb-4">
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-white dark:text-gray-600">{{$item->name??null}}</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->date??null}}</p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-lg text-white dark:text-gray-600">Next Scheduled: <span class="text-sm">{{$item->date?? null}}</span></p>
            <p class="text-sm text-white dark:text-gray-600">
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
            <p class="text-sm text-white dark:text-gray-600">
                @if($item->equipment == null)
                    {{ $item->battrei->name ?? null }}
                @else
                    {{ $item->equipment->name ?? null }}
                @endif
            </p>
            <p class="text-sm text-white dark:text-gray-600">
                @if($item->equipment == null)
                    {{ $item->battrei->model ?? null }}
                @else
                    {{ $item->equipment->model?? null }}
                @endif
            </p>
            <p class="text-sm font-bold text-gray-800 dark:text-gray-600">Batteri / Equipment</p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm font-bold text-white dark:text-gray-600">Technician</p>
            <p class="text-sm text-white dark:text-gray-600">{{$item->technician??null}}</p>
        </div>
    </div> 
    @endforeach
    {{--End Tabel Inventory--}}




</x-filament-widgets::widget>

