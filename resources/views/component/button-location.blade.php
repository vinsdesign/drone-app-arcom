@php
    use App\Helpers\TranslationHelper;
@endphp
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
{{-- <script src="https://cdn.tailwindcss.com"></script> --}}
<div>
<!--alret massage   -->
<div id="success-notification-location" class="hidden-notif bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center justify-between">
    <span>Successfully</span>
    <button id="close-notification-location" class="ml-4 text-white hover:text-gray-200 focus:outline-none">
        <i class="fas fa-times"></i>
    </button>
</div>


  <!-- Tombol untuk Membuka Modal -->
    <button style="font-size: 12px; background-color: #4A5568; color: white; font-weight: bold; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;" onclick="openModalLocation()" type="button">
        <span style="color: inherit; text-decoration: none;">
            {!! TranslationHelper::translateIfNeeded('Add New Location')!!}
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
                {!! TranslationHelper::translateIfNeeded('Create Location')!!}
            </h2>
            <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">
            {{-- error massages --}}
            <div id="bodyErrorMassagesLocation" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
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
                            <span id="errorMassagesLocation"></span>
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button type="button" onclick="closeMessagesLocation()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
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
                <!-- Name Input -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Name')!!}</label>
                    <input id="name" type="text" name="name" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Customer Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Customer')!!}</label>
                        <select id="customers_id" name="customers_id" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="" disabled selected>{!! TranslationHelper::translateIfNeeded('Select an Customer')!!}</option>
                            @if (App\Models\customer::where('teams_id', auth()->user()->teams()->first()->id)->count() == null)
                                <option value="" disabled>{!! TranslationHelper::translateIfNeeded('No Customer Available')!!}</option>
                            @else
                                @foreach (App\Models\customer::where('teams_id', auth()->user()->teams()->first()->id)->pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
            
                    <!-- Project Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Project')!!}</label>
                        <select id="projects_id" name="projects_id" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="" disabled selected>{!! TranslationHelper::translateIfNeeded('Select an projects')!!}</option>
                            @if (App\Models\projects::where('teams_id', auth()->user()->teams()->first()->id)->count() == null)
                                <option value="" disabled>{!! TranslationHelper::translateIfNeeded('No Project Found')!!}</option>
                            @else
                                @foreach (App\Models\projects::where('teams_id', auth()->user()->teams()->first()->id)->pluck('case', 'id') as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
            
                    <!-- Address Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Address')!!}</label>
                        <input id="address" type="text" name="address" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
                    <!-- Country Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Country')!!}</label>
                        <input id="country" type="text" name="country" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- City Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('City')!!}</label>
                        <input id="city" type="text" name="city" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- State Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('State')!!}</label>
                        <input id="states" type="text" name="states" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Postal Code Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Postal Code')!!}</label>
                        <input id="pos_code" type="number" name="pos_code" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Latitude Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Latitude')!!}</label>
                        <input id="latitude" type="number" name="latitude" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Longitude Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Longitude')!!}</label>
                        <input id="longitude" type="number" name="longitude" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
            
                    <!-- Altitude Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Altitude')!!}</label>
                        <input id="altitude" type="number" name="altitude" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
                </div>
                <!-- Description Input -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Description')!!}</label>
                    <textarea id="descriptionlocation" name="descriptionlocation" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                </div>
            
                <!-- Submit Button -->
                <div class="flex justify-end mt-4">
                    <button id="triggerButtonLocation" type="button" 
                        style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;"
                        class="button px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" onclick="createLocation()">
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
    function closeModalLocation() {
        const contents = document.querySelector('.location');
        contents.classList.add('active');
    }
    function openModalLocation() {
        const contents = document.querySelector('.location');
        contents.classList.remove('active');     
    }
    function closeMessagesLocation() {
        document.getElementById('bodyErrorMassagesLocation').style.display = 'none';
    }
</script>
{{-- test ajax ke controller action --}}
<script>
 function createLocation() {
    const $button = $('#triggerButtonLocation');
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

    // Validasi input kosong
    if (nameValue.trim() === '') {
        document.getElementById('bodyErrorMassagesLocation').style.display = 'block';
        document.getElementById('errorMassagesLocation').textContent = 'Name cannot be null';
            document.getElementById('bodyErrorMassagesLocation').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        setTimeout(() => {
            document.getElementById('bodyErrorMassagesLocation').style.display = 'none';
        }, 5000);
    }else if(addressValue.trim() === ''){
        document.getElementById('bodyErrorMassagesLocation').style.display = 'block';
        document.getElementById('errorMassagesLocation').textContent = 'Address cannot be null';
            document.getElementById('bodyErrorMassagesLocation').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        setTimeout(() => {
            document.getElementById('bodyErrorMassagesLocation').style.display = 'none';
        }, 5000);
    }else if(latitudeValue.trim() === ''){
        document.getElementById('bodyErrorMassagesLocation').style.display = 'block';
        document.getElementById('errorMassagesLocation').textContent = 'Latitude cannot be null';
            document.getElementById('bodyErrorMassagesLocation').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        setTimeout(() => {
            document.getElementById('bodyErrorMassagesLocation').style.display = 'none';
        }, 5000);
    }else if(longitudeValue.trim() === ''){
        document.getElementById('bodyErrorMassagesLocation').style.display = 'block';
        document.getElementById('errorMassagesLocation').textContent = ' cannot be null';
            document.getElementById('bodyErrorMassagesLocation').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        setTimeout(() => {
            document.getElementById('bodyErrorMassagesLocation').style.display = 'none';
        }, 5000);
    }
    else {
        $button.toggleClass("button--loading");
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
                $("#success-notification-location").removeClass("hidden-notif");
                $("#success-notification-location").addClass("notification");
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
{{-- notification --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notification = document.getElementById('success-notification-location');
        const closeButton = document.getElementById('close-notification-location');
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
