<x-filament-panels::page>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <div class="container mx-auto mt-10">
        <h2 class="mb-6 text-center text-2xl font-bold">Download Reports</h2>

        <div class="flex justify-center space-x-8">
            <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-xs">
                <h4 class="text-center text-lg font-semibold mb-4">Download Flight Report</h4>
                <form method="POST" action="{{ route('filament.report.download') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">From:</label>
                        <input type="date" name="start_date" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">To:</label>
                        <input type="date" name="end_date" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    </div>

                    <button type="submit" class="w-full" style="background-color: #ff8303; color: white; font-weight: 600; padding: 10px; border-radius: 0.375rem;">Download Report</button>
                </form>
            </div>

            <div class="bg-white p-20 rounded-lg shadow-md w-full max-w-xs">
                <h4 class="text-center text-lg font-semibold mb-4">Download Inventory Report</h4>
                <form method="POST" action="{{ route('filament.report.inventory.download') }}">
                    @csrf
                    <button type="submit" class="w-full" style="background-color: #ff8303; color: white; font-weight: 600; padding: 10px; border-radius: 0.375rem;">Download Report</button>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>
