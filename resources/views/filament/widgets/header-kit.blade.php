@php
    $user = Auth()->user()->Teams()->first()->id;
    $sumKits = App\Models\Kits::Where('teams_id',$user)->count('name');
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
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Kits</h1><br>
                    <span class="text-lg font-medium text-gray-500 dark:text-gray-400">{{ $sumKits }} Total</span>
                </div>
        
                <!-- Status indicators (Airworthy, Maintenance, Retired) -->
                <div class="flex space-x-12">
                </div>
        
                <!-- Action buttons -->
                @if (Auth::user()->can('create', App\Models\Kits::class)) 
                <div class="flex space-x-4">
                    <a href="{{ route('filament.admin.resources.kits.create', ['tenant' => auth()->user()->teams()->first()->id]) }}"><button class="filament-button px-6 py-2 text-sm font-semibold text-white bg-primary-600 dark:bg-primary-500 border border-transparent rounded-md hover:bg-primary-700 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600 dark:focus:ring-offset-gray-800">
                        Add Kits</button></a> 
                </div>
            @endif
            </div>
            <h3 class="text-base font-medium text-gray-500 dark:text-gray-400">This section allows you to create and maintain your kit list. A kit is a pack of battery, equipment and drones.</h3>
        </div>
        
        
    </x-filament::section>
</x-filament-widgets::widget>
