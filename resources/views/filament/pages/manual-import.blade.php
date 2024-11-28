<?php
use App\Helpers\TranslationHelper;

$currentTeamId = auth()->user()->teams()->first()->id;
    
    $drones = App\Models\drone::where('teams_id', $currentTeamId)
        ->where('status', 'airworthy')
        ->pluck('name', 'id');
        
    $projects = App\Models\Projects::where('teams_id', $currentTeamId)->pluck('case', 'id');
    
    $locations = App\Models\fligh_location::where('teams_id', $currentTeamId)->pluck('name', 'id');
    
    $batteries = App\Models\battrei::where('teams_id', $currentTeamId)
        ->where('status', 'airworthy')
        ->whereDoesntHave('kits')
        ->pluck('name', 'id');
    
        
    $equipments = App\Models\equidment::where('teams_id', $currentTeamId)
        ->where('status', 'airworthy')
        ->whereDoesntHave('kits')
        ->pluck('name', 'id');
    // dd($batteries, $equipments);
    
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
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
                <label for="log-type" class="font-semibold">{!! TranslationHelper::translateIfNeeded('Log Type: ') !!}</label>
                <select id="log-type" class="bg-gray-200 text-black dark:bg-gray-700 dark:text-gray-100 border-none rounded p-2">
                    <option value="airdata">AirData CSV</option>
                    <option value="aeromapper">Aeromapper.DAT</option>
                    <option value="airspace">Airspace.CSV</option>
                    <option value="anzu">Anzu.Json</option>
                    <option value="apm">APM.LOG</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button class=" tab-buttom active px-4 py-2 bg-gray-500 text-gray-900 dark:text-gray-100 font-semibold rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700" onclick="showContent(0)">{!! TranslationHelper::translateIfNeeded('Single Log') !!}</button>
                <button class="tab-buttom px-4 py-2 bg-gray-500 text-gray-900 font-semibold rounded-lg hover:bg-gray-600 dark:hover:bg-gray-800"onclick="showContent(1)">{!! TranslationHelper::translateIfNeeded('Multiple Log') !!}</button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-6 text-left main-content active">
            <label for="log-file" class="block text-lg font-semibold mb-2">{!! TranslationHelper::translateIfNeeded('Select your log file:') !!}</label>
            <div class="flex items-center space-x-4">
                <input type="file" id="log-file" class="bg-gray-200 dark:bg-gray-700 dark:text-gray-100 border rounded p-2 w-2/3">
                <button class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold rounded-lg border hover:bg-gray-300 dark:hover:bg-gray-800">
                    {!! TranslationHelper::translateIfNeeded('Analyze Flight Log') !!}
                </button>
            </div>
        </div>
        {{-- Multiple importer --}}
        <div class="p-6 text-left main-content bg-white dark:bg-gray-800">
            <h1 class="block text-lg font-semibold mb-2 drak::text-gray-100" >{!! TranslationHelper::translateIfNeeded('Set data to imports') !!}</h1>
            <div class="container mx-auto p-4">
                <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @csrf
                    <!-- Drones Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="drones_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Drones') !!}</label>
                        <select name="drones_id" id="drones_id" class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($drones as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
        
                    <!-- Flight Type Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Flight Type') !!}</label>
                        <select name="type" id="type" class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>-- Select an type Flight --</option>
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
                        <label for="projects_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Project') !!}</label>
                        <select name="projects_id" id="projects_id" class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>-- Select an Peoject --</option>
                            @if($projects->count() == null )
                            <option value="" disabled>No project available</option>
                            @else
                                @foreach($projects as $id => $case)
                                    <option value="{{ $id }}">{{ $case }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
        
                    <!-- Customer Name -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="customers_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Customer Name') !!}</label>
                        <input type="text" name="customers_name" id="customers_name" class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('customers_name', $currentTeam->getNameCustomer->name ?? '') }}" disabled>
                    </div>
        
                    <!-- Location Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="location_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Location') !!}</label>
                        <select name="location_id" id="location_id" class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Select an location</option>
                            @if($locations->count() == null)
                                <option value="" disabled>No location available</option>
                            @else
                                @foreach($locations as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
        
                    <!-- Battery Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="battreis" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Battery') !!}</label>
                        <select name="battreis[]" id="battreis" class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Select an Batteries</option>
                            @if($batteries->count() == null)
                                <option value="" disabled>No battery available</option>
                            @else
                                @foreach($batteries as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
        
                    <!-- Equipment Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="equidments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Equipment') !!}</label>
                        <select name="equidments[]" id="equidments" class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Select an Equipment</option>
                            @if($equipments->count() == null)
                                <option value="" disabled>No equipment available</option>
                            @else
                                @foreach($equipments as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
        
                    <!-- Pilot Select -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="users_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Pilot') !!}</label>
                        <select name="users_id" id="users_id" class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Select a Pilot</option>
                            @if($pilots->count() == null)
                                <option value="" disabled>No pilot available</option>
                            @else
                                @foreach($pilots as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
        
                    <!-- Image Upload -->
                    <div class="col-span-1 lg:col-span-1">
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Image') !!}</label>
                        <input type="file" id="image" name="file" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
        
                    <!-- Submit Button -->
                    <div class="col-span-1 lg:col-span-1 flex justify-end mt-4">
                        <button class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold rounded-lg border hover:bg-gray-300 dark:hover:bg-gray-800">
                            {!! TranslationHelper::translateIfNeeded('Analyze Flight Log') !!}
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