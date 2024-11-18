{{-- <head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head> --}}
@php
    use App\Helpers\TranslationHelper;
@endphp
{{-- <script src="https://cdn.tailwindcss.com"></script> --}}
<div>
<!--alret massage   -->
<div id="success-notification-equipment" class="hidden-notif bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center justify-between">
    <span>Successfully</span>
    <button id="close-notification-equipment" class="ml-4 text-white hover:text-gray-200 focus:outline-none">
        <i class="fas fa-times"></i>
    </button>
</div>


  <!-- Tombol untuk Membuka Modal -->
    <button style="font-size: 12px; background-color: #4A5568; color: white; font-weight: bold; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;" onclick="openModalEquipment()" type="button">
        <span style="color: inherit; text-decoration: none;">
            {!! TranslationHelper::translateIfNeeded('Add New Equipment')!!}
        </span>
    </button>

    <!-- Modal -->
    <div class="fixed equipment active inset-0 flex justify-center z-50" style="max-height: 80%">
        <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
            <!-- Tombol Close -->
            <button type="button"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                onclick="closeModalEquipment()">
                 &times;
            </button>

            <!-- Judul Modal -->
            <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                {!! TranslationHelper::translateIfNeeded('Create Equipment')!!}
            </h2>
            <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

            <!-- Form -->
            <div>
                @csrf
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Name')!!}</label>
                        <input id="nameequipment" type="text" name="name" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Model Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Model')!!}</label>
                        <input id="modelequipment" type="text" name="model" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Status Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Status')!!}</label>
                        <select id="statusequipment" name="statusbattrei" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="airworthy">airworthy</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="retired">Retired</option>
                        </select>
                    </div>
            
                    <!-- Inventory Asset Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Inventory/Asset')!!}</label>
                        <select id="inventory_assetequipment" name="inventory_asset" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="inventory">Inventory</option>
                            <option value="asset">Asset</option>
                        </select>
                    </div>
            
                    <!-- Serial Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Serial')!!}</label>
                        <input id="serialequipment" type="text" name="serial" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Type Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Type')!!}</label>
                        <select id="typeequipment" name="type" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="airframe">Airframe</option>
                            <option value="anenometer">Anenometer</option>
                            <option value="battery">Battery</option>
                            <option value="battery_charger">Battery Charger</option>
                            <option value="camera">Camera</option>
                            <option value="charger">Charger</option>
                            <option value="cradle">Cradle</option>
                            <option value="drives(disc, flash)">Drives(disc, flash)</option>
                            <option value="flight_controller">Flight Controller</option>
                            <option value="fvp_glasses">FVP Glasses</option>
                            <option value="gimbal">Gimbal</option>
                            <option value="gps">GPS</option>
                            <option value="lens">Lens</option>
                            <option value="lights">Lights</option>
                            <option value="monitor">Monitor</option>
                            <option value="motor">Motor</option>
                            <option value="parachute">Parachute</option>
                            <option value="phone/tablet">Phone/Tablet</option>
                            <option value="power_supply">Power Supply</option>
                            <option value="prop_guards">Prop Guards</option>
                            <option value="propeller">Propeller</option>
                            <option value="radio_receiver">Radio Receiver</option>
                            <option value="radio_transmitter">Radio Transmitter</option>
                            <option value="radio_extender">Radio Extender</option>
                            <option value="radio_finder(laser)">Radio Finder(laser)</option>
                            <option value="remote_controller">Remote Controller</option>
                            <option value="sensors">Sensors</option>
                            <option value="spreader">Spreader</option>
                            <option value="telementry_radio">Telemetry Radio</option>
                            <option value="tripod">Tripod</option>
                            <option value="video_transmitter">Video Transmitter</option>
                        </select>
                        
                    </div>
            
                    <!-- Drone Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('For Drone (Optional)')!!}</label>
                        <select id="drones_idequipment" name="drones_id" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            @foreach (App\Models\Drone::where('teams_id', auth()->user()->teams()->first()->id)->pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- User Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Owner')!!}</label>
                        <select id="users_idequipment" name="users_idequipment" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            @foreach (App\Models\User::whereHas('teams', function ($query) {
                                    $query->where('teams.id', auth()->user()->teams()->first()->id);
                                })->pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- Purchase Date Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Purchase Date')!!}</label>
                        <input id="purchase_dateequipment" type="date" name="purchase_date" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Insurable Value Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Insurable Value')!!}</label>
                        <input id="insurable_valueequipment" type="number" name="insurable_value" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Weight Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Weight')!!}</label>
                        <input id="weightequipment" type="number" name="weight" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Is Loaner Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Is Loaner')!!}</label>
                        <select id="is_loanerequipment" name="is_loaner" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
            
                    <!-- Firmware Version Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Firmware Version')!!}</label>
                        <input id="firmware_vequipment" type="text" name="firmware_v" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Hardware Version Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Hardware Version')!!}</label>
                        <input id="hardware_vequipment" type="text" name="hardware_v" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                </div>
                     <!-- Description Text Area -->
                     <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Description')!!}</label>
                        <textarea id="descriptionequipment" name="description" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                    </div>
            
                <!-- Submit Button -->
                <div class="flex justify-end mt-4">
                    <button id="triggerButton" type="button" 
                        style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;"
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" onclick="createEquipment()">
                        {!! TranslationHelper::translateIfNeeded('Submit')!!}
                    </button>
                </div>
            </div>            
            
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- close and open pop-up --}}
<script>
    function closeModalEquipment() {
        const contents = document.querySelector('.equipment');
        contents.classList.add('active');
    }
    function openModalEquipment() {
        const contents = document.querySelector('.equipment');
        contents.classList.remove('active');     
    }
</script>
<script>
    function createEquipment() {
        const nameValue = $('#nameequipment').val();
        const modelValue = $('#modelequipment').val();
        const statusValue = $('#statusequipment').val();
        const inventoryAssetValue = $('#inventory_assetequipment').val();
        const serialValue = $('#serialequipment').val();
        const typeValue = $('#typeequipment').val();
        const dronesIdValue = $('#drones_idequipment').val();
        const usersIdValue = $('#users_idequipment').val();
        const purchaseDateValue = $('#purchase_dateequipment').val();
        const insurableValueValue = $('#insurable_valueequipment').val();
        const weightValue = $('#weightequipment').val();
        const isLoanerValue = $('#is_loanerequipment').val();
        const firmwareVValue = $('#firmware_vequipment').val();
        const hardwareVValue = $('#hardware_vequipment').val();
        const descriptionValue = $('#descriptionequipment').val();

        // Log for debugging
        console.log({
            name: nameValue,
            model: modelValue,
            status: statusValue,
            inventory_asset: inventoryAssetValue,
            serial: serialValue,
            type: typeValue,
            drones_id: dronesIdValue,
            users_id: usersIdValue,
            purchase_date: purchaseDateValue,
            insurable_value: insurableValueValue,
            weight: weightValue,
            is_loaner: isLoanerValue,
            firmware_v: firmwareVValue,
            hardware_v: hardwareVValue,
            description: descriptionValue
        });

        // Validate empty fields
        if (
                nameValue.trim() === '' ||
                modelValue.trim() === '' ||
                statusValue.trim() === '' ||
                inventoryAssetValue.trim() === '' ||
                serialValue.trim() === '' ||
                typeValue.trim() === '' ||
                usersIdValue.trim() === '' ||
                purchaseDateValue.trim() === '' ||
                insurableValueValue.trim() === '' ||
                weightValue.trim() === '' ||
                isLoanerValue.trim() === '' ||
                firmwareVValue.trim() === '' ||
                hardwareVValue.trim() === '' ||
                descriptionValue.trim() === ''
            ) {
            alert('Name cannot be empty!');
            return;
        }

        // Send data via AJAX
        $.ajax({
            url: '{{ route('create-equipment') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Laravel CSRF token
                name: nameValue,
                model: modelValue,
                status: statusValue,
                inventory_asset: inventoryAssetValue,
                serial: serialValue,
                type: typeValue,
                drones_id: dronesIdValue,
                users_id: usersIdValue,
                purchase_date: purchaseDateValue,
                insurable_value: insurableValueValue,
                weight: weightValue,
                is_loaner: isLoanerValue,
                firmware_v: firmwareVValue,
                hardware_v: hardwareVValue,
                description: descriptionValue,
            },
            success: function(response) {
                console.log(response);
                $("#success-notification-equipment").removeClass("hidden-notif")
                $("#success-notification-equipment").addClass("notification")
                setTimeout(() => {
                    location.reload();
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.error('error:', error);
            }
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notification = document.getElementById('success-notification-equipment');
        const closeButton = document.getElementById('close-notification-equipment');
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

