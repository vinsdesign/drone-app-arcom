<x-filament-panels::page>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <div class="container mx-auto mt-10">
        <h2 class="mb-6 text-center text-2xl font-bold dark:text-gray-100">Download Reports</h2>

        <div class="flex justify-center flex-wrap space-x-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md w-full max-w-xs mb-4">
                <h4 class="text-center text-lg font-semibold mb-4 dark:text-gray-100 mb-4">Download Flight Report</h4>
                <form method="POST" action="{{ route('filament.report.download') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From:</label>
                        <input type="date" name="start_date" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-700 dark:text-gray-300" required>
                    </div>
                    
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To:</label>
                        <input type="date" name="end_date" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-700 dark:text-gray-300" required>
                    </div>

                    <button type="submit" class="w-full" style="background-color: #ff8303; color: white; font-weight: 600; padding: 10px; border-radius: 0.375rem;">Download Report</button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 p-20 rounded-lg shadow-md w-full max-w-xs mb-4">
                <h4 class="text-center text-lg font-semibold mb-4 dark:text-gray-100">Download Inventory Report</h4>
                <form method="POST" action="{{ route('filament.report.inventory.download') }}">
                    @csrf
                    <button type="submit" class="w-full" style="background-color: #ff8303; color: white; font-weight: 600; padding: 10px; border-radius: 0.375rem;">Download Report</button>
                </form>
            </div>

            <div class="flex justify-center space-x-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md w-full max-w-xs mb-4">
                    <h4 class="text-center text-lg font-semibold mb-4 dark:text-gray-100">Download Income/Expense Report</h4>
                    <form method="POST" action="{{ route('filament.report.incomeExpense.download') }}">
                        @csrf
    
                        <div class="mb-4">
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From:</label>
                            <input type="date" name="start_date" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-700 dark:text-gray-300" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To:</label>
                            <input type="date" name="end_date" class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md p-2 dark:bg-gray-700 dark:text-gray-300" required>
                        </div>
    
                        <button type="submit" class="w-full" style="background-color: #ff8303; color: white; font-weight: 600; padding: 10px; border-radius: 0.375rem;">Download Report</button>
                    </form>
                </div>

        </div>
    </div>
</x-filament-panels::page>
