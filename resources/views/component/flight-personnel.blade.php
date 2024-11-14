<?php
$teams = Auth()->user()->teams()->first()->id;
$personnel = session('personnel_id');
$flights = App\Models\Fligh::where('teams_id', $teams)->where('users_id',$personnel)->get();
$count = $flights->count('id');
?>
<head>
<style>
    .main-content.active{
        display: block;
    }
</style>
</head>
@if(request()->routeIs('filament.admin.resources.users.view'))
<div class="flex items-center justify-between py-4 px-6 border-b border-gray-300 bg-gray-100 dark:bg-gray-800 mb-4">
    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
        {{$count}} Flights
    </h2>
</div>
    @foreach($flights as $item)
    <div class="mb-4">
        <!-- Container utama dengan lebar lebih besar di bagian atas -->
        <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[900px] mx-auto shadow-lg">
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-4">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Name</p>
                <a href="{{route('filament.admin.resources.flighs.view',
                        ['tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $item->id,])}}">
                    <p class="text-sm text-gray-700 dark:text-gray-400" style="color:rgb(0, 85, 255)">{{$item->name ?? null}}</p>
                </a>
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-700 dark:text-gray-400 border-r pr-4">
                        {{$item->duration ?? null}}
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">
                        {{$item->start_date_flight ?? null}}
                    </p>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-400">Pilot: {{$item->users->name ?? null}}</p>
            </div>
        
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-4 px-4">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Drone</p>
                <a href="{{route('filament.admin.resources.drones.view',
                ['tenant' => Auth()->user()->teams()->first()->id,
                'record' => $item->drones->id,])}}"><p class="text-sm text-gray-700 dark:text-gray-400" style="color:rgb(0, 85, 255)">{{$item->drones->name}}</p></a>  
                <p class="text-sm text-gray-700 dark:text-gray-400" >{{$item->drones->brand}} / {{$item->drones->model}}</p>
            </div>
        
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-2">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Customer</p>
                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->customers->name ?? null}}</p>
            </div>
        
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-4">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Location</p>
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
                More Info
            </button>
            
            </div>
        </div>
        
        <!-- Bagian konten tambahan yang tersembunyi -->
        <div id="main-content-{{ $item->id }}" class="main-content px-2" style="display: none">
            <!-- Container pertama dengan ukuran lebih besar -->
            <div class="flex flex-wrap justify-between py-4 px-6 border-t border-gray-400 bg-gray-300 dark:bg-gray-700  shadow-lg">
                <div class="flex-1 min-w-[180px]">
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Landing</p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->landings ?? null}}</p>
                </div>
                <div class="flex-1 min-w-[180px">
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Type</p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->type ?? null}}</p>
                </div>
                <div class="flex-1 min-w-[180px]">
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Operation</p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->ops ?? null}}</p>
                </div>
            </div>
        
            <!-- Container kedua dengan ukuran lebih kecil -->
            <div class="flex items-center justify-between py-4 px-6 border-t border-gray-400 bg-gray-300 dark:bg-gray-700  shadow-lg">
                <div class="flex-1 min-w-[180px]">
                    <p class="text-sm text-gray-700 dark:text-gray-400"><strong>Personnel: </strong>{{$item->users->name}} (Pilot) {{$item->instructor}} (Instructor)</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif
{{-- pindah ke view project --}}
@php
    if (!request()->routeIs('filament.admin.resources.users.view')) {
        header("Location: " . route('filament.admin.resources.users.view', [
            'tenant' => $teams,
            'record' => $personnel
        ]));
        exit();
    }
@endphp
<script>
    @if(!request()->routeIs('filament.admin.resources.users.view'))
        window.location.href = "{{ route('filament.admin.resources.users.view', ['tenant' => $teams, 'record' => $personnel]) }}";
    @endif
</script>
{{-- end pindah ke view user --}}
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