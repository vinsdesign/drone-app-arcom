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
    @php
    use App\Helpers\TranslationHelper;
    @endphp
    @foreach($flight as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg"">
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Flight Name')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name}}</p>
            <div class="flex justify-between items-center rounded">
                <p class="text-sm text-gray-700 dark:text-gray-400 border border-gray-300 dark:border-gray-700">{{ $item->duration ?? null }}</p>
                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->start_date_flight ?? null }}</p>
            </div>
            
            <p class="text-sm text-gray-700 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Pilot :')!!} {{$item->users->name??null}}</p>
        </div>
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->name?? null}}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->brand?? null}} / {{$item->drones->model??null}}</p>
            
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Flight Type')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->type??null}}</p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Flight Location')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->fligh_location->name?? null}}</p>
        </div>

    </div>
    @endforeach
    {{-- end tabel Flight --}}

    {{-- Tabel Maintenance--}}
    @foreach($maintenance_drone as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Maintenance Name')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name??null}}</p>
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
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->name??null}}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drone->brand?? null}}/{{$item->drone->model??null}}</p>
            
            
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Technician')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->technician??null}}</p>
        </div>
    </div> 
    @endforeach
    @foreach($maintenance_eq as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg"">
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Maintenance Name')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name??null}}</p>
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
                            {!! TranslationHelper::translateIfNeeded('Overdue:')!!} {{ $daysOverdueDiff }} days
                        </span>
                    </span>
                @else
                    <span class="bg-green-600 text-white py-1 px-2 rounded">{{ $formatDate }}</span>
                @endif
            </p>    
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Battery / Equipment')!!}</p>
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
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Technician')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->technician??null}}</p>
        </div>
    </div> 
    @endforeach
    {{-- End Tabel Maintenance --}}

    {{--Tabel Inventory belum bisa memforeach per equipment dan battrei per flight ubah cara get datanya--}}
    @foreach($inventory as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg"">
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Item:')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">
            @if ($item->equidments->isEmpty())
                @foreach ($item->battries as $battery)
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{ $battery->name ?? 'No Battery' }} {!! TranslationHelper::translateIfNeeded(': Batteries')!!}</p>
                @endforeach
            @else
                @foreach ($item->equidments as $equipment)
                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $equipment->name ?? 'No Equipment' }} : {{$equipment->type?? null}}</p>
                @endforeach
            @endif
            </p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Flight')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name}}</p>
            <div class="flex justify-between items-center rounded">
                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->duration}}</p> 
                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->start_date_flight}}</p> 
            </div>
               
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Serial #')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">
                @if ($item->equidments->isEmpty())
                @foreach ($item->battries as $battery)
                    {{ $battery->serial_I ?? 'N/A' }}
                @endforeach
            @else
                @foreach ($item->equidments as $equipment)
                    {{ $equipment->serial ?? 'N/A' }}
                @endforeach
            @endif
            </p>
        </div>
    
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->name??null}}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->drones->brand??null}} - {{$item->drones->model}}</p>
        </div>
    </div> 
    @endforeach
    {{--End Tabel Inventory--}}


    {{--tabel Document--}}
    @foreach($document as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
        
        <!-- Kolom Name -->
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Name:')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->name ?? 'No Name' }}</p>
        </div>

        <!-- Kolom Owner -->
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Owner:')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->users->name ?? 'No Owner' }}</p>
        </div>

        <!-- Kolom Scope -->
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Scope:')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->scope ?? 'No Scope' }}</p>
        </div>

        <!-- Kolom Type -->
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Type:')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->type ?? 'No Type' }}</p>
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
{{--end tabel document--}}

{{-- tabel incident --}}
@foreach($incident as $item)
    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-4 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto mb-4 shadow-lg">
        
        <!-- Kolom Cause dan Status -->
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Cause:')!!}</p>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->cause }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Date:')!!} {{ $item->incident_date }}</p>
                </div>
                <span class="px-2 py-1 rounded text-white text-xs {{ $item->status == 'closed' ? 'bg-green-500' : 'bg-red-500' }}">
                    {{ ucfirst($item->status) }}
                </span>
            </div>
        </div>

        <!-- Kolom Drone Name -->
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone:')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->drone->name ?? 'No Drone' }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->drone->brand ?? 'No Drone' }} / {{$item->drone->model}}</p>
        </div>

        <!-- Kolom Personnel Involved -->
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Personnel Involved:')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->users->name ?? 'No Personnel' }}</p>
        </div>

        <!-- Kolom Location -->
        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Location:')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->fligh_locations->name ?? 'No Location' }}</p>
        </div>

        <!-- Kolom Project -->
        <div class="flex-1 min-w-[150px] mb-2">
            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Project:')!!}</p>
            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->project->case ?? 'No Project' }}</p>
        </div>

    </div>
@endforeach

{{-- end tabel Incident --}}

</x-filament-widgets::widget>

