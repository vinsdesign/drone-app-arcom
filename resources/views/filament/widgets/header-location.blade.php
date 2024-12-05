@php
    use App\Helpers\TranslationHelper;
    $user = Auth()->user()->Teams()->first()->id;
    $sumLocation = App\Models\fligh_location::Where('teams_id',$user)->where('status_visible', '!=', 'archived')->count('name');
@endphp
<x-filament-widgets::widget>

    <x-filament::section>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <div class="filament-stats-overview-widget p-6 border-b bg-white dark:bg-gray-800 rounded-lg shadow">
            <!-- Title and total drones -->
            <div class="flex flex-col space-y-6 sm:flex-row sm:space-y-0 justify-between items-center">
                <!-- Title Section -->
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{!! TranslationHelper::translateIfNeeded('Operation Locations') !!}</h1><br>
                    <span class="text-lg font-medium text-gray-500 dark:text-gray-400 p-3">{{ $sumLocation }} {!! TranslationHelper::translateIfNeeded('Total') !!}</span>
                </div>
        
                <!-- Status indicators (Airworthy, Maintenance, Retired) -->
                <div class="flex space-x-12">
                </div>
        
                <!-- Action buttons -->
                @if (Auth::user()->can('create', App\Models\fligh_location::class)) 
                <div class="flex space-x-4">
                    <a href="{{ route('filament.admin.resources.fligh-locations.create', ['tenant' => auth()->user()->teams()->first()->id]) }}"><button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                        {!! TranslationHelper::translateIfNeeded('Add Locations') !!}</button></a> 
                </div>
            @endif
            </div>
        </div>
        
    </x-filament::section>
</x-filament-widgets::widget>
