@php
    use Stichoza\GoogleTranslate\GoogleTranslate;

    $user = Auth()->user()->Teams()->first()->id;
    $sumDocument= App\Models\document::Where('teams_id',$user)->count('name');
    $flight = App\Models\document::Where('teams_id',$user)->where('scope','flight')->count('name');
    $organization= App\Models\document::Where('teams_id',$user)->where('scope','organizaton')->count('name');
    $pilot= App\Models\document::Where('teams_id', $user)->where('scope','pilot')->count('name');
    $project = App\Models\document::Where('teams_id', $user)->where('scope','project')->count('name');
    $eq = App\Models\document::Where('teams_id', $user)->where('scope','equipment/battery')->count('name');
    $incident = App\Models\document::Where('teams_id', $user)->where('scope','incident')->count('name');
    $drone= App\Models\document::Where('teams_id', $user)->where('scope','drones')->count('name');
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
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{!! GoogleTranslate::trans('Documents', session('locale') ?? 'en') !!}</h1><br>
                    <span class="text-lg font-medium text-gray-500 dark:text-gray-400 p-3">{{ $sumDocument }} {!! GoogleTranslate::trans('Total', session('locale') ?? 'en') !!}</span>
                </div>
        
                <!-- Status indicators (Airworthy, Maintenance, Retired) -->
                <div class="flex space-x-12">
                    <div class="text-center p-3">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">{!! GoogleTranslate::trans('Flights', session('locale') ?? 'en') !!} </h2>
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Doc. </h2>
                        <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $flight }}</h1>
                    </div>
                    <br>
                    <div class="text-center p-3">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">{!! GoogleTranslate::trans('Organization', session('locale') ?? 'en') !!} </h2>
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Doc. </h2>
                        <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $organization }}</h1>
                    </div>
                    <br>
                    <div class="text-center p-3">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">{!! GoogleTranslate::trans('Pilot', session('locale') ?? 'en') !!} </h2>
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Doc. </h2>
                        <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $pilot }}</h1>
                    </div>
                </div>
        
                <!-- Action buttons -->
                @if (Auth::user()->can('create', App\Models\document::class)) 
                <div class="flex space-x-4">
                    <a href="{{ route('filament.admin.resources.documents.create', ['tenant' => auth()->user()->teams()->first()->id]) }}"><button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                        {!! GoogleTranslate::trans('Add Document', session('locale') ?? 'en') !!}</button></a> 
                </div>
            @endif
            </div>
        </div>
        
    </x-filament::section>
</x-filament-widgets::widget>
