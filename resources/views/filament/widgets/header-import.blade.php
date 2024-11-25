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
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{!! TranslationHelper::translateIfNeeded('DJI Cloud Importer') !!}</h1><br>
            </div>
        </div>
        <h3 class="text-base font-medium text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Connect your DJI account to seamlessly import your flight logs stored in the DJI Cloud. 
        To access your most recent flight data, ensure your device (using the DJI GO app) is synchronized with the DJI Cloud. 
        Files that are duplicates or have already been imported will be automatically identified and skipped.') !!}
        </h3>
            <h3 class="text-base font-medium text-gray-500 dark:text-gray-400">
                {!! TranslationHelper::translateIfNeeded('Detailed instructions are available here.') !!}
            </h3>
    </div>
    </x-filament::section>
</x-filament-widgets::widget>
