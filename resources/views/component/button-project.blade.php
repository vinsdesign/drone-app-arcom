@php
    use App\Helpers\TranslationHelper;
@endphp
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
    .active{
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
    .button {
    position: relative;
    padding: 8px 16px;
    border: none;
    outline: none;
    border-radius: 2px;
    cursor: pointer;
    }

    .button__text {
    color: #ffffff;
    transition: all 0.2s;
    }

    .button--loading .button__text {
    visibility: hidden;
    opacity: 0;
    }

    .button--loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    border: 4px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: button-loading-spinner 1s ease infinite;
    }

    @keyframes button-loading-spinner {
    from {
        transform: rotate(0turn);
    }

    to {
        transform: rotate(1turn);
    }
    }
    /* warna filament bg*/
    .bg-red-50 {
        background-color: rgb(254, 242, 242);
    }
    .bg-red-100 {
        background-color: rgb(254, 226, 226);
    }
    .bg-red-200 {
        background-color: rgb(254, 202, 202);
    }
    .bg-red-300 {
        background-color: rgb(252, 165, 165);
    }
    .bg-red-400 {
        background-color: rgb(248, 113, 113);
    }
    .bg-red-500 {
        background-color: rgb(239, 68, 68);
    }
    .bg-red-600 {
        background-color: rgb(220, 38, 38);
    }
    .bg-red-700 {
        background-color: rgb(185, 28, 28);
    }
    .bg-red-800 {
        background-color: rgb(153, 27, 27);
    }
    .bg-red-900 {
        background-color: rgb(127, 29, 29);
    }
    .bg-red-950 {
        background-color: rgb(69, 10, 10);
    }
    /* Text color red */
    .text-red-50 {
        color: rgb(254, 242, 242);
    }
    .text-red-100 {
        color: rgb(254, 226, 226);
    }
    .text-red-200 {
        color: rgb(254, 202, 202);
    }
    .text-red-300 {
        color: rgb(252, 165, 165);
    }
    .text-red-400 {
        color: rgb(248, 113, 113);
    }
    .text-red-500 {
        color: rgb(239, 68, 68);
    }
    .text-red-600 {
        color: rgb(220, 38, 38);
    }
    .text-red-700 {
        color: rgb(185, 28, 28);
    }
    .text-red-800 {
        color: rgb(153, 27, 27);
    }
    .text-red-900 {
        color: rgb(127, 29, 29);
    }
    .text-red-950 {
        color: rgb(69, 10, 10);
    }
</style>
{{-- <script src="https://cdn.tailwindcss.com"></script> --}}

<div>
<!--alret massage   -->
@php
$response =session('successfully');
// dd($response);
@endphp
@if($response != null)
    <div id="success-notification-project" class="notification text-white p-4 rounded-lg shadow-lg flex items-center justify-between" style="background:#00c711">
        <span>Successfully</span>
        <button id="close-notification-project" class="ml-4 text-white hover:text-gray-200 focus:outline-none">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

  <!-- Tombol untuk Membuka Modal -->
    <button style="font-size: 12px; background-color: #4A5568; color: white; font-weight: bold; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;" onclick="openModal()" type="button">
        <span style="color: inherit; text-decoration: none;">
            {!! TranslationHelper::translateIfNeeded('Add New Project')!!}
        </span>
    </button>

    <!-- Modal -->
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
                {!! TranslationHelper::translateIfNeeded('Create Project')!!}
            </h2>
            <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

            {{-- error massages --}}
            <div id="bodyErrorMassagesProject" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
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
                            <span id="errorMassagesProject"></span>
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button type="button" onclick="closeMessagesProject()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
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
                    <!-- Case Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Case')!!}</label>
                        <input id="case" type="text" name="case" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>

                    <!-- Revenue Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Revenue')!!}</label>
                        <input id="revenue" type="number" name="revenue" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>

                    <!-- Currency Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Currency')!!}</label>
                        <select id="currencies_id" name="currencies_id" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            @php
                                $currencies = App\Models\currencie::all()->mapWithKeys(function ($currency) {
                                    return [$currency->id => "{$currency->name} - {$currency->iso}"];
                                });

                                $defaultCurrencyId = optional(auth()->user()->teams()->first())->currencies_id;
                            @endphp
                            <option value="" disabled selected>{!! TranslationHelper::translateIfNeeded('Choose a currency')!!}</option>
                            @foreach ($currencies as $id => $currency)
                                <option value="{{ $id }}" {{ $defaultCurrencyId == $id ? 'selected' : '' }}>
                                    {{ $currency }}
                                </option>
                            @endforeach
                                </select>
                    </div>

                    <!-- Customer Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Customer Name')!!}</label>
                        <select id="customers_id" name="customers_id" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            <option value="" disabled selected>{!! TranslationHelper::translateIfNeeded('Select an Customer')!!}</option>
                            @if(App\Models\customer::where('teams_id', auth()->user()->teams()->first()->id)->count() == null)
                                <option value="" disabled>{!! TranslationHelper::translateIfNeeded('SNo Customer Found')!!}</option>
                            @else
                                @foreach (App\Models\customer::where('teams_id', auth()->user()->teams()->first()->id)->pluck('name', 'id') as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Description Text Area -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Description')!!}</label>
                    <textarea id="description" name="description" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-4">
                    <button id="triggerButton" type="button" class="button" style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">
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
    function closeModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.add('active');
    }
    function openModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.remove('active');     
    }
    //messages close
    function closeMessagesProject() {
        document.getElementById('bodyErrorMassagesProject').style.display = 'none';
    }
</script>
{{-- test ajax ke controller action --}}
<script>
    $(document).ready(function() {
        $('#triggerButton').click(function() {
            const $button = $(this);
            const name = $('#name').val();
            const caseValue = $('#case').val();
            const revenuValue = $('#revenue').val();
            const customerValue = $('#customers_id').val();
            const currencieValue = $('#currencies_id').val();
            const descriptionValue = $('#description').val();

            console.log(name, caseValue, revenuValue, customerValue, currencieValue, descriptionValue);
            // Validasi input kosong
            if (caseValue.trim() === '') {
                document.getElementById('bodyErrorMassagesProject').style.display = 'block';
                document.getElementById('errorMassagesProject').textContent = 'Name cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesProject').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesProject').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(revenuValue.trim() !== '') {
                document.getElementById('bodyErrorMassagesProject').style.display = 'block';
                document.getElementById('errorMassagesProject').textContent = 'Revenue cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesProject').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesProject').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else if(customerValue.trim() == ''){
                document.getElementById('bodyErrorMassagesProject').style.display = 'block';
                document.getElementById('errorMassagesProject').textContent = 'Customer cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesProject').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesProject').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if (currencieValue.trim() = ''){
                document.getElementById('bodyErrorMassagesProject').style.display = 'block';
                document.getElementById('errorMassagesProject').textContent = 'Currencie cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesProject').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesProject').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } 
            else {
                $button.toggleClass("button--loading");
                $.ajax({
                    url: '{{ route('create-project') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Laravel CSRF token
                        case: caseValue,
                        revenue: revenuValue,
                        customer: customerValue,
                        currency: currencieValue,
                        description: descriptionValue
                    },
                    success: function(response) {
                        console.log(response);
                        // $("#success-notification-project").removeClass("hidden-notif");
                        // $("#success-notification-project").addClass("notification");

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
        });
    });
</script>
{{-- notification --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notification = document.getElementById('success-notification-project');
        const closeButton = document.getElementById('close-notification-project');
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

