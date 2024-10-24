@php
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
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        </head>
        <div class="filament-stats-overview-widget p-6 border-b bg-white dark:bg-gray-800 rounded-lg shadow">
            <!-- Title and total drones -->
            <div class="flex flex-col space-y-6 sm:flex-row sm:space-y-0 justify-between items-center">
                <!-- Title Section -->
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Documents</h1><br>
                    <span class="text-lg font-medium text-gray-500 dark:text-gray-400">{{ $sumDocument }} Total</span>
                </div>
        
                <!-- Status indicators (Airworthy, Maintenance, Retired) -->
                <div class="flex space-x-12">
                    <div class="text-center">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Flights </h2>
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Doc. </h2>
                        <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $flight }}</h1>
                    </div>
                    <br>
                    <div class="text-center">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Organization </h2>
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Doc. </h2>
                        <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $organization }}</h1>
                    </div>
                    <br>
                    <div class="text-center">
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Pilot </h2>
                        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400">Doc. </h2>
                        <h1 class="text-3xl font-bold text-gray-600 dark:text-gray-400">{{ $pilot }}</h1>
                    </div>
                </div>
        
                <!-- Action buttons -->
                @if (Auth::user()->can('create', App\Models\document::class)) 
                <div class="flex space-x-4">
                    <a href="{{ route('filament.admin.resources.documents.create', ['tenant' => auth()->user()->teams()->first()->id]) }}"><button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                        Add Document</button></a> 
                </div>
            @endif
            </div>
        </div>
        
    </x-filament::section>
</x-filament-widgets::widget>
