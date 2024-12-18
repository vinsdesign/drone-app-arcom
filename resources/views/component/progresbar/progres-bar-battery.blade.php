<?php 
use Carbon\Carbon;
    $id = $getRecord()->id;
    $name = $getRecord()->name;
    $flightTotal = $getRecord()->flaight_count;
    $status = $getRecord()->status;
    $lifeSpan = $getRecord()->life_span;
    $intialCycles = $getRecord()->initial_Cycle_count;
    $ownerId = $getRecord()->users_id;
    $ownerName = App\Models\User::where('id',$ownerId)->value('name');
    $lifespanCycle = $getRecord()->life_span_cycle;

    $flights = $getRecord()->fligh()
        ->whereHas('teams', function ($query) {
            $query->where('teams.id', auth()->user()->teams()->first()->id);
        })
        ->get()
        ->merge(
            $getRecord()->kits()->with(['fligh' => function ($query) {
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

    // $totalFlights = $flights->unique('id')->count();

    // dd($flightTotal,$totalDuration,$ownerName);
    // $route = route('filament.admin.resources.battery-chargers.index', [
    // 'tenant' => auth()->user()->teams()->first()->id,
    // 'record' => $id
    // ]);

    


?>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
    .progress-container {
        width: 100%;
        background-color: #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
        height: 30px;
        margin: 20px auto;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
    }

    .progress-bar {
        height: 100%;

        background-color: #4caf50;
        width: 0;
        text-align: center;
        line-height: 30px;
        color: white;
        transition: width 0.6s ease, background-color 0.4s ease;
    }

    .progress-bar.danger {
        background-color: #f44336;
    }
</style>
<div class="flex flex-col sm:flex-row sm:space-x-4 sm:space-y-0 space-y-4 justify-between sm:justify-end items-start sm:items-center w-full dark:text-gray-200 p-2">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 w-full">
    
        <div class="flex flex-col items-start sm:items-center space-y-2 w-full sm:w-auto flex-grow">
            <p class="text-lg font-semibold text-gray-800">{{$name}}</p>
            <p class="text-sm text-gray-500">{{$getRecord()->model ?? null}}</p>
        </div>
        
        <div class="flex flex-col space-y-4 w-full sm:w-auto flex-grow">

            <!-- Bagian Flight dan Durasi berada di atas -->
            <div class="flex flex-col sm:flex-row sm:space-x-4 sm:items-center space-y-2 sm:space-y-0 justify-center items-center">
                <p class="text-sm font-medium text-gray-700">Flight ({{$flightTotal}})</p>
                <p class="text-sm font-medium text-gray-700">{{$totalDuration}} time</p>
            </div>
            <!-- Bagian Lie Span dan Progress berada di bawah -->
            <div class="flex flex-col items-start sm:items-center text-center space-y-2 sm:space-y-0">
                <p class="text-sm font-medium text-gray-700">Lifespan {{$lifeSpan}}/Flight</p>
                <div class="w-full bg-gray-200 rounded-md h-6">
                    <div class="bg-blue-600 h-6 rounded-md progress-bar" id="progressBars-{{$id}}"></div>
                </div>
            </div>
        </div>
        
        <div class="relative flex flex-col space-y-4 w-full sm:w-auto flex-grow">
            

            
            <div class="flex flex-col sm:flex-row sm:space-x-4 sm:items-center space-y-2 sm:space-y-0 justify-center items-center">
                <p class="text-sm font-medium text-gray-700">{{$intialCycles}} Cycle(s)</p>
                <p class="text-sm font-medium text-gray-700">#Smart</p>
            </div>
        
            <button id="cycle-{{$id}}"
                class="absolute top-0.5 right-0 z-10 bg-blue-500 hover:bg-blue-600 text-white text-xs px-2 py-1 rounded">
                <a href="{{ route('filament.admin.resources.battery-chargers.index', ['tenant' => auth()->user()->teams()->first()->id, 'record' => $id]) }}">Cycle</a>
            </button>
    


            <!-- Bagian Lie Span dan Progress berada di bawah -->
            <div class="flex flex-col items-center text-center space-y-2 sm:space-y-0 w-full">
                <p class="text-sm font-medium text-gray-700">Lifespan {{$lifespanCycle}}/Cycle</p>
                <div class="w-full bg-gray-200 rounded-md h-6">
                    <div class="bg-blue-600 h-6 rounded-md progress-bar" id="progressBar-{{$id}}"></div>
                </div>
            </div>
            
        </div>

        <div class="flex flex-col items-start sm:items-start space-y-2 w-full sm:w-auto flex-grow">
            <p class="text-sm font-medium text-gray-800">
                Status: <span class="font-semibold text-blue-600">{{$status}}</span>
            </p>
            <p class="text-sm font-medium text-gray-800">
                Owner: <span class="font-semibold text-blue-600">{{$ownerName}}</span>
            </p>
        </div>

        <div class="flex flex-col items-start sm:items-start space-y-2 w-full sm:w-auto flex-grow">
            <p class="text-lg font-semibold text-gray-800"> For Drone:</p>
            <p class="text-sm text-gray-500">{{$getRecord()->drone->name ?? 'no set'}}</p>
        
        </div>
    </div>

</div>

<script>  
    // Cycle
    document.addEventListener('DOMContentLoaded', function () {
        const progressBar = document.getElementById('progressBar-{{$id}}');
        const maxData = {{ $lifespanCycle }};
        let currentData = {{ $intialCycles }};

        const percentage = (currentData / maxData) * 100;

        setTimeout(() => {
            progressBar.style.width = percentage + '%';
            if (currentData >= maxData) {
                progressBar.classList.add('danger');
            }
        }, 2000);
    });
    //flight
    document.addEventListener('DOMContentLoaded', function () {
        const progressBar = document.getElementById('progressBars-{{$id}}');
        const maxData = {{ $lifeSpan }};
        let currentData = {{ $flightTotal }};

        const percentage = (currentData / maxData) * 100;

        setTimeout(() => {
            progressBar.style.width = percentage + '%';
            if (currentData >= maxData) {
                progressBar.classList.add('danger');
            }
        }, 2000);
    });

</script>

  
