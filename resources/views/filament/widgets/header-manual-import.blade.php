@php
use App\Helpers\TranslationHelper;
@endphp
<x-filament-widgets::widget>
    <x-filament::section class="Drak:dark:bg-gray-800">
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
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{!! TranslationHelper::translateIfNeeded('Flight Log Importer') !!}</h1><br>
            </div>
        </div>
        <h3 class="text-base font-medium text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Add a new flight in your logbook by importing your Drone log file. 
        It can take several minutes to upload big files (Control progress in the browser status bar). 
        Thanks for your patience. Max file size is 5120M.') !!}
        </h3>
            <h3 class="text-base font-medium text-gray-500 dark:text-gray-400">
                {!! TranslationHelper::translateIfNeeded('You can customize log type importer list from your Settings.') !!}
            </h3>
    </div>
    </x-filament::section>
</x-filament-widgets::widget>
