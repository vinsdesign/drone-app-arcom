
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom styles for Laravel Filament-like appearance */
        .filament-form {
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .filament-form input,
        .filament-form select,
        .filament-form button {
            border: 1px solid #d1d5db; /* Gray-300 */
            border-radius: 0.375rem; /* md */
        }
        .filament-form button {
            background-color: #3b82f6; /* Blue-600 */
            color: white;
        }
        .filament-form button:hover {
            background-color: #2563eb; /* Blue-700 */
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="max-w-lg w-full filament-form p-6">
        <h1 class="text-2xl font-bold mb-4">Currency Settings</h1>

        <form action="{{ route('currency-store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="currency" class="block text-sm font-medium text-gray-700">Choose your currency:</label>
                <select name="currency_id" id="currency" required class="mt-1 block w-full">
                    @php
                        $currentTeam = auth()->user()->teams()->first();
                        $selectedCurrencyId = $currentTeam ? $currentTeam->currencies_id : null;
                    @endphp
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" 
                            {{ $currency->id == $selectedCurrencyId ? 'selected' : '' }}>
                        {{ $currency->name }} ({{ $currency->iso }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="set_as_default" value="1" class="form-checkbox">
                    <span class="ml-2">Set as Default for Projects and Maintenance</span>
                </label>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2" style="background-color: #ff8303; color: white; font-weight: 600; padding: 10px; border-radius: 0.375rem;">Save Changes</button>
            </div>
        </form>
    </div>

</body>
</html>