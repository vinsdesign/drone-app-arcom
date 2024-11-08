<?php
//convert $record to int
    $teams = Auth()->user()->teams()->first()->id;
    $id= $record->id;
    $customers = App\Models\Customer::where('teams_id', $teams)->where('id',$id)->get();
    // $customers = App\Models\Customer::where('teams_id', $teams)->get();
?>
<head>
<style>
    .main-content.active{
        display: block;
    }
</style>
{{-- </head>
@foreach($record as $id)
<div>{{$id->id}}</div>
@endforeach --}}
@props(['record'])
{{-- <div>{{ $record ? 'Data ditemukan' : 'Data kosong' }}</div> --}}
{{-- <div>{{$record->id}}</div> --}}
@foreach($customers as $item)
    <div class="mb-4" style="min-width: 120dvh">
        <!-- Container utama dengan lebar lebih besar di bagian atas -->
        <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[900px] mx-auto shadow-lg">
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-4">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Name</p>
                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name ?? null}}</p>
            </div>
        
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-4 px-4">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Contact</p>
                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->phone}}</p>
                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->email}}</p>
            </div>
        
            <div class="flex-1 min-w-[180px] border-r border-gray-300 pr-2">
                <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Address</p>
                <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->address}}</p>
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
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">Description : </p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->description ?? null}}</p>
                </div>
            </div>
        </div>
    </div>
@endforeach
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