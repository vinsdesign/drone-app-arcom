<?php
use Stichoza\GoogleTranslate\GoogleTranslate;

$currentTeamId = auth()->user()->teams()->first()->id;
    
    $drones = App\Models\drone::where('teams_id', $currentTeamId)
        ->where('status', 'airworthy')
        ->pluck('name', 'id');
        
    $projects = App\Models\Projects::where('teams_id', $currentTeamId)->pluck('case', 'id');
    
    $locations = App\Models\fligh_location::where('teams_id', $currentTeamId)->pluck('name', 'id');
    
    $batteries = App\Models\battrei::where('teams_id', $currentTeamId)
        ->whereDoesntHave('kits')
        ->pluck('name', 'id');
        
    $equipments = App\Models\equidment::where('teams_id', $currentTeamId)
        ->where('status', 'airworthy')
        ->whereDoesntHave('kits')
        ->pluck('name', 'id');
    
    $pilots = App\Models\User::whereHas('teams', function ($query) use ($currentTeamId) {
        $query->where('team_id', $currentTeamId);
    })->whereHas('roles', function ($query) {
        $query->where('roles.name', 'Pilot');
    })->pluck('name', 'id');
?>
<x-filament-panels::page>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .main-content {
    display: none; /* Initially hidden */
    padding: 20px;
    border-radius: 8px;
    animation: fadeIn 0.5s ease;
    }
    .main-content.active {
        display: block; /* Displayed when active */
    }
    button.active{
        background-color: #ecefef;
    }
</style>
<script src="https://cdn.tailwindcss.com"></script>
</head>
{{-- @php 
$test =Auth()->user()->roles()->pluck('name');    
dd($test)
@endphp --}}
<div class="block min-h-screen w-full">
    <div class="w-full p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <!-- Top Bar -->
        <div class="flex justify-between items-center bg-gray-700 dark:bg-gray-900 text-white p-4 rounded-t-lg">
            <div class="flex items-center space-x-2">
                <label for="log-type" class="font-semibold">{!! GoogleTranslate::trans('Log Type:', session('locale') ?? 'en') !!}</label>
                <select id="log-type" class="bg-gray-200 text-black dark:bg-gray-700 dark:text-gray-100 border-none rounded p-2">
                    <option value="airdata">AirData CSV</option>
                    <option value="aeromapper">Aeromapper.DAT</option>
                    <option value="airspace">Airspace.CSV</option>
                    <option value="anzu">Anzu.Json</option>
                    <option value="apm">APM.LOG</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button class=" tab-buttom active px-4 py-2 bg-gray-500 text-gray-900 dark:text-gray-100 font-semibold rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700" onclick="showContent(0)">{!! GoogleTranslate::trans('Single Log', session('locale') ?? 'en') !!}</button>
                <button class="tab-buttom px-4 py-2 bg-gray-500 text-gray-900 font-semibold rounded-lg hover:bg-gray-600 dark:hover:bg-gray-800"onclick="showContent(1)">{!! GoogleTranslate::trans('Multiple Logs', session('locale') ?? 'en') !!}</button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-6 text-left main-content active">
            <label for="log-file" class="block text-lg font-semibold mb-2">{!! GoogleTranslate::trans('Select your log file:', session('locale') ?? 'en') !!}</label>
            <div class="flex items-center space-x-4">
                <input type="file" id="log-file" class="bg-gray-200 dark:bg-gray-700 dark:text-gray-100 border rounded p-2 w-2/3">
                <button class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold rounded-lg border hover:bg-gray-300 dark:hover:bg-gray-800">
                    {!! GoogleTranslate::trans('Analyze Flight Log', session('locale') ?? 'en') !!}
                </button>
            </div>
        </div>
        {{-- Multiple importer --}}
        <div class="p-6 text-left main-content bg-white dark:bg-gray-800">
            <h1 class="block text-lg font-semibold mb-2 drak::text-gray-100" >{!! GoogleTranslate::trans('Set Data To Imports', session('locale') ?? 'en') !!}</h1>
            <div class="container mx-auto p-4">
                <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @csrf
                    <!-- Drones Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="drones_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! GoogleTranslate::trans('Drones', session('locale') ?? 'en') !!}</label>
                        <select name="drones_id" id="drones_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            @foreach($drones as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    <!-- Flight Type Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! GoogleTranslate::trans('Flight Type', session('locale') ?? 'en') !!}</label>
                        <select name="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option value="">-- None --</option>
                            <option value="commercial-agriculture">Commercial-Agriculture</option>
                            <option value="commercial-inspection">Commercial-Inspection</option>
                            <option value="commercial-mapping/survey">Commercial-Mapping/Survey</option>
                            <option value="commercial-other">Commercial-Other</option>
                            <option value="commercial-photo/video">Commercial-Photo/Video</option>
                            <option value="emergency">Emergency</option>
                            <option value="hobby-entertainment">Hobby-Entertainment</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="mapping_hr">Mapping HR</option>
                            <option value="mapping_uhr">Mapping UHR</option>
                            <option value="photogrammetry">Photogrammetry</option>
                            <option value="science">Science</option>
                            <option value="search_rescue">Search and Rescue</option>
                            <option value="simulator">Simulator</option>
                            <option value="situational_awareness">Situational Awareness</option>
                            <option value="spreading">Spreading</option>
                            <option value="surveillance/patrol">Surveillance or Patrol</option>
                            <option value="survey">Survey</option>
                            <option value="test_flight">Test Flight</option>
                            <option value="training_flight">Training Flight</option> 
                        </select>
                    </div>
        
                    <!-- Project Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="projects_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! GoogleTranslate::trans('Project', session('locale') ?? 'en') !!}</label>
                        <select name="projects_id" id="projects_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            @foreach($projects as $id => $case)
                                <option value="{{ $id }}">{{ $case }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    <!-- Customer Name -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="customers_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! GoogleTranslate::trans('Customer Name', session('locale') ?? 'en') !!}</label>
                        <input type="text" name="customers_name" id="customers_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300" value="{{ old('customers_name', $currentTeam->getNameCustomer->name ?? '') }}" disabled>
                    </div>
        
                    <!-- Location Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="location_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! GoogleTranslate::trans('Location', session('locale') ?? 'en') !!}</label>
                        <select name="location_id" id="location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            @foreach($locations as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    <!-- Battery Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="battreis" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! GoogleTranslate::trans('Battery', session('locale') ?? 'en') !!}</label>
                        <select name="battreis[]" id="battreis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            @foreach($batteries as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    <!-- Equipment Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="equidments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! GoogleTranslate::trans('Equipment', session('locale') ?? 'en') !!}</label>
                        <select name="equidments[]" id="equidments" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            @foreach($equipments as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    <!-- Pilot Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="users_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! GoogleTranslate::trans('Pilot', session('locale') ?? 'en') !!}</label>
                        <select name="users_id" id="users_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            @foreach($pilots as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    <!-- Image Upload -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! GoogleTranslate::trans('Image', session('locale') ?? 'en') !!}</label>
                        <input type="file" id="image" name="file" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
        
                    <!-- Submit Button -->
                    <div class="col-span-1 lg:col-span-1 flex justify-end mt-4">
                        <button class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold rounded-lg border hover:bg-gray-300 dark:hover:bg-gray-800">
                            {!! GoogleTranslate::trans('Analyze Flight Log', session('locale') ?? 'en') !!}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- end Multiple Importer --}}
    </div>
</div>


</x-filament-panels::page>
<Script>
    function showContent(index) {
        const contents = document.querySelectorAll('.main-content');
        const buttomContent = document.querySelectorAll('.tab-buttom');
        contents.forEach((content, i) => {
            content.classList.remove('active');
            if (i === index) {
                content.classList.add('active');
            }
        });
        buttomContent.forEach((buttomContent, i) => {
           buttomContent.classList.remove('active');
            if (i === index) {
               buttomContent.classList.add('active');
            }
        });
    }
</Script>