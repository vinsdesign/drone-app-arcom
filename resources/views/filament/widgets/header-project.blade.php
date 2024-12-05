@php
    use App\Helpers\TranslationHelper;
    $user = Auth()->user()->teams()->first()->id;
    $project = App\models\Projects::Where('teams_id',$user)->where('status_visible', '!=', 'archived')->count('case')
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
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{!! TranslationHelper::translateIfNeeded('Projects') !!}</h1><br>
                <span class="text-lg font-medium text-gray-500 dark:text-gray-400 p-3">{{ $project }} {!! TranslationHelper::translateIfNeeded('Total') !!}</span>
            </div>
            @if (Auth::user()->can('create', App\Models\Projects::class)) 
            <div class="flex space-x-4">
                <a href="{{ route('filament.admin.resources.projects.create', ['tenant' => auth()->user()->teams()->first()->id]) }}"><button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                    {!! TranslationHelper::translateIfNeeded('Add Project') !!}</button></a> 
            </div>
        @endif
        </div>
    </div>
    </x-filament::section>
    </x-filament-widget::widget>

