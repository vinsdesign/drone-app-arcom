@php
    $user = Auth()->user()->Teams()->first()->id;
    $maintenance = App\Models\maintence_eq::Where('teams_id',$user)->count('name');
    $inProgres = App\Models\maintence_eq::Where('teams_id',$user)->where('status','in_progress')->count('name');
    $complate = App\Models\maintence_eq::Where('teams_id',$user)->where('status','completed')->count('name');
    $schedule = App\Models\maintence_eq::Where('teams_id', $user)->where('status','Schedule')->count('name');
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
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Maintenance <br> Equipment & Batteries</h1><br>
                <span class="text-lg font-medium text-gray-500 dark:text-gray-400">{{ $maintenance }} Total</span>
            </div>
    
            <!-- Status indicators (Airworthy, Maintenance, Retired) -->
            <div class="flex space-x-12">
                <div class="text-center">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Airworthy</h2>
                    <h1 class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $complate }}</h1>
                </div>
                <br>
                <div class="text-center">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Maintenance</h2>
                    <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $inProgres }}</h1>
                </div>
                <br>
                <div class="text-center">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Schedule</h2>
                    <h1 class="text-3xl font-bold text-red-600 dark:text-red-300">{{ $schedule }}</h1>
                </div>
            </div>
    
            <!-- Action buttons -->
            <div class="flex space-x-4">
                <button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                    Add Maintenance
                </button>
            </div>
        </div>
    </div>
    </x-filament::section>
</x-filament-widgets::widget>
