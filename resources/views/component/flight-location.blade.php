<?php
use App\Helpers\TranslationHelper;
$teams = Auth()->user()->teams()->first()->id;
$locations = session('location_id');
$flights = App\Models\Fligh::where('teams_id', $teams)->where('location_id', $locations)->paginate(5);

$flightCount = App\Models\Fligh::where('teams_id', $teams)->where('location_id', $locations)->get();

$count = $flightCount->count('id');

?>

<head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .main-content.active {
            display: block;
        }
    </style>
</head>
@if (request()->routeIs('filament.admin.resources.fligh-locations.view'))
    <div class="flex items-center justify-between py-4 px-6 border-b border-gray-300 bg-gray-100 dark:bg-gray-800 mb-4">
        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
            ({{ $count }}) {!! TranslationHelper::translateIfNeeded('Flights') !!}
        </h2>
    </div>
    @foreach ($flights as $item)
        <div class="mb-4">
            <!-- Container utama dengan lebar lebih besar di bagian atas -->
            <div
                class="overflow-x-auto border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-full mx-auto mb-4 shadow-lg">
                <div class="flex flex-nowrap space-x-4 min-w-max flex-col">
                    <div class="flex flex-nowrap space-x-4 min-w-max flex-1 p-2">
                        <div class="flex-1 min-w-[300px] mb-2 border-r border-gray-300 pr-4">

                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Name') !!}
                            </p>
                            @if ($item->shared != 0)
                                <a
                                    href="{{ route('filament.admin.resources.flighs.view', [
                                        'tenant' => Auth()->user()->teams()->first()->id,
                                        'record' => $item->id,
                                    ]) }}">
                                    <p class="text-sm text-gray-700 dark:text-gray-400" style="color:rgb(0, 85, 255)">
                                        {{ $item->name ?? null }}</p>
                                </a>
                            @else
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->name ?? null }}</p>
                            @endif

                            <div class="flex justify-between items-center">
                                <p class="text-sm text-gray-700 dark:text-gray-400 border-r pr-4">
                                    {{ $item->start_date_flight ?? null }}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">
                                    {{ $item->duration ?? null }}
                                </p>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Pilot:') !!}
                                {{ $item->users->name ?? null }}</p>
                        </div>

                        <div class="flex-1 min-w-[300px] mb-2 border-r border-gray-300 pr-4">
                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone') !!}
                            </p>
                            @if ($item->drones->shared != 0)
                                <a
                                    href="{{ route('filament.admin.resources.drones.view', [
                                        'tenant' => Auth()->user()->teams()->first()->id,
                                        'record' => $item->drones->id,
                                    ]) }}">
                                    <p class="text-sm text-gray-700 dark:text-gray-400" style="color:rgb(0, 85, 255)">
                                        {{ $item->drones->name }}</p>
                                </a>
                            @else
                                <p class="text-sm text-gray-700 dark:text-gray-400" style="color:rgb(0, 85, 255)">
                                    {{ $item->drones->name }}
                            @endif
                            <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->drones->brand }} /
                                {{ $item->drones->model }}</p>
                        </div>

                        <div class="flex-1 min-w-[300px] mb-2 border-r border-gray-300 pr-4">
                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Customer') !!}
                            </p>
                            <a
                                href="{{ route('filament.admin.resources.customers.view', [
                                    'tenant' => Auth()->user()->teams()->first()->id,
                                    'record' => $item->customers->id,
                                ]) }}">
                                <p class="text-sm text-gray-700 dark:text-gray-400" style="color:rgb(0, 85, 255)">
                                    {{ $item->customers->name }}</p>
                            </a>
                        </div>

                        <div class="flex-1 min-w-[300px] mb-2 border-r border-gray-300 pr-4">
                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Location') !!}
                            </p>
                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                {{ $item->fligh_location->name ?? null }}
                            </p>
                        </div>

                        <div class="flex-1 min-w-[180px]">
                            <button
                                class="inline-block text-sm text-white rounded px-4 py-3 transition-all duration-300 ease-in-out bg-gray-400 dark:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white dark:hover:text-white focus:outline-none"
                                onclick="showContent({{ $item->id }})">
                                {!! TranslationHelper::translateIfNeeded('More Info') !!}
                            </button>

                        </div>

                    </div>
                    <!-- Bagian konten tambahan yang tersembunyi -->
                    <div id="main-content-{{ $item->id }}" class="main-content px-2 flex-1" style="display: none">
                        <!-- Container pertama dengan ukuran lebih besar -->
                        <div
                            class="flex flex-nowrap space-x-4 min-w-max  justify-between py-4 px-6 border-t border-gray-400 bg-gray-300 dark:bg-gray-700  shadow-lg">
                            <div class="flex-1 min-w-[300px] mb-2 border-r border-gray-300 pr-4">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">
                                    {!! TranslationHelper::translateIfNeeded('Landing') !!}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->landings ?? null }}</p>
                            </div>
                            <div class="flex-1 min-w-[180px">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">
                                    {!! TranslationHelper::translateIfNeeded('Type') !!}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->type ?? null }}</p>
                            </div>
                            <div class="flex-1 min-w-[300px] mb-2 border-r border-gray-300 pr-4">
                                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">
                                    {!! TranslationHelper::translateIfNeeded('Operation') !!}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->ops ?? null }}</p>
                            </div>
                        </div>

                        <!-- Container kedua dengan ukuran lebih kecil -->
                        <div
                            class="flex items-center justify-between py-4 px-6 border-t border-gray-400 bg-gray-300 dark:bg-gray-700  shadow-lg">
                            <div class="flex-1 min-w-[300px] mb-2 border-r border-gray-300 pr-4">
                                <p class="text-sm text-gray-700 dark:text-gray-400"><strong>{!! TranslationHelper::translateIfNeeded('Personnel:') !!}
                                    </strong>{{ $item->users->name }} (Pilot) {{ $item->instructors->name ?? null }}
                                    (Instructor)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <div class="mt-4">
        {{ $flights->links() }}
    </div>

@endif
{{-- pindah ke view location --}}
@php
    if (!request()->routeIs('filament.admin.resources.fligh-locations.view')) {
        header(
            'Location: ' .
                route('filament.admin.resources.fligh-locations.view', [
                    'tenant' => $teams,
                    'record' => $locations,
                ]),
        );
        exit();
    }
@endphp
<script>
    @if (!request()->routeIs('filament.admin.resources.fligh-locations.view'))
        window.location.href =
            "{{ route('filament.admin.resources.fligh-locations.view', ['tenant' => $teams, 'record' => $locations]) }}";
    @endif
</script>
{{-- end pindah ke view location --}}
<script>
    function showContent(id) {
        const contents = document.querySelectorAll('.main-content');
        const contentToShow = document.getElementById(`main-content-${id}`);
        if (contentToShow) {
            if (contentToShow.style.display === 'block') {
                contentToShow.style.display = 'none';
            } else {
                contentToShow.style.display = 'block';
            }
        }
    }
</script>
