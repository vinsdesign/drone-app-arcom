<?php
    use App\Helpers\TranslationHelper;
    $teams = Auth()->user()->teams()->first()->id;
    $customer = App\Models\customer::where('teams_id',$teams)->get();
    $project = App\Models\projects::where('teams_id',$teams)->get();
    $default = App\Models\team::where('id',$teams)->get();
    $defaultCountCustomer = App\Models\team::where('id',$teams)->count('id_customers');
    $defaultCountProject = App\Models\team::where('id',$teams)->count('id_projects');
    $defaultCountType = App\Models\team::where('id',$teams)->count('flight_type');
    $team = App\Models\Team::where('id', $teams)->first();
    $defaultPilot = $team ? $team->set_pilot : false;
    $isChecked = $defaultPilot;
?>
<x-filament-panels::page>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
            
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <!-- Include CSS for Select2 -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
            <!-- Include jQuery (required for Select2) -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

            <!-- Include Select2 JS -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
            <!--tailwind-->
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">


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
        .tab-button.active {
            background-color: #2563eb;
        }

            /* Container for tabs and content */
            .tab-container {
                display: grid;
                grid-template-columns: 1fr 3fr; /* Left column 1/4, right column 3/4 */
                gap: 20px;
                padding: 20px;
                border-radius: 8px;
            }
            /* Styling for the tab buttons container */
            .tab-buttons {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
    
            /* Tab buttons styling */
            .tab-button {
                background-color: #3b82f6;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                border: none;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
    
            .tab-button.active {
                background-color: #2563eb;
            }
    
            /* Tab content styling */
            .tab-content {
                display: none;
                padding: 20px;
                border-radius: 8px;
                animation: fadeIn 0.5s ease;
            }
            .tab-content.active {
                display: block;
            }
    
            /* Responsive design */
            @media (max-width: 768px) {
                .tab-container {
                    grid-template-columns: 1fr; /* Stack on small screens */
                }
            }
    
            /* Animation */
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
    </style>
    </head>
    {{-- Main Menu --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Menu 1 -->
        <div class="main-button bg-white dark:bg-gray-800 shadow dark:shadow-lg rounded-lg p-4 text-center hover:shadow-lg dark:hover:shadow-xl transition-shadow" onclick="showContent(0)">
            <div class="text-blue-500 dark:text-blue-400 mb-2">
                <x-heroicon-o-globe-alt class="w-8 h-8 mx-auto" />
            </div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('General Settings') !!}</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{!! TranslationHelper::translateIfNeeded('Set your website here') !!}</p>
        </div>
    
        <!-- Menu 2 -->
        @if (Auth::user()->roles()->pluck('name')->contains('super_admin') || (Auth::user()->roles()->pluck('name')->contains('panel_user')))
        <div class="main-button bg-white dark:bg-gray-800 shadow dark:shadow-lg rounded-lg p-4 text-center hover:shadow-lg dark:hover:shadow-xl transition-shadow cursor-pointer" onclick="showContent(1)">
            <div class="mb-2 text-gray-800 dark:text-gray-200">
                <x-heroicon-s-currency-dollar class="w-8 h-8 mx-auto" />
            </div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('Currency') !!}</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{!! TranslationHelper::translateIfNeeded('Manage your currency here') !!}</p>
        </div>
        @endif
        <!-- Menu 3 -->
        <div class="main-button bg-white dark:bg-gray-800 shadow dark:shadow-lg rounded-lg p-4 text-center hover:shadow-lg dark:hover:shadow-xl transition-shadow" onclick="showContent(2)">
            <div class="text-yellow-500 dark:text-yellow-400 mb-2">
                <x-heroicon-c-language class="w-8 h-8 mx-auto" />
            </div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('Language') !!}</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{!! TranslationHelper::translateIfNeeded('Manage your Language') !!}</p>
        </div>
    </div>
    
    {{-- End Main Menu --}}
<div class="main-contents">
    {{-- General Setting--}}
    <div id="maincontent0" class="container tab-border-warna main-content">
        <!-- Main tab container -->
        <div class="tab-container grid grid-cols-1 md:grid-cols-4 gap-5 p-5 rounded-lg bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700">
            <!-- Tab buttons on the left -->
            <div class="tab-buttons flex flex-col border-r border-gray-300 dark:border-gray-700 p-4 space-y-2">

                <h1 class="text-lg font-bold border-b border-gray-500 pb-2">{!! TranslationHelper::translateIfNeeded('Setting') !!}</h1>
                <button id="tab0" data-index="0" class="tab-button active border border-gray-300 dark:border-gray-700 rounded-lg py-2 px-4 text-gray-800 dark:text-gray-200" onclick="showContentTab(0)">{!! TranslationHelper::translateIfNeeded('Profile') !!}</button>
                @if (Auth::user()->roles()->pluck('name')->contains('super_admin') || (Auth::user()->roles()->pluck('name')->contains('panel_user')))
                    <button id="tab1" data-index="1" class="tab-button border border-gray-300 dark:border-gray-700 rounded-lg py-2 px-4 text-gray-800 dark:text-gray-200" onclick="showContentTab(1)">{!! TranslationHelper::translateIfNeeded('Organization') !!}</button>
                @endif
                <button id="tab2" data-index="2" class="tab-button border border-gray-300 dark:border-gray-700 rounded-lg py-2 px-4 text-gray-800 dark:text-gray-200" onclick="showContentTab(2)">{!! TranslationHelper::translateIfNeeded('Import Rules') !!}</button>
                <button id="tab3" data-index="3" class="tab-button border border-gray-300 dark:border-gray-700 rounded-lg py-2 px-4 text-gray-800 dark:text-gray-200" onclick="showContentTab(3)">{!! TranslationHelper::translateIfNeeded('API') !!}</button>
                <button id="tab4" data-index="4" class="tab-button border border-gray-300 dark:border-gray-700 rounded-lg py-2 px-4 text-gray-800 dark:text-gray-200" onclick="showContentTab(4)">{!! TranslationHelper::translateIfNeeded('Billing') !!}</button>
            </div>
    
            <!-- Tab content on the right -->
            <div class="tab-contents">
                {{-- menu profile --}}
                <div id="content0" class="tab-content active">
                    @livewire('personal-info')
                    @livewire('my-custom-component')
                    @livewire('update-password')
                </div>
                {{-- menu Team --}}
                
                <div id="content1" class="tab-content">
                    @if (Auth::user()->roles()->pluck('name')->contains('super_admin') || (Auth::user()->roles()->pluck('name')->contains('panel_user')))
                        @livewire(\app\filament\Pages\Tenancy\EditTeamProfil::class)
                    @endif
                </div>
                
                {{-- menu Import Rules --}}
                <div id="content2" class="tab-content p-6 bg-gray-50 dark:bg-gray-800 rounded-lg shadow-md">
                    <h1 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('Import Rules') !!}</h1>
                    <p class="mb-3 text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('These rules will be automatically set for all logs on new imported flights.') !!}<br>{!! TranslationHelper::translateIfNeeded('Rules will be applied in these flight log importers:') !!}</p>
                    
                    <ul class="list-disc pl-5 mb-6">
                        <li><a href="{{route('filament.admin.resources.manual-imports.index',[Auth()->user()->teams()->first()->id])}}" class="text-blue-600 dark:text-blue-400 hover:underline">{!! TranslationHelper::translateIfNeeded('Manual Multiple Importer') !!}</a></li>
                        <li ><a href="{{route('filament.admin.resources.importers.index',[Auth()->user()->teams()->first()->id])}}" class="text-blue-600 dark:text-blue-400 hover:underline">{!! TranslationHelper::translateIfNeeded('Dji Cloud Importer') !!}</a></li>
                    </ul>
                    <h1 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('Default Value') !!}</h1>
                    <form class="space-y-4" action="{{route('default-value')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="customer" class="font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Default Customer') !!}</label>
                            <select name="customer" id="customer" class="border border-gray-300 dark:border-gray-600 rounded-lg w-full p-2 mt-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400">
                                @if($defaultCountCustomer > 0)
                                    @foreach($default as $key)
                                    <option value="{{$key->id_customers}}">-- {{$key->getNameCustomer->name}} --</option>
                                    @endforeach  
                                @endif
                                <option value="">-- None --</option>
                                @foreach($customer as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                
                        <div class="form-group">
                            <label for="projects" class="font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Default Project/Job') !!}</label>
                            <select name="projects" id="projects" class="border border-gray-300 dark:border-gray-600 rounded-lg w-full p-2 mt-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400">
                                @if($defaultCountProject > 0)
                                    @foreach($default as $key)
                                    <option value="{{$key->id_projects}}">-- {{$key->getNameProject->case}} --</option>
                                    @endforeach  
                                @endif
                                <option value="">-- None --</option>
                                @foreach($project as $item)
                                <option value="{{$item->id}}">{{$item->case}}</option>
                                @endforeach
                            </select>
                        </div>
                
                        <div class="form-group">
                            <label for="Sflight_type" class="font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Default Flight Type') !!}</label>
                            <select name="flight_type" id="flight_type" class="border border-gray-300 dark:border-gray-600 rounded-lg w-full p-2 mt-1 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400">
                                @if($defaultCountType > 0)
                                    @foreach($default as $key)
                                    <option value="{{$key->flight_type}}">-- {{$key->flight_type}} --</option>
                                    @endforeach  
                                @endif
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
                
                        <div class="form-group flex items-center space-x-2">
                            <input name="pilot" type="checkbox"  id="pilot" class="form-checkbox h-5 w-5 text-blue-600 dark:text-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400"{{ $isChecked ? 'checked' : '' }}>
                            <label for="pilot" class="font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('If Pilot not set upfront, set Auto-detected Drone Owner As Pilot') !!}</label>
                        </div>  
                        <button type="submit" class="bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-all duration-200 ease-in-out">
                            {!! TranslationHelper::translateIfNeeded('Submit') !!}
                        </button>
                    </form>
                </div> 
                {{-- form Dji sync --}}
                <div id="content3" class="tab-content p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-4">{!! TranslationHelper::translateIfNeeded('DJi Sync') !!}</h1>
                    <form class="space-y-4">
                        <div class="form-group">
                            <label for="email" class="font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Email') !!}</label>
                            <input type="email" id="email" class="border border-gray-300 dark:border-gray-600 rounded-lg w-full p-2 mt-1 focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Password') !!}</label>
                            <input type="password" id="password" class="border border-gray-300 dark:border-gray-600 rounded-lg w-full p-2 mt-1 focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="since_sync" class="font-medium text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Sync Flights Since') !!}</label>
                            <input type="datetime-local" id="since_sync" class="border border-gray-300 dark:border-gray-600 rounded-lg w-full p-2 mt-1 focus:outline-none focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 ease-in-out">
                            {!! TranslationHelper::translateIfNeeded('Sync') !!}
                        </button>
                    </form>
                </div>
                {{--Billing Settings--}}
                <div id="content4" class="tab-content  p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
                    @livewire('billing')
                </div>
            </div>
        </div>
    </div>
 
    {{--Currency Setting--}}
    <div id="maincontent1" class="main-content flex justify-center bg-gray-50 dark:bg-gray-900 ">
        <div class="max-w-lg w-full p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('Currency Settings') !!}</h1>
           
            <form action="{{route('currency-store')}}" method="POST">
                @csrf
                
                <div class="mb-4">
                    
                    <label for="currency" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Choose your ssacurrency:') !!}</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="search-box" 
                            placeholder="Search currency..." 
                            class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2" 
                            oninput="filterAndSelect(this.value)"
                        >
                        <select 
                            name="currency_id" 
                            id="currency" 
                            required 
                            class="block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @php
                                $currencies = App\Models\currencie::all();
                                $currentTeam = auth()->user()->teams()->first();
                                $selectedCurrencyId = $currentTeam ? $currentTeam->currencies_id : null;
                            @endphp
                            @foreach($currencies as $currency)
                                <option 
                                    value="{{ $currency->id }}" 
                                    {{ $currency->id == $selectedCurrencyId ? 'selected' : '' }}>
                                    {{ $currency->name }} ({{ $currency->iso }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    
                </div>
    
                {{-- <div class="mb-4">
                    <label class="inline-flex items-center text-gray-700 dark:text-gray-400">
                        <input type="checkbox" name="set_as_default" value="1" class="form-checkbox h-5 w-5 text-blue-600 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500">
                        <span class="ml-2">Set as Default for Projects and Maintenance</span>
                    </label>
                </div> --}}
    
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 font-semibold bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-400 shadow">
                        {!! TranslationHelper::translateIfNeeded('Save Changes') !!} 
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    {{--languages Setting--}}
<div id="maincontent2" class="main-content flex justify-center bg-gray-50 dark:bg-gray-900 mt-6">
    <div class="max-w-lg w-full p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('Language Settings') !!}</h1>
        
        <form action="{{ route('change.language') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Choose your language:') !!}</label>
                <select name="language" id="language" required class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="en" {{ request()->cookie('locale') == 'en' ? 'selected' : '' }}>English</option>
                    <option value="es" {{ request()->cookie('locale') == 'es' ? 'selected' : '' }}>Spanish</option>
                    <option value="fr" {{ request()->cookie('locale') == 'fr' ? 'selected' : '' }}>French</option>
                    <option value="id" {{ request()->cookie('locale') == 'id' ? 'selected' : '' }}>Indonesian</option>
                    <option value="ko" {{ request()->cookie('locale') == 'ko' ? 'selected' : '' }}>Korean</option>
                    <option value="ja" {{ request()->cookie('locale') == 'ja' ? 'selected' : '' }}>Japan</option>
                </select>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 font-semibold bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-400 shadow">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
{{-- <script>
    document.getElementById('maincontent2').style.display = 'none';
</script> --}}



</div>
    
    <script>
        function showContentTab(index) {
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach((content, i) => {
                content.classList.remove('active');
                if (i === index) {
                    content.classList.add('active');
                }
            });
            const contentss = document.querySelectorAll('.tab-button');
            contentss.forEach((contentsss) => {
                contentsss.classList.remove('active');
            });
            const target = document.querySelector(`.tab-button[data-index="${index}"]`);
            if (target) {
                target.classList.add('active');
            }
        }
        function showContent(index) {
            const contents = document.querySelectorAll('.main-content');
            contents.forEach((content, i) => {
                content.classList.remove('active');
                if (i === index) {
                    content.classList.add('active');
                }
            });
        }
    </script>
    {{-- search Currency --}}
    <script>
        function filterAndSelect(searchValue) {
            const select = document.getElementById('currency');
            const options = select.querySelectorAll('option');

            // search value
            const query = searchValue.toLowerCase();
            let foundAndSelected = false;
            options.forEach(option => {
                const text = option.textContent.toLowerCase();

                // Show/hide options
                if (text.includes(query)) {
                    option.style.display = '';
                    if (!foundAndSelected) {
                        select.value = option.value;
                        foundAndSelected = true;
                    }
                } else {
                    option.style.display = 'none';
                }
            });

            if (!foundAndSelected) {
                select.value = '';
            }
        }


    </script>
    
    {{--end tab --}}
</x-filament-panels::page>
