{{-- <head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head> --}}
<style>
    .active{
        display: none;
    }
    .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 50;
            }
    .hidden-notif
    {
        display: none;
    }
</style>
<div>
    {{-- @php
    dd(session()->all());
    @endphp --}}
<!--alret massage   -->
{{-- <div id="success-notification" class="hidden-notif bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center justify-between">
    <span>test notification</span>
    <button id="close-notification" class="ml-4 text-white hover:text-gray-200 focus:outline-none">
        <i class="fas fa-times"></i>
    </button>
</div> --}}


  <!-- Tombol untuk Membuka Modal -->
    <button style="font-size: 12px; background-color: #4A5568; color: white; font-weight: bold; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;" onclick="openModalEquipment()" type="button">
        <span style="color: inherit; text-decoration: none;">
            Add New Equipment
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
                Create Equipment
            </h2>
            <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

            <!-- Form -->
            <form id="customForm" method="POST">
                @csrf
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Name</label>
                        <input id="nameequipment" type="text" name="name" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Model Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Model</label>
                        <input id="modelequipment" type="text" name="model" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Status Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Status</label>
                        <input id="statusequipment" type="text" name="status" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Inventory Asset Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Inventory Asset</label>
                        <input id="inventory_assetequipment" type="text" name="inventory_asset" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Serial Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Serial</label>
                        <input id="serialequipment" type="text" name="serial" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Type Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Type</label>
                        <input id="typeequipment" type="text" name="type" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Drone Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Drone</label>
                        <select id="drones_idequipment" name="drones_id" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            @foreach (App\Models\Drone::where('teams_id', auth()->user()->teams()->first()->id)->pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- User Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Owner</label>
                        <select id="users_idequipment" name="users_idequipment" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            @foreach (App\Models\User::whereHas('teams', function ($query) {
                                    $query->where('teams.id', auth()->user()->teams()->first()->id);
                                })->pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- Purchase Date Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Purchase Date</label>
                        <input id="purchase_dateequipment" type="date" name="purchase_date" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Insurable Value Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Insurable Value</label>
                        <input id="insurable_valueequipment" type="number" name="insurable_value" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Weight Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Weight</label>
                        <input id="weightequipment" type="number" name="weight" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Is Loaner Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Is Loaner</label>
                        <select id="is_loanerequipment" name="is_loaner" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
            
                    <!-- Firmware Version Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Firmware Version</label>
                        <input id="firmware_vequipment" type="text" name="firmware_v" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Hardware Version Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Hardware Version</label>
                        <input id="hardware_vequipment" type="text" name="hardware_v" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                </div>
                     <!-- Description Text Area -->
                     <div>
                        <label class="block text-gray-700 dark:text-gray-300">Description</label>
                        <textarea id="descriptionequipment" name="description" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                    </div>
            
                <!-- Submit Button -->
                <div class="flex justify-end mt-4">
                    <button id="triggerButton" type="button" 
                        style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;"
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" onclick="createEquipment()">
                        Submit
                    </button>
                </div>
            </form>            
            
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
        if (nameValue.trim() == '') {
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
                $("#success-notification").removeClass("hidden-notif").addClass("notification");
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

