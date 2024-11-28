<?php
use App\Helpers\TranslationHelper;

$currentTeamId = auth()->user()->teams()->first()->id;
    
    $drones = App\Models\drone::where('teams_id', $currentTeamId)
        ->where('status', 'airworthy')
        ->pluck('name', 'id');
    // dd($drones);
        
    $projects = App\Models\Projects::where('teams_id', $currentTeamId)->pluck('case', 'id');
    
    $batteriesEquipment = App\Models\battrei::where('teams_id', $currentTeamId)
        ->where('status', 'airworthy')
        ->whereDoesntHave('kits')
        ->pluck('name', 'id')
        ->merge(
        App\Models\equidment::where('teams_id', $currentTeamId)
            ->where('status', 'airworthy')
            ->whereDoesntHave('kits')
            ->pluck('name', 'id')
    );
    // dd($batteriesEquipment);
    
        
    // $equipments = App\Models\equidment::where('teams_id', $currentTeamId)
    //     ->where('status', 'airworthy')
    //     ->whereDoesntHave('kits')
    //     ->pluck('name', 'id');
    
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
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
    .active{
        display: none;
    }
    .tob-bar, .tob-bar-login {
    display: none;
}

.tob-bar.active, .tob-bar-login.active {
    display: block;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
</head>
{{-- add data content --}}
    <div class="fixed active inset-0 flex justify-center z-50" style="max-height: 80%">
        <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
            <!-- Tombol Close -->
            <button type="button"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                onclick="closeModal()">
                &times;
            </button>

            <!-- Judul Modal -->
            <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                {!! TranslationHelper::translateIfNeeded('Add Data to Import')!!}
            </h2>
            <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

            <!-- Form -->
            <div class="p-4">
                @csrf
                    <!-- Drone -->
                <div class="mb-2">
                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Drone')!!}</label>
                    <Select id="drone" name="drone" class="w-full mt-1 p-2 border rounded-md focus:ring focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 bg-white border-gray-300 text-gray-900"> 
                        <option value="" disabled selected>Select an drone</option>
                        @foreach($drones as $id => $name)
                            <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </Select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">

                    <!-- Flight Type -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Flight Type')!!}</label> 
                        <Select id="flight_type" name="flight_type" class="w-full mt-1 p-2 border rounded-md focus:ring focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 bg-white border-gray-300 text-gray-900"> 
                            <option value="" disabled selected>Select an Type Flight</option>
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

                            
                        </Select>
                    </div>

                    <!-- Project -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Projects')!!}</label>
                        <select id="project" name="project" class="w-full mt-1 p-2 border rounded-md focus:ring focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 bg-white border-gray-300 text-gray-900">
                            <option value="" disabled selected>Select an project</option>
                            @foreach($projects as $id => $case)
                                <option value="{{$id}}">{{$case}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Battery & Equipment On-Board -->
                <div class="mb-2">
                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Battery & Equipment On-Board')!!}</label>
                    <x-filament::input.wrapper class="dark:bg-gray-900 dark:border-gray-700">
                        <x-filament::input.select 
                            wire:model="status" 
                            id="EquipmentBattrei" 
                            name="status"
                            class=" chosen-select w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500" 
                            multiple aria-placeholder="Select Equipment Or Battrei">
                            @foreach($batteriesEquipment as $id => $name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-4">
                    <button id="triggerButton" type="button" 
                        class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                    hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                    focus:ring-gray-500 dark:focus:ring-gray-300" onclick="closeModal()">
                        {!! TranslationHelper::translateIfNeeded('Submit')!!}
                    </button>
                </div>
                
            </div>
        </div>
    </div>
{{-- end add data content --}}
<div class="block min-h-screen w-full">
    <div class="w-full p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <!-- Top Bar -->
        <div id="tob-bar" class="tob-bar active bg-gray-700 dark:bg-gray-900 text-white p-4 rounded-t-lg">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <!-- Email-->
                <div class="flex items-center space-x-2">
                    <label for="email" class="font-semibold text-sm">
                        {!! TranslationHelper::translateIfNeeded('Your DJI Account Email: ') !!}
                    </label>
                    <input type="email" name="email" id="email" 
                           class="bg-gray-200 text-black dark:bg-gray-700 dark:text-white
                                  border-none rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <!-- Password-->
                <div class="flex items-center space-x-2">
                    <label for="password" class="font-semibold text-sm">
                        {!! TranslationHelper::translateIfNeeded('Password: ') !!}
                    </label>
                    <input type="password" name="password" id="password" 
                           class="bg-gray-200 text-black dark:bg-gray-700 dark:text-white
                                  border-none rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <!-- Login Button -->
                <div>
                    <button class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                                   hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                   focus:ring-gray-500 dark:focus:ring-gray-300" onclick="closeLogin()">
                        {!! TranslationHelper::translateIfNeeded('Login') !!}
                    </button>
                </div>
            </div>
        </div>
        <!--Top Bar if Login-->
        <div id="tob-bar-login" class="tob-bar-login bg-gray-700 dark:bg-gray-900 text-white p-4 rounded-t-lg">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <!-- Account-->
                <div class="flex items-center space-x-2">
                    <p class="font-semibold text-sm">
                        {!! TranslationHelper::translateIfNeeded('Your DJI Account: Name Account') !!}
                    </p>
                    <button class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                    hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                    focus:ring-gray-500 dark:focus:ring-gray-300" onclick="openLogin()">
                        {!! TranslationHelper::translateIfNeeded('Disconnect') !!}
                    </button>

                </div>
                <!--Date Sncy-->
                <div class="flex items-center space-x-2">
                    <label for="password" class="font-semibold text-sm">
                        {!! TranslationHelper::translateIfNeeded('Sync Flight Since: ') !!}
                    </label>
                    <input type="date" name="date" id="date" 
                           class="bg-gray-200 text-black dark:bg-gray-700 dark:text-white
                                  border-none rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    {{-- button flight list --}}
                    <button class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                    hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                    focus:ring-gray-500 dark:focus:ring-gray-300">
                       {!! TranslationHelper::translateIfNeeded('Get Flight List') !!}
                   </button>
                   {{-- button add data --}}
                   <button class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                   hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                   focus:ring-gray-500 dark:focus:ring-gray-300" onclick="openModal()">
                      {!! TranslationHelper::translateIfNeeded('Add Data To Import') !!}
                  </button>

                </div>
            </div>
        </div>
        {{-- tabel --}}
        <div class="mb-2">
            
                    <div class="mt-4 flex justify-end mb-4">
                        <a href="#" class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                    hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                    focus:ring-gray-500 dark:focus:ring-gray-300">{!! TranslationHelper::translateIfNeeded('Import All')!!}</a>
                    </div>
                
                    <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 mx-auto mb-4 shadow-lg p-4">
                        
                        <!-- Kolom Name -->
                        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2 overflow-hidden">
                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold truncate">{!! TranslationHelper::translateIfNeeded('DJIFLightRecord_2024_09_10_[02:50:43].txt')!!}</p>
                        </div>
                
                        <!-- Date time-->
                        <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('2024-09-10 02:50:43')!!}</p>
                        </div>
                
                        <!-- Import -->
                        <div class="flex justify-center items-center min-w-[150px] mb-2">
                            <a href="#" target="_blank" rel="noopener noreferrer" 
                               class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                                      hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                      focus:ring-gray-500 dark:focus:ring-gray-300">
                                {!! TranslationHelper::translateIfNeeded('Import') !!}
                            </a>
                        </div>
                    </div>
        </div>
        {{-- end tabel --}} 

        

        {{-- <!-- Content Area -->
        <div class="p-6 text-left main-content active">
            <label for="log-file" class="block text-lg font-semibold mb-2">{!! TranslationHelper::translateIfNeeded('Select your log file:') !!}</label>
            <div class="flex items-center space-x-4">
                <input type="file" id="log-file" class="bg-gray-200 dark:bg-gray-700 dark:text-gray-100 border rounded p-2 w-2/3">
                <button class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold rounded-lg border hover:bg-gray-300 dark:hover:bg-gray-800">
                    {!! TranslationHelper::translateIfNeeded('Analyze Flight Log') !!}
                </button>
            </div>
        </div> --}}
        
        {{-- Multiple importer --}}
        {{-- <div class="p-6 text-left main-content bg-white dark:bg-gray-800">
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
        </div> --}}
        {{-- end Multiple Importer --}}
    </div>
</div>
</x-filament-panels::page>
{{-- <Script>
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
</Script> --}}

<script>
function closeLogin() {
    const topBar = document.querySelector('#tob-bar');
    const tobBarlogin = document.querySelector('#tob-bar-login');
    topBar.classList.remove('active');
    tobBarlogin.classList.add('active');
}

function openLogin() {
    const topBar = document.querySelector('#tob-bar');
    const tobBarlogin = document.querySelector('#tob-bar-login');
    topBar.classList.add('active');
    tobBarlogin.classList.remove('active');
}
</script>

<script>
    function closeModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.add('active');
    }
    function openModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.remove('active');     
    }
</script>

<script>
    $(document).ready(function(){
        var multipleCancelButton = new Choices('#EquipmentBattrei', {
            removeItemButton: true,
            maxItemCount: 10,          
            searchResultLimit: 10,
            renderChoiceLimit: 10
            
        }); 
    });
</script>