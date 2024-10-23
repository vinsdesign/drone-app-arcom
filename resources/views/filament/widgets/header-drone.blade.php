@php
    $user = Auth()->user()->Teams()->first()->id;
    $sumDrone = App\Models\Drone::Where('teams_id',$user)->count('name');
    $airworthy = App\Models\Drone::Where('teams_id',$user)->where('status','airworthy')->count('name');
    $maintenance = App\Models\Drone::Where('teams_id',$user)->where('status','maintenance')->count('name');
    $retired = App\Models\Drone::Where('teams_id', $user)->where('status','retired')->count('name');
@endphp
<x-filament-widgets::widget>

    <x-filament::section>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        </head>
        <div class="filament-stats-overview-widget p-6 border-b bg-white dark:bg-gray-800 rounded-lg shadow">
            <!-- Title and total drones -->
            <div class="flex flex-col space-y-6 sm:flex-row sm:space-y-0 justify-between items-center">
                <!-- Title Section -->
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Drones</h1><br>
                    <span class="text-lg font-medium text-gray-500 dark:text-gray-400">{{ $sumDrone }} Total</span>
                </div>
        
                <!-- Status indicators (Airworthy, Maintenance, Retired) -->
                <div class="flex space-x-12">
                    <div class="text-center">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Airworthy</h2>
                        <h1 class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $airworthy }}</h1>
                    </div>
                    <br>
                    <div class="text-center">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Maintenance</h2>
                        <h1 class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $maintenance }}</h1>
                    </div>
                    <br>
                    <div class="text-center">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Retired</h2>
                        <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-300">{{ $retired }}</h1>
                    </div>
                </div>
        
                <!-- Action buttons -->
                <div class="flex space-x-4">
                    <button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                        Add Drones
                    </button>
                </div>
            </div>
        </div>
        
    </x-filament::section>
</x-filament-widgets::widget>
