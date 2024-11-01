<x-filament-panels::page>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<div class="block min-h-screen w-full">
    <div class="w-full p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <!-- Top Bar -->
        <div class="flex justify-between items-center bg-gray-700 dark:bg-gray-900 text-white p-4 rounded-t-lg">
            <div class="flex items-center space-x-2">
                <label for="log-type" class="font-semibold">Log Type:</label>
                <select id="log-type" class="bg-gray-200 text-black dark:bg-gray-700 dark:text-gray-100 border-none rounded p-2">
                    <option value="airdata">AirData CSV</option>
                    <option value="aeromapper">Aeromapper.DAT</option>
                    <option value="airspace">Airspace.CSV</option>
                    <option value="anzu">Anzu.Json</option>
                    <option value="apm">APM.LOG</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-gray-100 font-semibold rounded-lg hover:bg-gray-400 dark:hover:bg-gray-700">Single Log</button>
                <button class="px-4 py-2 bg-gray-500 dark:bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-600 dark:hover:bg-gray-800">Multiple Logs</button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-6 text-left">
            <label for="log-file" class="block text-lg font-semibold mb-2">Select your log file:</label>
            <div class="flex items-center space-x-4">
                <input type="file" id="log-file" class="bg-gray-200 dark:bg-gray-700 dark:text-gray-100 border rounded p-2 w-2/3">
                <button class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold rounded-lg border hover:bg-gray-300 dark:hover:bg-gray-800">
                    Analyze Flight Log
                </button>
            </div>
        </div>
        
    </div>
</div>


</x-filament-panels::page>