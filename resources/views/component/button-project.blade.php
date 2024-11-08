<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Example</title>
    {{-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> --}}
    <style>
        .active{
            display: none;
        }
    </style>
</head>
{{--test--}}
<!-- resources/views/filament/projects-form.blade.php -->
<div class="fixed active inset-0 flex justify-center z-50" style="max-height: 80% ">
    <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
        <!-- Tombol Close -->
        <button type="button"
        class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
        onclick="closeModal()">
             &times;
        </button>
        <!-- Content -->
        <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
            Create Project
        </h2>
        <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">
        {{-- form --}}
        <div class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
            <form id="customForm'" action="{{route('create-project')}}" method="POST">
                @csrf
                <input type="hidden" name="teams_id" value="{{ auth()->user()->teams()->first()->id ?? null }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Case Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Case</label>
                        <input type="text" name="case" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>

                    <!-- Revenue Input -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Revenue</label>
                        <input type="number" name="revenue" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                    </div>
                    
                    <!-- Currency Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Currency</label>
                        <select name="currencies_id" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            @foreach (App\Models\currencie::all() as $currency)
                                <option value="{{ $currency->id }}">
                                    {{ $currency->name }} - {{ $currency->iso }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Customer Select -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300">Customer Name</label>
                        <select name="customers_id" required class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            @foreach (App\Models\customer::where('teams_id', auth()->user()->teams()->first()->id)->pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Description Text Area -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" required maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                </div>
                <!-- Submit Button -->
                <div class="flex justify-end mt-4">
                    <button type="button" id="'submitButton" style="font-size: 12px; background-color: #4A5568; color: white; font-weight: bold; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" onclick="validateAndSubmit()">Submit</button></a>
                </div>
            </form>
      
        </div>
    </div>
</div>
{{--form--}}
<button style="font-size: 12px; background-color: #4A5568; color: white; font-weight: bold; padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer;" onclick="openModal()">
    <a href="#" style="color: inherit; text-decoration: none;">
        Add New Project
    </a>
</button>
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
<script>
    function validateAndSubmit() {
        const form = document.getElementById('customForm');
        if (!form) {
            console.error("Form not found!");
            return;
        }
        const form = document.getElementById('customForm');
        const caseInput = form.querySelector('input[name="case"]');
        const revenueInput = form.querySelector('input[name="revenue"]');
        const currencySelect = form.querySelector('select[name="currencies_id"]');
        const customerSelect = form.querySelector('select[name="customers_id"]');
        const descriptionTextarea = form.querySelector('textarea[name="description"]');

        // Validate inputs and log values
        if (!caseInput.value) {
            console.error("Case field is empty.");
            caseInput.focus();
            return;
        }
        if (!revenueInput.value) {
            console.error("Revenue field is empty.");
            revenueInput.focus();
            return;
        }
        if (!currencySelect.value) {
            console.error("Currency is not selected.");
            currencySelect.focus();
            return;
        }
        if (!customerSelect.value) {
            console.error("Customer Name is not selected.");
            customerSelect.focus();
            return;
        }
        if (!descriptionTextarea.value) {
            console.error("Description field is empty.");
            descriptionTextarea.focus();
            return;
        }

        // Log values to console
        console.log("Case:", caseInput.value);
        console.log("Revenue:", revenueInput.value);
        console.log("Currency ID:", currencySelect.value);
        console.log("Customer ID:", customerSelect.value);
        console.log("Description:", descriptionTextarea.value);

        // If all validations pass, submit the form
        form.submit();
    }
</script>


