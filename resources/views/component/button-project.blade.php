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
                            <option value="" disabled selected>Choose a currency</option>
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
                            @foreach (App\Models\customer::where('teams_id', auth()->user()->teams()->first()->id)->pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
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
                    <button id="triggerButton" type="button" 
                        style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;"
                        class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" onclick="closeModal()">
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
    function closeModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.add('active');
    }
    function openModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.remove('active');     
    }
</script>
{{-- test ajax ke controller action --}}
<script>
    $(document).ready(function() {
        $('#triggerButton').click(function() {
            const name = $('#name').val();
            const caseValue = $('#case').val();
            const revenuValue = $('#revenue').val();
            const customerValue = $('#customers_id').val();
            const currencieValue = $('#currencies_id').val();
            const descriptionValue = $('#description').val();

            console.log(name, caseValue, revenuValue, customerValue, currencieValue, descriptionValue);
            // Validasi input kosong
            if (caseValue.trim() === '' || revenuValue.trim() === '' || customerValue.trim() === '' || currencieValue.trim() === '' || descriptionValue.trim() === '') {
                alert('Name cannot be empty!');
            } else {
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
