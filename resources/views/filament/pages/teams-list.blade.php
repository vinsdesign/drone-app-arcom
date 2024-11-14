@php
use App\Helpers\TranslationHelper;
@endphp
<x-filament-panels::page>
    <div class="mb-4 p-4 border border-gray-300 rounded-lg bg-white shadow-sm w-1/2 mx-auto text-center dark:bg-gray-800">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{!! TranslationHelper::translateIfNeeded('Total PT') !!}</h2> 
        <span class="text-2xl font-bold dark:text-white">{{ $this->getTotalTeams() }}</span> 
    </div>        
    {{ $this->table }}
</x-filament-panels::page>
