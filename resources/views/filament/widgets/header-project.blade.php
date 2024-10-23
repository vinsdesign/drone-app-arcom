@php
    $user = Auth()->user()->teams()->first()->id;
    $project = App\models\Projects::Where('teams_id',$user)->count('case')
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
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Projects</h1><br>
                <span class="text-lg font-medium text-gray-500 dark:text-gray-400">{{ $project }} Total</span>
            </div>
            <div class="flex space-x-4">
                <button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                    Add Projects
                </button>
            </div>
        </div>
    </div>
    </x-filament::section>
    </x-filament-widget::widget>

