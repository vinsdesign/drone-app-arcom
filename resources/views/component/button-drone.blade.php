@php
    use App\Helpers\TranslationHelper;
    $users = App\Models\User::whereHas('teams', function ($query) {
    $query->where('teams.id', auth()->user()->teams()->first()->id);
 });
@endphp
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<div>
{{-- <script src="https://cdn.tailwindcss.com"></script> --}}
<!--alret massage   -->
<div id="success-notification-drone" class="hidden-notif bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center justify-between">
    <span>Successfully</span>
    <button id="close-notification-drone" class="ml-4 text-white hover:text-gray-200 focus:outline-none">
        <i class="fas fa-times"></i>
    </button>
</div>
  <!-- Tombol untuk Membuka Modal -->
    <button style="font-size: 12px; background-color: #4A5568; color: white; font-weight: bold; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;" onclick="openModalDrone()" type="button">
        <span style="color: inherit; text-decoration: none;">
            {!! TranslationHelper::translateIfNeeded('Add New Drone')!!}
        </span>
    </button>

    <!-- Modal -->
    <div class="fixed drone active inset-0 flex justify-center z-50" style="max-height: 80%">
        <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
            <!-- Tombol Close -->
            <button type="button"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                onclick="closeModalDrone()">
                 &times;
            </button>

            <!-- Judul Modal -->
            <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                {!! TranslationHelper::translateIfNeeded('Create Drone')!!}
            </h2>
            <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">
            {{-- error massages --}}
            <div id="bodyErrorMassagesDrone" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
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
                            <span id="errorMassagesDrone"></span>
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button type="button" onclick="closeMessagesDrone()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
                            data-bs-dismiss="alert" aria-label="Close">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Form -->
            <div>
                @csrf
                {{-- <input type="hidden" name="teams_id" value="{{ auth()->user()->teams()->first()->id ?? null }}"> --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Name')!!}</label>
                        <input id="namedrone" type="text" name="name" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Model Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Model')!!}</label>
                        <input id="modeldrone" type="text" name="model" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Brand Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Brand')!!}</label>
                        <input id="branddrone" type="text" name="brand" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Type Input -->
                    <div>
                        <label for="type" class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Type')!!}</label>
                        <select id="typedrone" name="type" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="" disabled selected>{!! TranslationHelper::translateIfNeeded('Select Type')!!}</option>
                            <option value="aircraft">Aircraft</option>
                            <option value="autoPilot">AutoPilot</option>
                            <option value="boat">Boat</option>
                            <option value="fixed_wing">Fixed-Wing</option>
                            <option value="flight controller">Flight Controller</option>
                            <option value="flying-wings">Flying-Wings</option>
                            <option value="fpv">FPV</option>
                            <option value="hexsacopter">Hexsacopter</option>
                            <option value="home-made">Home-Made</option>
                            <option value="multi-rotors">Multi-Rotors</option>
                            <option value="quadcopter">Quadcopter</option>
                            <option value="rover">Rover</option>
                            <option value="rpa">RPA</option>
                            <option value="submersible">Submersible</option>
                        </select>
                    </div>
                    
            
                    <!-- Serial P Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Serial Printed')!!}</label>
                        <input id="serial_pdrone" type="text" name="serial_p" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Serial I Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Serial Internal')!!}</label>
                        <input id="serial_idrone" type="text" name="serial_i" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Flight Count Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Flight Count')!!}</label>
                        <input id="flight_cdrone" type="number" name="flight_c" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Remote C Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Remote Controller')!!}</label>
                        <input id="remote_cdrone" type="text" name="remote_c" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Remote Controller2')!!}</label>
                        <input id="remote_ccdrone" type="text" name="remote_c" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Status')!!}</label>
                        <select id="statusdrone" name="inventory_asset" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="airworthy">airworthy</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="retired">Retired</option>
                        </select>
                    </div>
                </div>
                <!--Legal ID-->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Legal ID')!!}</label>
                    <input id="idlegaldrone" type="text" name="remote_c" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                </div>
            
                <!-- Drone Details Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Inventory Asset Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Inventory/Asset')!!}</label>
                        <select id="inventory_assetdrone" name="inventory_asset" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="inventory">Inventory</option>
                            <option value="asset">Asset</option>
                        </select>
                    </div>

                    <!-- drone Geometry -->
                    <div>
                        <label for="geometry" class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Drone Geometry')!!}</label>
                        <select id="geometrydrone" name="geometry" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="" disabled selected>{!! TranslationHelper::translateIfNeeded('Select Geometry')!!}</option>
                            <option value="dual_rotor_coaxial">Dual Rotor Coaxial</option>
                            <option value="fixed_wing_1">Fixed Wing 1</option>
                            <option value="fixed_wing_2">Fixed Wing 2</option>
                            <option value="fixed_wing_3">Fixed Wing 3</option>
                            <option value="hexa_plus">Hexa +</option>
                            <option value="hexa_x">Hexa X</option>
                            <option value="octa_plus">Octa +</option>
                            <option value="octa_v">Octa V</option>
                            <option value="octa_x">Octa X</option>
                            <option value="quad_plus">Quad +</option>
                            <option value="quad_x">Quad X</option>
                            <option value="quad_x_dji">Quad X DJI</option>
                            <option value="single_rotor">Single Rotor</option>
                            <option value="tri">Tri</option>
                            <option value="vtol_1">VTOL 1</option>
                            <option value="vtol_2">VTOL 2</option>
                            <option value="vtol_3">VTOL 3</option>
                            <option value="vtol_4">VTOL 4</option>
                            <option value="x8_coaxial">X8 Coaxial</option>
                            <option value="x6_coaxial">X6 Coaxial</option>
                        </select>
                    </div>
                    

                    <!-- Users Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Owner')!!}</label>
                        <select id="users_iddrone" name="users_id" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="" disabled selected>{!! TranslationHelper::translateIfNeeded('Select Users')!!}</option>
                            @if($users === null)
                                <option value="" disabled selected>{!! TranslationHelper::translateIfNeeded('No Users Foundr')!!}</option>
                            @else
                                @foreach (App\Models\User::whereHas('teams', function ($query) {
                                        $query->where('teams.id', auth()->user()->teams()->first()->id);
                                    })->pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
            
                    <!-- Firmware Version Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Firmware Version')!!}</label>
                        <input id="firmware_vdrone" type="text" name="firmware_v" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Hardware Version Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Hardware Version')!!}</label>
                        <input id="hardware_vdrone" type="text" name="hardware_v" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Propulsion Version Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Propulsion Version')!!}</label>
                        <select id="propulsion_vdrone" name="propulsion_vdrone" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="" disabled selected>{!! TranslationHelper::translateIfNeeded('Select an propulsion version')!!}</option>
                            <option value="electric">Electric</option>
                            <option value="fuel">Fuel</option>
                            <option value="hydrogen">Hydrogen</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="turbine">Turbine</option>
                        </select>
                        
                    </div>
            
                    <!-- Color Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Color')!!}</label>
                        <input id="colordrone" type="text" name="color" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Remote Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Remote ID')!!}</label>
                        <input id="remotedrone" type="text" name="remote" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
                </div>
            
                <!-- Flight Information Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Conn Card Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Connection Card')!!}</label>
                        <input id="conn_carddrone" type="text" name="conn_card" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
                    <!-- Initial Flight Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Initial Flight')!!}</label>
                        <input id="initial_flightdrone" type="number" name="initial_flight" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Initial Flight Time Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Initial Flight Time')!!}</label>
                        <input 
                            id="initial_flight_timedrone" 
                            type="text" 
                            name="initial_flight_time" 
                         
                            class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500" 
                            oninput="this.value = this.value.replace(/[^0-9:]/g, '').replace(/^([0-9]{2})([0-9]{2})/, '$1:$2:');" 
                            placeholder="HH:mm:ss" 
                            value="00:00:00"
                        >
                    </div>
                    
            
                    <!-- Max Flight Time Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Max Flight Time')!!}</label>
                        <input 
                        id="max_flight_timedrone" 
                        type="text" 
                        name="max_flight_time" 
                        class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"
                        oninput="this.value = this.value.replace(/[^0-9:]/g, '').replace(/^([0-9]{2})([0-9]{2})/, '$1:$2:');" 
                        placeholder="HH:mm:ss" 
                        value="00:00:00"
                        >
                    </div>
                </div>
                    <!-- Description Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Description')!!}</label>
                        <textarea id="descriptiondrone" name="description" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                    </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-4">
                    <button id="triggerButtonDrone" type="button" 
                        style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;"
                        class="button px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" onclick="createDrone()">
                        <span class="button__text">{!! TranslationHelper::translateIfNeeded('Submit')!!}</span>
                    </button>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- close and open pop-up --}}
<script>
    function closeModalDrone() {
        const contents = document.querySelector('.drone');
        contents.classList.add('active');
    }
    function openModalDrone() {
        const contents = document.querySelector('.drone');
        contents.classList.remove('active');     
    }
        //messages close
    function closeMessagesDrone() {
        document.getElementById('bodyErrorMassagesDrone').style.display = 'none';
    }
</script>
{{-- test ajax ke controller action --}}
<script>
    function createDrone() {
        const $button = $('#triggerButtonDrone');
        const nameValue = $('#namedrone').val();
        const statusValue = $('#statusdrone').val();
        const idLegalValue = $('#idlegaldrone').val();
        const brandValue = $('#branddrone').val();
        const modelValue = $('#modeldrone').val();
        const typeValue = $('#typedrone').val();
        const serialPValue = $('#serial_pdrone').val();
        const serialIValue = $('#serial_idrone').val();
        const flightCValue = $('#flight_cdrone').val();
        const remoteCValue = $('#remote_cdrone').val();
        const remoteCCValue = $('#remote_ccdrone').val();
        const geometryValue = $('#geometrydrone').val();
        const inventoryAssetValue = $('#inventory_assetdrone').val();
        const descriptionValue = $('#descriptiondrone').val();
        const usersIdValue = $('#users_iddrone').val();
        const firmwareVValue = $('#firmware_vdrone').val();
        const hardwareVValue = $('#hardware_vdrone').val();
        const propulsionVValue = $('#propulsion_vdrone').val();
        const colorValue = $('#colordrone').val();
        const remoteValue = $('#remotedrone').val();
        const connCardValue = $('#conn_carddrone').val();
        const initialFlightValue = $('#initial_flightdrone').val();
        const initialFlightTimeValue = $('#initial_flight_timedrone').val();
        const maxFlightTimeValue = $('#max_flight_timedrone').val();


        // Validate empty fields
        if (nameValue.trim() === '') {
            document.getElementById('bodyErrorMassagesDrone').style.display = 'block';
            document.getElementById('errorMassagesDrone').textContent = 'Name cannot be null';
                document.getElementById('bodyErrorMassagesDrone').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            setTimeout(() => {
                document.getElementById('bodyErrorMassagesDrone').style.display = 'none';
            }, 5000);
        }else if (statusValue.trim() === ''){
            document.getElementById('bodyErrorMassagesDrone').style.display = 'block';
            document.getElementById('errorMassagesDrone').textContent = 'Status cannot be null';
                document.getElementById('bodyErrorMassagesDrone').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            setTimeout(() => {
                document.getElementById('bodyErrorMassagesDrone').style.display = 'none';
            }, 5000);
        }else{
            $button.toggleClass("button--loading");
            // Send data via AJAX
            $.ajax({
                url: '{{ route('create-drone') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Laravel CSRF token
                    name: nameValue,
                    status: statusValue,
                    idlegal: idLegalValue,
                    brand: brandValue,
                    model: modelValue,
                    type: typeValue,
                    serial_p: serialPValue,
                    serial_i: serialIValue,
                    flight_c: flightCValue,
                    remote_c: remoteCValue,
                    remote_cc: remoteCCValue,
                    geometry: geometryValue,
                    inventory_asset: inventoryAssetValue,
                    description: descriptionValue,
                    users_id: usersIdValue,
                    firmware_v: firmwareVValue,
                    hardware_v: hardwareVValue,
                    propulsion_v: propulsionVValue,
                    color: colorValue,
                    remote: remoteValue,
                    conn_card: connCardValue,
                    initial_flight: initialFlightValue,
                    initial_flight_time: initialFlightTimeValue,
                    max_flight_time: maxFlightTimeValue,
                },
                success: function(response) {
                    console.log(response);
                    $("#success-notification-drone").removeClass("hidden-notif")
                    $("#success-notification-drone").addClass("notification")
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    console.error('error:', error);
                    $button.removeClass("button--loading");
                }
            });
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notification = document.getElementById('success-notification-drone');
        const closeButton = document.getElementById('close-notification-drone');
        if (notification) {
            setTimeout(() => {
                notification.style.display = 'none';
            }, 8000); // Hide after 8 seconds

            closeButton.addEventListener('click', () => {
                notification.style.display = 'none';
            });
        }
    });
</script>
