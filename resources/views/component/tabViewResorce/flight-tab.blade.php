<?php
    use App\Helpers\TranslationHelper;
    //project Document
    $id = $getRecord()->id;
    session_start();
    $media_id = session('idRecord');
    // dd($media_id);
    //media data
    $media =  App\Models\media_fligh::Where('fligh_id',$id)->where('id',$media_id)->first();
    $projectID = $getRecord()->projects_id;
  
    //document Project
    if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
        $queryDocument = App\Models\Document::query()->where('projects_id', $projectID)->where('status_visible', '!=', 'archived')->get();
    }else{
        $queryDocument = App\Models\Document::query()
        ->where('projects_id', $projectID)
        ->where('status_visible', '!=', 'archived')
        ->where(function ($query) {
            $query->where('shared', 1)
                ->orWhere('users_id', auth()->id());
        })->get();
    }
    
    $documentProjects =  $queryDocument;

    //flight Document
    if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
        $queryDocumentFlight = App\Models\Document::query()->where('scope','Flight')->where('status_visible', '!=', 'archived')->get();
    }else{
        $queryDocumentFlight = App\Models\Document::query()
        ->where('scope','Flight')
        ->where('status_visible', '!=', 'archived')
        ->where(function ($query) {
            $query->where('shared', 1)
                ->orWhere('users_id', auth()->id());
        })->get();
    }

    $documentFlight = $queryDocumentFlight;

    //FlightIncident
    $incidents = DB::table('incidents')
    ->join('drones', 'incidents.drone_id', '=', 'drones.id')
    ->join('fligh_locations', 'incidents.location_id', '=', 'fligh_locations.id')
    ->join('users', 'incidents.personel_involved_id', '=', 'users.id')
    ->join('flighs', function ($join) {
        $join->on('incidents.projects_id', '=', 'flighs.projects_id')
             ->on('incidents.location_id', '=', 'flighs.location_id')
             ->on('incidents.drone_id', '=', 'flighs.drones_id')
             ->whereRaw('DATE(incidents.incident_date) = DATE(flighs.start_date_flight)');
    })
    ->where('flighs.id',$id)
    ->select('incidents.*','fligh_locations.name as location_name','drones.name as drone_name','users.name as user_name')
    ->get();

    //fligh media
    $flighmedia = App\Models\media_fligh::Where('fligh_id',$id)->get();
    // dd($flighmedia);



?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Import at vite('resources/css/app.css') untuk tailwind perlu NPM-->
    {{-- @vite('resources/css/app.css') --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .active-modal{
            display: none;
        }
        .hidden-notif
        {
            display: none;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 50;
            display: block;
        }
        .tab-button:hover {
            background-color: #cbd5e1;
            color: #000;
        }
        .tab-button.active {
            background-color: #3b82f6;
            color: white;
        }

        /* Tab content styling */
        .tab-content {
            display: none;
            padding: 20px;
            margin-top: 10px;
            animation: fadeIn 0.5s ease;
        }
        .tab-content.active {
            display: block;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>

<div class="container bg-gray-200 flex flex-wrap space-x-4 border border-gray-300 rounded-lg dark:bg-gray-800">
    <!-- Tab headers -->
    <div class="container mx-auto p-5">

        <div class="flex flex-wrap items-center justify-between border border-gray-300 rounded-lg p-2 bg-black dark:bg-gray-900">
          
            <div class="flex items-center">
                <p class="text-xl font-bold text-white">{!! TranslationHelper::translateIfNeeded('Attachments') !!}</p>
            </div>
            {{-- button --}}
            <div class="flex flex-wrap gap-2">
                <button id="tab0" class="tab-button active text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto">
                    {!! TranslationHelper::translateIfNeeded('Flight Document') !!}
                </button>
                <button id="tab1" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto">
                    {!! TranslationHelper::translateIfNeeded('Project Document') !!}
                </button>
                <button id="tab2" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto">
                    {!! TranslationHelper::translateIfNeeded('Incident') !!}
                </button>
                <button id="tab3" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto">
                    {!! TranslationHelper::translateIfNeeded('Media') !!}
                </button>
            </div>

        </div>
        

        <!-- Tab content -->
        <div class="content">

            {{-- content Documment Flight--}}
            <div id="content0" class="tab-content active">

                <!-- Modal -->
                <div class="fixed active-modal inset-0 flex justify-center z-50" style="max-height: 80%">
                    <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
                        <!-- Tombol Close -->
                        <button type="button"
                            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                            onclick="closeModal()">
                                &times;
                        </button>

                        <!-- Judul Modal -->
                        <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                            {!! TranslationHelper::translateIfNeeded('Upload Flight Document')!!}
                        </h2>
                        <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">
                        {{-- error massages --}}
                        <div id="bodyErrorMassagesDflight" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <!-- Icon Error -->
                                    <svg class="h-5 w-5 text-red-400 dark:text-red-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0m1.414-1.414a9 9 0 0110.899 0m-5.7 5.8a2.25 2.25 0 10-3.18-3.181m0 0a2.25 2.25 0 013.18 3.181m-3.18-3.181L12 12m0 0l3.18-3.18" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm">
                                    <p class="font-medium text-red-800 dark:text-red-200">
                                        {!! TranslationHelper::translateIfNeeded('Error: ') !!}
                                        <span id="errorMassagesDflight"></span>
                                    </p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button type="button" onclick="closeMessages()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
                                        data-bs-dismiss="alert" aria-label="Close">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Form -->
                        <form id="documentFormFlight" enctype="multipart/form-data">
                            @csrf
                            <input id="ownerFlight" type="hidden" name="teams_idFlight" value="{{ auth()->user()->first()->id }}">
                            <input id="flight" type="hidden" name="recordIDFlight" value="Flight">
                    
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name Input -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Name') !!}</label>
                                    <input id="nameFlight" type="text" name="nameFlight" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                        
                                <!-- Expired Input -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Expiration Date') !!}</label>
                                    <input id="expiredDateFlight" type="date" name="expiredDateFlight" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                        
                                <!-- RefNumber Input -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Ref / Certificate #') !!}</label>
                                    <input id="refnumberFlight" type="text" name="refnumberFlight" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                        
                                <!-- External Link -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('External Link') !!}</label>
                                    <input id="externalLinkFlight" type="text" name="externalLinkFlight" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                            </div>
                        
                            <!-- Document -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('File Document') !!}</label>
                                <input id="dockFlight" type="file" name="dockFlight" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                        
                            <!-- Notes -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Notes') !!}</label>
                                <textarea id="descriptionFlight" name="descriptionFlight" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                            </div>
                        
                            <!-- Submit Button -->
                            <div class="flex justify-end mt-4">
                                <button id="triggerButtonFlight" type="button" class="button" style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">
                                    <span class="button__text">{!! TranslationHelper::translateIfNeeded('Submit') !!}</span>
                                </button>
                            </div>
                        </form>
                        
                    </div>
                </div>

                {{-- tabel --}}
                <div class="mb-2">
                    
                    <div class="mt-4 flex justify-end mb-4">
                        <button type="button" onclick="openModal()" class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                    hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                    focus:ring-gray-500 dark:focus:ring-gray-300">{!! TranslationHelper::translateIfNeeded('Add Flight Document')!!}</button>
                    </div>
                
                    @if($documentFlight->count() > 0)
                        @foreach($documentFlight as $item)

                            <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-800 mx-auto mb-4 shadow-lg p-4">
                                
                                <!-- column Name -->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2 overflow-hidden">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold truncate">{!! TranslationHelper::translateIfNeeded('Name : ')!!}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->name}}</p>
                                </div>
                        
                                <!-- Column Number Ref-->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Number :')!!}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->refnumber}}</p></p>
                                </div>

                                <!-- Column Expiration-->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Expiration : ')!!} <span class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->expired_date}}</span></p>
                                    
                                    @php
                                        $now = Carbon\Carbon::now();
                                        $Expired = Carbon\Carbon::createFromFormat('Y-m-d',$item->expired_date);
                                        $daysRemaining = $now->diffInDays($Expired, false);
                                        $daysRemaining = intval($daysRemaining);
                                    @endphp

                                    @if($daysRemaining > 0)
                                        <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">
                                            {!! TranslationHelper::translateIfNeeded('Expired In ')!!}{{$daysRemaining}}{!! TranslationHelper::translateIfNeeded(' Days')!!}
                                        </p>
                                    
                                    @elseif ($daysRemaining === 0) 
                                        <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">
                                            {!! TranslationHelper::translateIfNeeded('Hari ini adalah hari terakhir sebelum expired.')!!}
                                        </p>
                                    
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">
                                            {!! TranslationHelper::translateIfNeeded('Tanggal expired telah lewat ')!!}{{$daysRemaining}}{!! TranslationHelper::translateIfNeeded(' hari yang lalu')!!}
                                        </p>
                                    @endif
                                    

                                </div>

                                <!-- Column Modified-->
                                <div class="flex justify-end items-center mb-2 min-w-[150px] border-gray-300 pr-2">
                                    <a href="{{route('filament.admin.resources.documents.edit',['tenant' => Auth()->user()->teams()->first()->id, 'record' => $item->id])}}" class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                                        hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                        focus:ring-gray-500 dark:focus:ring-gray-300">
                                        {!! TranslationHelper::translateIfNeeded('Edit') !!}
                                    </a>
                                </div>
                        
                            
                            </div>
                        @endforeach
                    @else
                        <p class="text-center text-gray-600 dark:text-gray-300 mt-4">No Data Found</p>
                    @endif

                </div>
                {{-- end tabel --}} 

            </div>

            {{-- content Documment Project --}}
            <div id="content1" class="tab-content">
    
                <!-- Modal -->
                <div class="fixed active-modal flight-create inset-0 flex justify-center z-50" style="max-height: 80%">
                    <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
                        <!-- Tombol Close -->
                        <button type="button"
                            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                            onclick="closeModalFlight()">
                                &times;
                        </button>

                        <!-- Judul Modal -->
                        <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                            {!! TranslationHelper::translateIfNeeded('Upload Project Document')!!}
                        </h2>
                        <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">
                        {{-- error massages --}}
                        <div id="bodyErrorMassagesDproject" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <!-- Icon Error -->
                                    <svg class="h-5 w-5 text-red-400 dark:text-red-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0m1.414-1.414a9 9 0 0110.899 0m-5.7 5.8a2.25 2.25 0 10-3.18-3.181m0 0a2.25 2.25 0 013.18 3.181m-3.18-3.181L12 12m0 0l3.18-3.18" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm">
                                    <p class="font-medium text-red-800 dark:text-red-200">
                                        {!! TranslationHelper::translateIfNeeded('Error: ') !!}
                                        <span id="errorMassagesDproject"></span>
                                    </p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button type="button" onclick="closeMessages()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
                                        data-bs-dismiss="alert" aria-label="Close">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Form -->
                        <form id="documentForm" enctype="multipart/form-data">
                            @csrf
                            <input id="owner" type="hidden" name="teams_id" value="{{ auth()->user()->first()->id }}">
                            <input id="project" type="hidden" name="recordID" value="{{ $id }}">
                        
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name Input -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Name') !!}</label>
                                    <input id="name" type="text" name="name" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                        
                                <!-- Expired Input -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Expiration Date') !!}</label>
                                    <input id="expiredDate" type="date" name="expiredDate" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                        
                                <!-- RefNumber Input -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Ref / Certificate #') !!}</label>
                                    <input id="refnumber" type="text" name="refnumber" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                        
                                <!-- External Link -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('External Link') !!}</label>
                                    <input id="externalLink" type="text" name="externalLink" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                            </div>
                        
                            <!-- Document -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('File Document') !!}</label>
                                <input id="dock" type="file" name="dock" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                        
                            <!-- Notes -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Notes') !!}</label>
                                <textarea id="description" name="description" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                            </div>
                        
                            <!-- Submit Button -->
                            <div class="flex justify-end mt-4">
                                <button id="triggerButton" type="button" class="button" style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">
                                    <span class="button__text">{!! TranslationHelper::translateIfNeeded('Submit') !!}</span>
                                </button>
                            </div>
                        </form>
                        
                    </div>
                </div>

                {{-- tabel --}}
                <div class="mb-2">
                    
                    <div class="mt-4 flex justify-end mb-4">
                        <button type="button" onclick="openModalFlight()" class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                    hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                    focus:ring-gray-500 dark:focus:ring-gray-300">{!! TranslationHelper::translateIfNeeded('Upload Project Document')!!}</button>
                    </div>
                
                    @if($documentProjects->count() > 0)
                        @foreach($documentProjects as $item)

                            <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-800 mx-auto mb-4 shadow-lg p-4">
                                
                                <!-- column Name -->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2 overflow-hidden">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold truncate">{!! TranslationHelper::translateIfNeeded('Name : ')!!}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->name}}</p>
                                </div>
                        
                                <!-- Column Number Ref-->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Number :')!!}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->refnumber}}</p></p>
                                </div>

                                <!-- Column Expiration-->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Expiration : ')!!} <span class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->expired_date}}</span></p>
                                    
                                    @php
                                        $now = Carbon\Carbon::now();
                                        $Expired = Carbon\Carbon::createFromFormat('Y-m-d',$item->expired_date);
                                        $daysRemaining = $now->diffInDays($Expired, false);
                                        $daysRemaining = intval($daysRemaining);
                                    @endphp

                                    @if($daysRemaining > 0)
                                        <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">
                                            {!! TranslationHelper::translateIfNeeded('Expired In ')!!}{{$daysRemaining}}{!! TranslationHelper::translateIfNeeded(' Days')!!}
                                        </p>
                                    
                                    @elseif ($daysRemaining === 0) 
                                        <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">
                                            {!! TranslationHelper::translateIfNeeded('Hari ini adalah hari terakhir sebelum expired.')!!}
                                        </p>
                                    
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">
                                            {!! TranslationHelper::translateIfNeeded('Tanggal expired telah lewat ')!!}{{$daysRemaining}}{!! TranslationHelper::translateIfNeeded(' hari yang lalu')!!}
                                        </p>
                                    @endif
                                    

                                </div>

                                <!-- Column Modified-->
                                <div class="flex justify-end items-center mb-2 min-w-[150px] border-gray-300 pr-2">
                                    <a href="{{route('filament.admin.resources.documents.edit',['tenant' => Auth()->user()->teams()->first()->id, 'record' => $item->id])}}" class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                                       hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                       focus:ring-gray-500 dark:focus:ring-gray-300">
                                        {!! TranslationHelper::translateIfNeeded('Edit') !!}
                                    </a>
                                </div>
                        
                            
                            </div>
                        @endforeach
                    @else
                        <p class="text-center text-gray-600 dark:text-gray-300 mt-4">No Data Found</p>
                    @endif

                </div>
                {{-- end tabel --}} 

            </div>

            {{-- Content Flight Incident --}}
            <div id="content2" class="tab-content">

                {{-- tabel --}}
                <div class="mb-2">
                
                    @if($incidents->count() > 0)
                        @foreach($incidents as $item)

                            <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-800 mx-auto mb-none shadow-lg p-4">
                                
                                <!-- column Name -->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2 overflow-hidden">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold truncate">{!! TranslationHelper::translateIfNeeded('Date : ')!!}<span class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->incident_date}}</span></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{!! TranslationHelper::translateIfNeeded('cause : ')!!}{{$item->cause}}</p>
                                </div>
                        
                                <!-- Column Location-->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Location :')!!}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->location_name}}</p></p>
                                </div>

                                <!-- Column Drone-->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone : ')!!}</p>
                                    
                                    <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">
                                        {{$item->drone_name}}
                                    </p>
                                </div>

                                <!-- Column Modified-->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-l text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Pilot : ')!!}</p>
                                    
                                    <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">
                                        {{$item->user_name}}
                                    </p>
                                </div>
                                <div class="flex justify-end items-center mb-2 min-w-[150px] border-gray-300 pr-2">
                                    <a href="{{route('filament.admin.resources.incidents.edit',['tenant' => Auth()->user()->teams()->first()->id, 'record' => $item->id])}}" class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                                        hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                        focus:ring-gray-500 dark:focus:ring-gray-300">
                                         {!! TranslationHelper::translateIfNeeded('Edit') !!}
                                     </a>
                                </div>
                            </div>
                            <div class="px-2 mb-4">
                                <div class="flex items-center justify-between py-4 px-6 border-t border-gray-400 bg-gray-300 dark:bg-gray-700  shadow-lg">
                                    <div class="flex-1 min-w-[180px]">
                                        <p class="text-sm text-gray-700 dark:text-gray-400"><strong>{!! TranslationHelper::translateIfNeeded('Notes:')!!} </strong>{{$item->description}}</p>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    @else
                        <p class="text-center text-gray-600 dark:text-gray-300 mt-4">No Data Found</p>
                    @endif

                </div>
                {{-- end tabel --}} 

            </div>

            {{-- Content Flight Media --}}
            <div id="content3" class="tab-content">

                <!-- Modal  create-->
                <div class="fixed active-modal media-create inset-0 flex justify-center z-50" style="max-height: 80%">
                    <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
                        <!-- Tombol Close -->
                        <button type="button"
                            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                            onclick="closeModalMedia()">
                                &times;
                        </button>

                        <!-- Judul Modal -->
                        <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                            {!! TranslationHelper::translateIfNeeded('Media Overview')!!}
                        </h2>
                        <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">
                        {{-- error massages --}}
                        <div id="bodyErrorMassagesMCreate" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <!-- Icon Error -->
                                    <svg class="h-5 w-5 text-red-400 dark:text-red-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0m1.414-1.414a9 9 0 0110.899 0m-5.7 5.8a2.25 2.25 0 10-3.18-3.181m0 0a2.25 2.25 0 013.18 3.181m-3.18-3.181L12 12m0 0l3.18-3.18" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm">
                                    <p class="font-medium text-red-800 dark:text-red-200">
                                        {!! TranslationHelper::translateIfNeeded('Error: ') !!}
                                        <span id="errorMassagesMCreate"></span>
                                    </p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button type="button" onclick="closeMessages()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
                                        data-bs-dismiss="alert" aria-label="Close">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Form -->
                        <form id="documentForm" enctype="multipart/form-data">
                            @csrf
                            <input id="ownerMedia" type="hidden" name="teams_id" value="{{ auth()->user()->first()->id }}">
                            <input id="mediaFlightID" type="hidden" name="mediaFlightID" value="{{ $id }}">
                        
                            <!-- Name Input -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Media Title') !!}</label>
                                <input id="nameMedia" type="text" name="nameMedia" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                                <!-- Url-->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Media URL') !!}</label>
                                    <input id="urlMedia" type="text" name="urlMedia" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                        
                                <!-- Type -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Type Url') !!}</label>
                                    <select id="mediaType" type="text" name="mediaType" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                        <option disabled selected value="">Select an Type Url</option>
                                        <option value="picture">Picture URL</option>
                                        <option value="video">Video URL</option>
                                        <option value="other">Other URL</option>
                                    </select>
                                </div>
    
                            </div>
                        
                            <!-- Notes -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Notes') !!}</label>
                                <textarea id="descriptionMedia" name="descriptionMedia" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                            </div>
                        
                            <!-- Submit Button -->
                            <div class="flex justify-end mt-4">
                                <button id="triggerButtonMedia" type="button" class="button" style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">
                                    <span class="button__text">{!! TranslationHelper::translateIfNeeded('Submit') !!}</span>
                                </button>
                            </div>
                        </form>
                        
                    </div>
                </div>

                <!-- Modal  Edit-->
            @if($media != null)
                <div class="fixed active-modal media-edit inset-0 flex justify-center z-50" style="max-height: 80%">
                    <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
                        <!-- Tombol Close -->
                        <button type="button"
                            class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                            onclick="closeModalMediaEdit()">
                                &times;
                        </button>

                        <!-- Judul Modal -->
                        <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                            {!! TranslationHelper::translateIfNeeded('Edit Media Overview')!!}
                        </h2>
                        <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">
                        {{-- error massages --}}
                        <div id="bodyErrorMassagesMEdit" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <!-- Icon Error -->
                                    <svg class="h-5 w-5 text-red-400 dark:text-red-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0m1.414-1.414a9 9 0 0110.899 0m-5.7 5.8a2.25 2.25 0 10-3.18-3.181m0 0a2.25 2.25 0 013.18 3.181m-3.18-3.181L12 12m0 0l3.18-3.18" />
                                    </svg>
                                </div>
                                <div class="ml-3 text-sm">
                                    <p class="font-medium text-red-800 dark:text-red-200">
                                        {!! TranslationHelper::translateIfNeeded('Error: ') !!}
                                        <span id="errorMassagesMEdit"></span>
                                    </p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <button type="button" onclick="closeMessages()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
                                        data-bs-dismiss="alert" aria-label="Close">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Form -->
                        <form id="documentFormEdit" enctype="multipart/form-data">
                            @csrf
                            <input id="idMediaEdit" type="hidden" name="IdMediaEdit" value="{{$media->id}}">
                            <input id="ownerMediaEdit" type="hidden" name="teams_id" value="{{$media->owner_id}}">
                            <input id="mediaFlightIDEdit" type="hidden" name="mediaFlightID" value="{{ $media->fligh_id}}">
                        
                            <!-- Name Input -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Media Title') !!}</label>
                                <input id="nameMediaEdit" type="text" name="nameMedia" maxlength="255" 
                                value="{{$media->title}}"
                                class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                                <!-- Url-->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Media URL') !!}</label>
                                    <input id="urlMediaEdit" type="text" name="urlMedia" 
                                    value="{{$media->url}}"
                                    class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>
                        
                                <!-- Type -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Type Url') !!}</label>
                                    <select id="mediaTypeEdit" type="text" name="mediaType" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                        @if($media->type != null)
                                            <option value="{{$media->type}}" selected >-- {{$media->type}} Url --</option>
                                        @endif
                                        <option disabled value="">Select an Type Url</option>
                                        <option value="picture">Picture URL</option>
                                        <option value="video">Video URL</option>
                                        <option value="other">Other URL</option>
                                    </select>
                                </div>
    
                            </div>
                        
                            <!-- Notes -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Notes') !!}</label>
                                <textarea id="descriptionMediaEdit" name="descriptionMedia" maxlength="255"
                                class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">{{ $media->description ?? '' }}</textarea>
                            </div>
                        
                            <!-- Submit Button -->
                            <div class="flex justify-end mt-4">
                                <button id="triggerButtonMediaEdit" type="button" class="button" style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">
                                    <span class="button__text">{!! TranslationHelper::translateIfNeeded('Edit') !!}</span>
                                </button>
                            </div>
                        </form>
                        
                    </div>
                </div>
            @endif


             {{-- tabel --}}
             <div class="mb-2">
                    
                <div class="mt-4 flex justify-end mb-4">
                    <button type="button" onclick="openModalMedia()" class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                focus:ring-gray-500 dark:focus:ring-gray-300">{!! TranslationHelper::translateIfNeeded('Add Media')!!}</button>
                </div>
            
                @if($flighmedia->count() > 0)
                    @foreach($flighmedia as $item)

                        <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-800 mx-auto mb-4 shadow-lg p-4">
                            
                            <!-- column Name -->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2 overflow-hidden">
                                <p class="text-l text-gray-800 dark:text-gray-200 font-semibold truncate">{!! TranslationHelper::translateIfNeeded('Title : ')!!}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->title}}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->type}}</p>
                            </div>
                    
                            <!-- Column Number Ref-->
                            <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                <p class="text-l text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Notes :')!!}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-150 font-semibold truncate">{{$item->description}}</p></p>
                            </div>

                            <!-- Column Modified-->
                            <div class="flex justify-end items-center mb-2 min-w-[150px] border-gray-300 pr-2">   
                                <button 
                                    id="ViewButton-{{ $item->id }}" 
                                    type="button" 
                                    class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                                        hover:bg-gray-600 dark:hover:bg--400 focus:outline-none focus:ring-2 
                                        focus:ring-gray-500 dark:focus:ring-gray-300"
                                    onclick="window.open('{{$item->url}}', '_blank')"
                                >
                                    {!! TranslationHelper::translateIfNeeded('View') !!}
                                </button>
                            </div>
                            <div class="flex justify-end items-center mb-2 min-w-[150px] border-gray-300 pr-2">

                                <button id="valueButton-{{ $item->id }}" type="button" class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                                hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                focus:ring-gray-500 dark:focus:ring-gray-300" value="{{$item->id}}" onclick="openModalMediaEdit({{ $item->id }})">
                                {!! TranslationHelper::translateIfNeeded('Edit') !!}
                                </button>
                            </div>
                    
                        
                        </div>
                    @endforeach
                @else
                    <p class="text-center text-gray-600 dark:text-gray-300 mt-4">No Data Found</p>
                @endif

            </div>
            {{-- end tabel --}} 

            </div>

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function closeModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.add('active-modal');
    }
    function openModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.remove('active-modal');     
    }

    //flightDocument
    function closeModalFlight() {
        const contents = document.querySelector('.flight-create');
        contents.classList.add('active-modal');
    }
    function openModalFlight() {
        const contents = document.querySelector('.flight-create');
        contents.classList.remove('active-modal');     
    }

    //media create
    function closeModalMedia() {
        const contents = document.querySelector('.media-create');
        contents.classList.add('active-modal');
    }
    function openModalMedia() {
        const contents = document.querySelector('.media-create');
        contents.classList.remove('active-modal');     
    }

        //media Edit
    function closeModalMediaEdit() {
        const contents = document.querySelector('.media-edit');

        contents.classList.add('active-modal');
    }
    function openModalMediaEdit(itemId) {
        const contents = document.querySelector('.media-edit');
        
        
        const buttonValue = itemId;
        console.log(buttonValue);
            $.ajax({
            url: '{{ route("edit.media.flight") }}',
            type: 'POST',
            data: {
                button_value: buttonValue,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Data diterima: ', response);
                localStorage.setItem('modalStatus', 'close');
                location.reload(); 
            },
            error: function(xhr, status, error) {
                console.error('Error: ', error);
            }
        });

    }
    //untuk edit media ketika di klik edit
    document.addEventListener('DOMContentLoaded', function () {
    const modalStatus = localStorage.getItem('modalStatus');
    console.log(modalStatus);

        if (modalStatus === 'close') {
            const contents = document.querySelector('.media-edit');
            document.getElementById('tab3')?.classList.add('active');
            document.getElementById('content3')?.classList.add('active');
            document.getElementById('content0')?.classList.remove('active');
            document.getElementById('tab0')?.classList.remove('active');

            if (contents) {
                contents.classList.remove('active-modal');
            }

            // Hapus status setelah diterapkan
            localStorage.removeItem('modalStatus');
        }
    });
//end media

    //messages close document flight
    function closeMessagesDflight() {
        document.getElementById('bodyErrorMassagesDflight').style.display = 'none';
    }
    //messages close document Project
    function closeMessagesDproject() {
        document.getElementById('bodyErrorMassagesDproject').style.display = 'none';
    }
    //messages close Media create
        function closeMessagesMCreate() {
        document.getElementById('bodyErrorMassagesMCreate').style.display = 'none';
    }
    //messages close Media edit
        function closeMessagesMEdite() {
        document.getElementById('bodyErrorMassagesMEdit').style.display = 'none';
    }
</script>

<script>

    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {

            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        
            button.classList.add('active');
            const contentId = `content${button.id.charAt(button.id.length - 1)}`;
            document.getElementById(contentId).classList.add('active');
        });
    });
</script>

<script>

    //media flight Edit
    $(document).ready(function() {
        $('#triggerButtonMediaEdit').click(function() {
            const formDataMediaEdit = new FormData();
            formDataMediaEdit.append('_token', '{{ csrf_token() }}'); // CSRF token
            formDataMediaEdit.append('id', $('#idMediaEdit').val());
            formDataMediaEdit.append('name', $('#nameMediaEdit').val());
            formDataMediaEdit.append('url', $('#urlMediaEdit').val());
            formDataMediaEdit.append('type', $('#mediaTypeEdit').val());
            formDataMediaEdit.append('notes', $('#descriptionMediaEdit').val());
            formDataMediaEdit.append('owner', $('#ownerMediaEdit').val());
            formDataMediaEdit.append('flight', $('#mediaFlightIDEdit').val());

            let name = $('#nameMediaEdit').val().trim() || null;
            let url = $('#urlMediaEdit').val().trim() || null;
            let type = $('#mediaTypeEdit').val();

            if(name == null){
                document.getElementById('bodyErrorMassagesMEdit').style.display = 'block';
                document.getElementById('errorMassagesMEdit').textContent = 'Name cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesMEdit').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesMEdit').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(url == null){
                document.getElementById('bodyErrorMassagesMEdit').style.display = 'block';
                document.getElementById('errorMassagesMEdit').textContent = 'Url cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesMEdit').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesMEdit').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(type == null){
                document.getElementById('bodyErrorMassagesMEdit').style.display = 'block';
                document.getElementById('errorMassagesMEdit').textContent = 'Type cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesMEdit').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesMEdit').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            else{

                $.ajax({
                    url: '{{ route('create.media.flight.record') }}',
                    type: 'POST',
                    data: formDataMediaEdit,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        });
    });

    //media flight Create
    $(document).ready(function() {
        $('#triggerButtonMedia').click(function() {
            const formDataMedia = new FormData();
            formDataMedia.append('_token', '{{ csrf_token() }}'); // CSRF token
            formDataMedia.append('name', $('#nameMedia').val());
            formDataMedia.append('url', $('#urlMedia').val());
            formDataMedia.append('type', $('#mediaType').val());
            formDataMedia.append('notes', $('#descriptionMedia').val());
            formDataMedia.append('owner', $('#ownerMedia').val());
            formDataMedia.append('flight', $('#mediaFlightID').val());

            let name = $('#nameMedia').val().trim() || null;
            let url = $('#urlMedia').val().trim() || null;
            let type = $('#mediaType').val();
            console.log(type);
            if(name == null){
                document.getElementById('bodyErrorMassagesMCreate').style.display = 'block';
                document.getElementById('errorMassagesMCreate').textContent = 'Name cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesMCreate').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesMCreate').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(url == null){
                document.getElementById('bodyErrorMassagesMCreate').style.display = 'block';
                document.getElementById('errorMassagesMCreate').textContent = 'Url cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesMCreate').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesMCreate').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(type == null){
                document.getElementById('bodyErrorMassagesMCreate').style.display = 'block';
                document.getElementById('errorMassagesMCreate').textContent = 'Type cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesMCreate').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesMCreate').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            else{

                $.ajax({
                    url: '{{ route('create.media.flight') }}',
                    type: 'POST',
                    data: formDataMedia,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        });
    });

    //flight document
    $(document).ready(function() {
        $('#triggerButtonFlight').click(function() {
            const formDataFlight = new FormData();
            formDataFlight.append('_token', '{{ csrf_token() }}'); // CSRF token
            formDataFlight.append('name', $('#nameFlight').val());
            formDataFlight.append('expired', $('#expiredDateFlight').val());
            formDataFlight.append('refNumber', $('#refnumberFlight').val());
            formDataFlight.append('link', $('#externalLinkFlight').val());
            formDataFlight.append('notes', $('#descriptionFlight').val());
            formDataFlight.append('dock', $('#dockFlight')[0].files[0]); // File input
            formDataFlight.append('owner', $('#ownerFlight').val());
            formDataFlight.append('flight', $('#flight').val());

            let name = $('#nameFlight').val().trim() || null;
            let expired = $('#expiredDateFlight').val().trim() || null;
            let refNumber = $('#refnumberFlight').val().trim() || null;
            let dock = $('#dockFlight').val().trim() || null;
            let link = $('#externalLinkFlight').val().trim() || null;

            if(name == null){
                document.getElementById('bodyErrorMassagesDflight').style.display = 'block';
                document.getElementById('errorMassagesDflight').textContent = 'Name cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesDflight').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesDflight').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(refNumber == null){
                document.getElementById('bodyErrorMassagesDflight').style.display = 'block';
                document.getElementById('errorMassagesDflight').textContent = 'Ref number  cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesDflight').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesDflight').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(expired == null){
                document.getElementById('bodyErrorMassagesDflight').style.display = 'block';
                document.getElementById('errorMassagesDflight').textContent = 'Expired date cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesDflight').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesDflight').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(dock == null && link == null){
                document.getElementById('bodyErrorMassagesDflight').style.display = 'block';
                document.getElementById('errorMassagesDflight').textContent = 'Please enter a link or your document';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesDflight').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesDflight').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            else{

                $.ajax({
                    url: '{{ route('create.document.flight') }}',
                    type: 'POST',
                    data: formDataFlight,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        });
    });

    //Project document
    $(document).ready(function() {
        $('#triggerButton').click(function() {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}'); // CSRF token
            formData.append('name', $('#name').val());
            formData.append('expired', $('#expiredDate').val());
            formData.append('refNumber', $('#refnumber').val());
            formData.append('link', $('#externalLink').val());
            formData.append('notes', $('#description').val());
            formData.append('dock', $('#dock')[0].files[0]); // File input
            formData.append('owner', $('#owner').val());
            formData.append('project', $('#project').val());

            let name = $('#name').val().trim() || null;
            let expired = $('#expiredDate').val().trim() || null;
            let refNumber = $('#refnumber').val().trim() || null;
            let dock = $('#dock').val().trim() || null;
            let link = $('#externalLink').val().trim() || null;

            if(name == null){
                document.getElementById('bodyErrorMassagesDproject').style.display = 'block';
                document.getElementById('errorMassagesDproject').textContent = 'Name cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesDproject').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesDproject').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(refNumber == null){
                document.getElementById('bodyErrorMassagesDproject').style.display = 'block';
                document.getElementById('errorMassagesDproject').textContent = 'Ref number  cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesDproject').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesDproject').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(expired == null){
                document.getElementById('bodyErrorMassagesDproject').style.display = 'block';
                document.getElementById('errorMassagesDproject').textContent = 'Expired date cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesDproject').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesDproject').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(dock == null && link == null){
                document.getElementById('bodyErrorMassagesDproject').style.display = 'block';
                document.getElementById('errorMassagesDproject').textContent = 'Please enter a link or your document';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesDproject').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesDproject').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
            else{

                $.ajax({
                    url: '{{ route('create.document.project') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        });
    });


</script>
