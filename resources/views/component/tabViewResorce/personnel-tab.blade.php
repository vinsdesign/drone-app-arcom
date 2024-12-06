<?php
    use App\Helpers\TranslationHelper;
    //project Document
    $id = $getRecord()->id;

    if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
        $queryUsers = App\Models\Document::query()->where('users_id', $id)->where('status_visible', '!=', 'archived')->get();
    }else{
        $queryUsers = App\Models\Document::query()
        ->where('users_id', $id)
        ->where('status_visible', '!=', 'archived')
        ->where(function ($query) {
            $query->where('shared', 1)
                ->orWhere('users_id', auth()->id());
        })->get();
    }

    $documentProjects = $queryUsers;
    //FlightIncident
    $Incident = App\Models\Incident::Where('personel_involved_id',$id)->get();

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
        
            <div class="flex flex-wrap gap-2">
                <button id="tab0" class="tab-button active text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto">
                    {!! TranslationHelper::translateIfNeeded('Users Document') !!}
                </button>
                <button id="tab1" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto">
                    {!! TranslationHelper::translateIfNeeded('Incident') !!}
                </button>
            </div>
        </div>
        

        <!-- Tab content -->
        <div class="content">

            {{-- content Documment User --}}
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
                            {!! TranslationHelper::translateIfNeeded('Add Document')!!}
                        </h2>
                        <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

                        {{-- error massages --}}
                        <div id="bodyErrorMassages" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
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
                                        <span id="errorMassages"></span>
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
                            <input id="relation" type="hidden" name="recordID" value="{{ $id }}">
                        
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

                                <!-- Type -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Ref / Certificate #') !!}</label>
                                    <select id="type" type="text" name="type" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                        <option value="" disabled selected >{!! TranslationHelper::translateIfNeeded('Select an type') !!}</option>
                                        <option value="Pilot License">Pilot License</option>
                                        <option value="UAV Training Course">UAV Training Course</option>
                                        <option value="Remote Pilot Certificate">Remote Pilot Certificate</option>
                                        <option value="Currency Certificate">Currency Certificate</option>
                                        <option value="Medical Certificate">Medical Certificate</option>
                                        <option value="Insurance Certificate">Insurance Certificate</option>
                                        <option value="Registration #">Registration #</option>
                                        <option value="Regulatory Certificate">Regulatory Certificate</option>
                                        <option value="Checklist">Checklist</option>
                                        <option value="Manual">Manual</option>
                                        <option value="Other Certificate">Other Certificate</option>
                                        <option value="Site Assessment">Site Assessment</option>
                                        <option value="Safety Instruction">Safety Instruction</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <!-- External Link -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('External Link') !!}</label>
                                <input id="externalLink" type="text" name="externalLink" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
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
                        <button type="button" onclick="openModal()" class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                    hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                    focus:ring-gray-500 dark:focus:ring-gray-300">{!! TranslationHelper::translateIfNeeded('Upload Document')!!}</button>
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

            {{-- Content Incident --}}
            <div id="content1" class="tab-content">

                {{-- tabel --}}
                <div class="mb-2">
                
                    @if($Incident->count() > 0)
                        @foreach($Incident as $item)

                            <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-4 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto shadow-lg">
                                        
                                <!-- Kolom Cause dan Status -->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Cause:')!!}</p>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <a href="{{route('filament.admin.resources.incidents.edit',['tenant'=>Auth()->user()->teams()->first()->id,'record'=>$item->id])}}">
                                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->cause }}</p>
                                            </a>
                                            <p class="text-xs text-gray-500 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Date:')!!} {{ $item->incident_date }}</p>
                                        </div>
                                        <span class="px-2 py-1 rounded text-white text-xs {{ $item->status == 0 ? 'bg-green-500' : 'bg-red-500' }}">
                                            @if($item->status == 0)
                                                Closed
                                            @else
                                                Under Review
                                            @endif
                                        </span>
                                    </div>
                                </div>
                        
                                <!-- Kolom Drone Name -->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Drone:')!!}</p>
                                    <a href="{{route('drone.statistik', ['drone_id' => $item->drone->id ?? 0])}}">
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->drone->name ??  TranslationHelper::translateIfNeeded('No Drone') }}</p>
                                    </a>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->drone->brand ??  TranslationHelper::translateIfNeeded('No Drone') }} / {{$item->drone->model}}</p>
                                </div>
                        
                                <!-- Kolom Personnel Involved -->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Personnel Involved:')!!}</p>
                                    <a href="{{route('filament.admin.resources.users.view',['tenant'=>Auth()->user()->teams()->first()->id, 'record'=> $item->users->id ?? 0])}}">
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->users->name ??  TranslationHelper::translateIfNeeded('No Personnel') }}</p>
                                    </a>
                                </div>
                        
                                <!-- Kolom Location -->
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Location:')!!}</p>
                                    <a href="{{route('filament.admin.resources.fligh-locations.edit',['tenant' =>Auth()->user()->teams()->first()->id,'record'=>$item->fligh_locations->id ?? 0])}}">
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->fligh_locations->name ??  TranslationHelper::translateIfNeeded('No Location') }}</p>
                                    </a>
                                </div>
                        
                                <!-- Kolom Project -->
                                <div class="flex-1 min-w-[150px] mb-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Project:')!!}</p>
                                    <a href="{{route('filament.admin.resources.projects.view',['tenant'=>Auth()->user()->teams()->first()->id,'record'=>$item->project->id ?? 0])}}">
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->project->case ??  TranslationHelper::translateIfNeeded('No Project') }}</p>
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

        </div>
    </div>
</div>

<script>
    function closeModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.add('active-modal');
    }
    function openModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.remove('active-modal');     
    }
        //messages close
    function closeMessages() {
        document.getElementById('bodyErrorMassages').style.display = 'none';
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

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
        formData.append('relation', $('#relation').val());
        formData.append('type', $('#type').val());

        let name = $('#name').val().trim() || null;
        let expired = $('#expiredDate').val().trim() || null;
        let refNumber = $('#refnumber').val().trim() || null;
        let dock = $('#dock').val().trim() || null;
        let link = $('#externalLink').val().trim() || null;

        if(name == null){
            document.getElementById('bodyErrorMassages').style.display = 'block';
            document.getElementById('errorMassages').textContent = 'Name cannot be null';
            setTimeout(() => {
                document.getElementById('bodyErrorMassages').style.display = 'none';
            }, 5000);
            document.getElementById('bodyErrorMassages').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
        }else if(refNumber == null){
            document.getElementById('bodyErrorMassages').style.display = 'block';
            document.getElementById('errorMassages').textContent = 'Ref number  cannot be null';
            setTimeout(() => {
                document.getElementById('bodyErrorMassages').style.display = 'none';
            }, 5000);
            document.getElementById('bodyErrorMassages').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
        }else if(expired == null){
            document.getElementById('bodyErrorMassages').style.display = 'block';
            document.getElementById('errorMassages').textContent = 'Expired date cannot be null';
            setTimeout(() => {
                document.getElementById('bodyErrorMassages').style.display = 'none';
            }, 5000);
            document.getElementById('bodyErrorMassages').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
        }else if(dock == null && link == null){
            document.getElementById('bodyErrorMassages').style.display = 'block';
            document.getElementById('errorMassages').textContent = 'Please enter a link or your document';
            setTimeout(() => {
                document.getElementById('bodyErrorMassages').style.display = 'none';
            }, 5000);
            document.getElementById('bodyErrorMassages').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
        }else{
            $.ajax({
            url: '{{ route('create.document.personnel') }}',
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
