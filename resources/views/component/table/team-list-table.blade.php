
<div class="grid grid-cols-1 gap-2 sm:grid-cols-3 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
    <div class="p-3 text-xs">
        <span class="font-medium text-blue-700 dark:text-blue-300">Owners:</span>
        <span class="block text-gray-800 dark:text-gray-100">{{ $getRecord()->owner }}</span>
    </div>
    <div class="p-3 text-xs">
        <span class="font-medium">Phone:</span>
        <span class="block text-gray-800 dark:text-gray-100">{{ $getRecord()->phone }}</span>
    </div>
    <div class="p-3 text-xs">
        <span class="font-medium">Email:</span>
        <span class="block text-gray-800 dark:text-gray-100">{{ $getRecord()->email }}</span>
    </div>
</div>

<div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg text-xs">
    <div class="p-3 rounded-lg">
        <span style="font: bold;" class="font-medium">Address:</span>
        <span class="block text-gray-800 dark:text-gray-100">{{ $getRecord()->address }}</span>
    </div>
</div>
