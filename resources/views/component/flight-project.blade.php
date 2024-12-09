<?php
use App\Helpers\TranslationHelper;
$teams = Auth()->user()->teams()->first()->id;
$projects = session('project_id');
$flights = App\Models\Fligh::where('teams_id', $teams)->where('projects_id',$projects)->paginate(5);
$flightCount = App\Models\Fligh::where('teams_id', $teams)->where('projects_id',$projects)->get();
$count = $flightCount->count('id');
?>
<head>
<style>
    .main-content.active{
        display: block;
    }
</style>
</head>
@if(request()->routeIs('filament.admin.resources.projects.view'))
<div class="flex items-center justify-between py-4 px-6 border-b border-gray-300 bg-gray-100 dark:bg-gray-800 mb-4">
    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
        {{$count}} {!! TranslationHelper::translateIfNeeded('Flights')!!}
    </h2>
</div>
    @foreach($flights as $item)
    <div class="mb-4">
        <!-- Container utama dengan lebar lebih besar di bagian atas -->
        <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[900px] mx-auto shadow-lg">
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-4">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Name')!!}</p>
                <a href="{{route('filament.admin.resources.flighs.view',
                        ['tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $item->id,])}}">
                    <p class="text-sm text-gray-700 dark:text-gray-400" style="color:rgb(0, 85, 255)">{{$item->name ?? null}}</p>
                </a>
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-700 dark:text-gray-400 border-r pr-4">
                        {{$item->start_date_flight ?? null}}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">
                        {{$item->duration ?? null}}
                    </p>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Pilot:')!!} {{$item->users->name ?? null}}</p>
            </div>
        
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-4 px-4">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone')!!}</p>
                <a href="{{route('filament.admin.resources.drones.view',
                ['tenant' => Auth()->user()->teams()->first()->id,
                'record' => $item->drones->id,])}}"><p class="text-sm text-gray-700 dark:text-gray-400" style="color:rgb(0, 85, 255)">{{$item->drones->name}}</p></a>  
                <p class="text-sm text-gray-700 dark:text-gray-400" >{{$item->drones->brand}} / {{$item->drones->model}}</p>
            </div>
        
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-2">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Customer')!!}</p>
                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->customers->name ?? null}}</p>
            </div>
        
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-4">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Location')!!}</p>
                <a href="{{route('filament.admin.resources.flighs.view',
                        ['tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $item->fligh_location->id,])}}">
                         <p class="text-sm text-gray-700 dark:text-gray-400" style="color:rgb(0, 85, 255)">{{$item->fligh_location->name ?? null}}</p>
                        </a>
               
            </div>
            
            <div class="flex-1 min-w-[180px]">
                <button
                class="inline-block text-sm text-white rounded px-4 py-3 transition-all duration-300 ease-in-out bg-gray-400 dark:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white dark:hover:text-white focus:outline-none"
                onclick="showContent({{ $item->id }})">
                {!! TranslationHelper::translateIfNeeded('More Info')!!}
            </button>
            
            </div>
        </div>
        
        <!-- Bagian konten tambahan yang tersembunyi -->
        <div id="main-content-{{ $item->id }}" class="main-content px-2" style="display: none">
            <!-- Container pertama dengan ukuran lebih besar -->
            <div class="flex flex-wrap justify-between py-4 px-6 border-t border-gray-400 bg-gray-300 dark:bg-gray-700  shadow-lg">
                <div class="flex-1 min-w-[180px]">
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Landing')!!}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->landings ?? null}}</p>
                </div>
                <div class="flex-1 min-w-[180px">
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Type')!!}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->type ?? null}}</p>
                </div>
                <div class="flex-1 min-w-[180px]">
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Operation')!!}</p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->ops ?? null}}</p>
                </div>
            </div>
        
            <!-- Container kedua dengan ukuran lebih kecil -->
            <div class="flex items-center justify-between py-4 px-6 border-t border-gray-400 bg-gray-300 dark:bg-gray-700  shadow-lg">
                <div class="flex-1 min-w-[180px]">
                    <p class="text-sm text-gray-700 dark:text-gray-400"><strong>{!! TranslationHelper::translateIfNeeded('Personnel:')!!} </strong>{{$item->users->name}} (Pilot) {{$item->instructor}} (Instructor)</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <div class="mt-4">
        {{ $flights->links() }}
    </div>
@endif
{{-- pindah ke view project --}}
@php
    if (!request()->routeIs('filament.admin.resources.projects.view')) {
        header("Location: " . route('filament.admin.resources.projects.view', [
            'tenant' => $teams,
            'record' => $projects
        ]));
        exit();
    }
@endphp
<script>
    @if(!request()->routeIs('filament.admin.resources.projects.view'))
        window.location.href = "{{ route('filament.admin.resources.projects.view', ['tenant' => $teams, 'record' => $projects]) }}";
    @endif
</script>
{{-- end pindah ke view project --}}
<script>
function showContent(id) {
    const contents = document.querySelectorAll('.main-content');
    const contentToShow = document.getElementById(`main-content-${id}`);
    if (contentToShow) {
        if (contentToShow.style.display === 'block') {
            contentToShow.style.display = 'none';
        } 
        else {
            contentToShow.style.display = 'block';
        }
    }
}
</script>