@php
    use App\Helpers\TranslationHelper;
    $sumTeams = App\Models\Team::count('name');
@endphp
<x-filament-widgets::widget>

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <div class="filament-stats-overview-widget p-6 border-b bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="flex flex-col space-y-6 sm:flex-row sm:space-y-0 justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{!! TranslationHelper::translateIfNeeded('Teams') !!}</h1><br>
                    <span class="text-lg font-medium text-gray-500 dark:text-gray-400 p-3">{{ $sumTeams }} {!! TranslationHelper::translateIfNeeded('Total') !!}</span>
                </div>
                <div class="flex space-x-12">
                </div>
            </div>
        </div>
        
</x-filament-widgets::widget>
