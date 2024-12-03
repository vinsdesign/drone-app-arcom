<?php
    use App\Helpers\TranslationHelper;
    //project Document
    $id = $getRecord()->id;
    $teams = Auth()->user()->teams()->first()->id;
    $documentProjects = App\Models\Document::Where('battrei_id', $id)->get();
    //FlightIncident
    $maintenance = App\Models\maintence_eq::Where('battrei_id',$id)->get();
    //flight
    $flights = App\Models\Fligh::where('teams_id', $teams)
    ->whereHas('battreis', function ($query) use ($id) {
        $query->where('battrei_id', $id);
    })
    ->get();

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Import at vite('resources/css/app.css') untuk tailwind perlu NPM-->
    {{-- @vite('resources/css/app.css') --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .main-content.active{
            display: block;
        }
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
                    {!! TranslationHelper::translateIfNeeded('Flight') !!} ({{$flights->count()}})
                </button>
                <button id="tab1" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto">
                    {!! TranslationHelper::translateIfNeeded('Batterei Document') !!}
                </button>
                <button id="tab2" class="tab-button text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded text-sm sm:text-base w-full sm:w-auto">
                    {!! TranslationHelper::translateIfNeeded('Maintenence') !!}
                </button>
            </div>
        </div>
        

        <!-- Tab content -->
        <div class="content">
            {{--Flight--}}

            <div id="content0" class="tab-content active">
                @if($flights->count() > 0)
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
                                            {{$item->duration ?? null}}
                                        </p>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">
                                            {{$item->start_date_flight ?? null}}
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
                                
                                <div class="flex-1 min-w-[180px] mt-4 justify-end">
                                    <button
                                        class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg 
                                                hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                                focus:ring-gray-500 dark:focus:ring-gray-300"
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
                @else
                    <p class="text-center text-gray-600 dark:text-gray-300 mt-4">No Data Found</p>
                @endif


            </div>

            {{-- content Documment Project --}}
            <div id="content1" class="tab-content">
    
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
                            {!! TranslationHelper::translateIfNeeded('Add Equipment Document')!!}
                        </h2>
                        <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

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

            {{-- Content Maintenence --}}
            <div id="content2" class="tab-content">

                {{-- tabel --}}
                <div class="mb-2">
                
                    @if($maintenance->count() > 0)
                        @foreach($maintenance as $item)

                            <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 max-w-[800px] mx-auto shadow-lg">
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Maintenance Name') !!}</p>
                                    <a href="{{route('filament.admin.resources.maintenance-batteries.edit',
                                    ['tenant' => Auth()->user()->teams()->first()->id,
                                    'record' =>$item->id])}}">
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->name??null}}</p>
                                    </a>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->date??null}}</p>
                                </div>
                            
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Next Scheduled:') !!} <span class="text-sm text-gray-700 dark:text-gray-400">{{$item->date?? null}}</span></p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">
                                        @php
                                            $now = Carbon\Carbon::now();
                                            $formatDate = \Carbon\Carbon::parse($item->date)->format('Y-m-d');
                                            $daysOverdueDiff = $now->diffInDays($item->date, false);
                                        @endphp
                                            @if($daysOverdueDiff < 0) 
                                            @php
                                                $daysOverdueDiff = abs(intval($daysOverdueDiff));
                                            @endphp
                                                <span style="
                                                    display: inline-block;
                                                    background-color: red; 
                                                    color: white; 
                                                    padding: 3px 6px;
                                                    border-radius: 5px;
                                                    font-weight: bold;
                                                ">
                                                    {!! TranslationHelper::translateIfNeeded('Overdue:')!!} {{ $daysOverdueDiff }} {!! TranslationHelper::translateIfNeeded('days')!!}
                                                </span>
                                            </span>
                                        @else
                                            <span class="bg-green-600 text-white py-1 px-2 rounded">{{ $formatDate }}</span>
                                        @endif
                                    </p>    
                                </div>
                            

                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Battery / Equipment') !!}</p>
                                        @if($item->equidment_id == null)
                                            <a href="{{route('battery.statistik', ['battery_id' => $item->battrei_id])}}">
                                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->battrei->name ?? null }}</p>
                                            </a>
                                        @else
                                            <a href="{{route('equipment.statistik', ['equipment_id' => $item->equidment_id])}}">
                                                <p class="text-sm text-gray-700 dark:text-gray-400">{{ $item->equidment->name ?? null }}</p>
                                            </a>
                                        @endif
                                    <p class="text-sm text-gray-700 dark:text-gray-400">
                                        @if($item->equidment == false)
                                            {!! TranslationHelper::translateIfNeeded('Battery') !!}
                                        @else
                                            {{ $item->equidment->type?? null }}
                                        @endif
                                    </p>
                                </div>
                            
                                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Technician')!!}</p>
                                    <p class="text-sm text-gray-700 dark:text-gray-400">{{$item->technician??null}}</p>
                                </div>
                            </div>
                            <div class="px-2 mb-4">
                                <div class="flex items-center justify-between py-4 px-6 border-t border-gray-400 bg-gray-300 dark:bg-gray-700  shadow-lg">
                                    <div class="flex-1 min-w-[180px]">
                                        <p class="text-sm text-gray-700 dark:text-gray-400"><strong>{!! TranslationHelper::translateIfNeeded('Notes:')!!} </strong>{{$item->notes}}</p>
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

        $.ajax({
            url: '{{ route('create.battrei.equipment') }}',
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
    });
});


</script>
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