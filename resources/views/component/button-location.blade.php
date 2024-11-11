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
    <button style="font-size: 12px; background-color: #4A5568; color: white; font-weight: bold; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;" onclick="openModalLocation()" type="button">
        <span style="color: inherit; text-decoration: none;">
            Add New Location
        </span>
    </button>

    <!-- Modal -->
    <div class="fixed location active inset-0 flex justify-center z-50" style="max-height: 80%">
        <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
            <!-- Tombol Close -->
            <button type="button"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                onclick="closeModalLocation()">
                 &times;
            </button>

            <!-- Judul Modal -->
            <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                Create Location
            </h2>
            <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

            <!-- Form -->
            <form id="customFormLocation" method="POST">
                @csrf
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Name</label>
                        <input id="name" type="text" name="name" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Description Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Description</label>
                        <textarea id="descriptionlocation" name="descriptionlocation" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                    </div>
            
                    <!-- Address Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Address</label>
                        <input id="address" type="text" name="address" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
                    <!-- Country Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Country</label>
                        <input id="country" type="text" name="country" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- City Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">City</label>
                        <input id="city" type="text" name="city" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- State Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">State</label>
                        <input id="states" type="text" name="states" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Postal Code Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Postal Code</label>
                        <input id="pos_code" type="number" name="pos_code" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Latitude Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Latitude</label>
                        <input id="latitude" type="number" name="latitude" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Longitude Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Longitude</label>
                        <input id="longitude" type="number" name="longitude" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Altitude Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Altitude</label>
                        <input id="altitude" type="number" name="altitude" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Customer Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Customer</label>
                        <select id="customers_id" name="customers_id" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            @foreach (App\Models\customer::where('teams_id', auth()->user()->teams()->first()->id)->pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- Project Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Project</label>
                        <select id="projects_id" name="projects_id" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            @foreach (App\Models\project::where('teams_id', auth()->user()->teams()->first()->id)->pluck('case', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            
                <!-- Submit Button -->
                <div class="flex justify-end mt-4">
                    <button id="triggerButtonLocation" type="button" 
                        style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;"
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" onclick="createLocation()">
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
    function closeModalLocation() {
        const contents = document.querySelector('.location');
        contents.classList.add('active');
    }
    function openModalLocation() {
        const contents = document.querySelector('.location');
        contents.classList.remove('active');     
    }
</script>
{{-- test ajax ke controller action --}}
<script>
 function createLocation() {
    const nameValue = $('#name').val();
    const addressValue = $('#address').val();
    const cityValue = $('#city').val();
    const stateValue = $('#states').val();
    const countryValue = $('#country').val();
    const poscodeValue = $('#pos_code').val();
    const latitudeValue = $('#latitude').val();
    const longitudeValue = $('#longitude').val();
    const altitudeValue = $('#altitude').val();
    const customerValue = $('#customers_id').val();
    const projectValue = $('#projects_id').val();
    const descriptionValue = $('#descriptionlocation').val();

    console.log(stateValue, customerValue, projectValue);
    console.log(descriptionValue, projectValue);
    // Validasi input kosong
    if (stateValue.trim() === '') {
        alert('State cannot be empty!');
    } else {
        $.ajax({
            url: '{{ route('create-Location') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Laravel CSRF token
                name: nameValue,
                address: addressValue,
                city: cityValue,
                country: countryValue,
                state: stateValue,
                pos_code: poscodeValue,
                latitude: latitudeValue,
                longitude: longitudeValue,
                altitude: altitudeValue,
                customers: customerValue,
                projects: projectValue,
                description: descriptionValue,
            },
            success: function(response) {
                console.log(response);
                $("#success-notification").removeClass("hidden-notif");
                $("#success-notification").addClass("notification");
                setTimeout(() => {
                    location.reload();
                }, 3000);
            },
            error: function(xhr, status, error) {
                console.error('error:', error);
            }
        });
    }
}

</script>
{{-- notification --}}
{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const notification = document.getElementById('success-notification');
        const closeButton = document.getElementById('close-notification');
        if (notification) {
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 8000); // Hide after 8 seconds

            closeButton.addEventListener('click', () => {
                notification.style.display = 'none';
            });
        }
    });
</script> --}}
