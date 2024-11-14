@php
    use App\Helpers\TranslationHelper;
    $user = Auth()->user()->Teams()->first()->id;
    $sumMission = App\Models\PlannedMission::Where('teams_id',$user)->count('name');
    $planned = App\Models\PlannedMission::Where('teams_id',$user)->where('status','planned')->count('name');
    $completed = App\Models\PlannedMission::Where('teams_id',$user)->where('status','completed')->count('name');
    $cancel = App\Models\PlannedMission::Where('teams_id', $user)->where('status','cancel')->count('name');
    $append = App\Models\PlannedMission::Where('teams_id', $user)->where('status','append')->count('name');
@endphp
<x-filament-widgets::widget>

    <x-filament::section>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <div class="filament-stats-overview-widget p-6 border-b bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="flex flex-col space-y-6 sm:flex-row sm:space-y-0 justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{!! TranslationHelper::translateIfNeeded('Open Missions') !!}</h1><br>
                    <span class="text-lg font-medium text-gray-500 dark:text-gray-400 p-2">{{ $sumMission }} {!! TranslationHelper::translateIfNeeded('Total') !!}</span>
                </div>
        
                <div class="flex space-x-12">
                </div>
        
                @if (Auth::user()->can('create', App\Models\PlannedMission::class)) 
                <div class="flex space-x-4">
                    <a href="{{ route('filament.admin.resources.planned-missions.create', ['tenant' => auth()->user()->teams()->first()->id]) }}">
                        <button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                            {!! TranslationHelper::translateIfNeeded('Add Missions') !!}
                        </button>
                    </a> 
                </div>
            @endif
            </div>
        </div>
        
    </x-filament::section>
</x-filament-widgets::widget>
