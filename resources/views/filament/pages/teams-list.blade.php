<x-filament-panels::page>
    <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-white shadow-sm w-1/2 mx-auto text-center">
        <h2 class="text-xl font-semibold text-gray-800">Total PT</h2> <!-- Increased text size -->
        <span class="text-2xl font-bold">{{ $this->getTotalTeams() }}</span> <!-- Increased text size -->
    </div>        
    {{ $this->table }}
</x-filament-panels::page>
