@php
    use App\Helpers\TranslationHelper;
    $user = Auth()->user()->Teams()->first()->id;
    $sumbattery = App\Models\battrei::Where('teams_id',$user)->count('name');
    $airworthy = App\Models\battrei::Where('teams_id',$user)->where('status','airworthy')->count('name');
    $maintenance = App\Models\battrei::Where('teams_id',$user)->where('status','maintenance')->count('name');
    $retired = App\Models\battrei::Where('teams_id', $user)->where('status','retired')->count('name');
@endphp
<x-filament-widgets::widget>
    <x-filament::section>

    <div class="filament-stats-overview-widget p-6 border-b bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Title and total batteries -->
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-6 sm:space-y-0">
            <!-- Title Section -->
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white truncate">{!! TranslationHelper::translateIfNeeded('Batteries') !!}</h1>
                <span class="text-lg font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $sumbattery }} {!! TranslationHelper::translateIfNeeded('Total') !!}</span>
            </div>
    
            <!-- Status indicators -->
            <div class="flex flex-wrap justify-center gap-6">
                <div class="text-center">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Airworthy') !!}</h2>
                    <h1 class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $airworthy }}</h1>
                </div>
                <div class="text-center">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Maintenance') !!}</h2>
                    <h1 class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $maintenance }}</h1>
                </div>
                <div class="text-center">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Retired') !!}</h2>
                    <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-300">{{ $retired }}</h1>
                </div>
            </div>
    
            <!-- Action buttons -->
            @if (Auth::user()->can('create', App\Models\battrei::class)) 
            <div class="flex space-x-4">
                <a href="{{ route('filament.admin.resources.battreis.create', ['tenant' => auth()->user()->teams()->first()->id]) }}">
                    <button class="px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                        {!! TranslationHelper::translateIfNeeded('Add Batteries') !!}
                    </button>
                </a>
            </div>
            @endif
        </div>
    </div>
    
    
    </x-filament::section>
</x-filament-widgets::widget>
