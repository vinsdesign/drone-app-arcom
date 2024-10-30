<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Menu 1 -->
        <div class="bg-white shadow rounded-lg p-4 text-center hover:shadow-lg transition-shadow">
            <div class="text-blue-500 mb-2">
                <x-heroicon-o-globe-alt class="w-8 h-8 mx-auto" />
            </div>
            <h2 class="text-lg font-semibold">General Settings</h2>
            <p class="text-gray-600 mt-1">Set your website here</p>
        </div>

        <!-- Menu 2 -->
        <div class="bg-white shadow rounded-lg p-4 text-center hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('currency-settings')}}'">
            <div class="mb-2">
                <x-heroicon-s-currency-dollar class="w-8 h-8 mx-auto" />
            </div>
            <h2 class="text-lg font-semibold" >Currency</h2>
            <p class="text-gray-600 mt-1">Manage your currency here</p>
        </div>

        <!-- Menu 3 -->
        <div class="bg-white shadow rounded-lg p-4 text-center hover:shadow-lg transition-shadow">
            <div class="text-yellow-500 mb-2">
                <x-heroicon-c-language class="w-8 h-8 mx-auto" />
            </div>
            <h2 class="text-lg font-semibold">Language</h2>
            <p class="text-gray-600 mt-1">Manage your Language</p>
        </div>
    </div>
</x-filament-panels::page>
