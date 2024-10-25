@php
    $user = Auth()->user()->Teams()->first()->id;
    $sumFlight = App\Models\fligh::Where('teams_id',$user)->count('name');
    $flights = App\Models\fligh::Where('teams_id',$user)->get();
    $totalDurationInSeconds = $flights->sum(function ($flight) {
                list($hours, $minutes, $seconds) = explode(':', $flight->duration);
                return ($hours * 3600) + ($minutes * 60) + $seconds;
            });
            $totalHours = floor($totalDurationInSeconds / 3600);
            $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
            $totalSeconds = $totalDurationInSeconds % 60;
            // Format total durasi
            $formattedTotalDuration = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);
    $team = Auth()->user()->Teams()->first()->name;
    
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<x-filament-widgets::widget>
    <x-filament::section>

    <div class="filament-stats-overview-widget p-6 border-b bg-white dark:bg-gray-800 rounded-lg shadow">
        <!-- Title and total drones -->
        <div class="flex flex-col space-y-6 sm:flex-row sm:space-y-0 justify-between items-center">
            <!-- Title Section -->
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{$team}}</h1><br>
            </div>
    
            <!-- Status indicators (Airworthy, Maintenance, Retired) -->
            <div class="flex space-x-12">
                <div class="text-center">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Flying Time</h2>
                    <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-300">{{ $formattedTotalDuration }}</h1>
                </div>
                <br>
                <div class="text-center">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Flights</h2>
                    <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-300">{{ $sumFlight }}</h1>
                </div>
                <br>
            </div>
    
            <!-- Action buttons -->
            
            @if (Auth::user()->can('create', App\Models\fligh::class)) 
                <div class="flex space-x-4">
                    {{-- <a href="{{ route('filament.admin.resources.flighs.create', ['tenant' => auth()->user()->teams()->first()->id]) }}"><button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                        Add Flight</button></a>  --}}
                </div>
            @endif
 
        </div>
    </div>
    
    </x-filament::section>
</x-filament-widgets::widget>
