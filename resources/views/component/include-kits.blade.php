<?php
    $id = $getRecord()->id;
    $kits = App\Models\Kits::whereHas('battrei', function ($query) use ($id) {
    $query->where('battrei_id', $id);
})->get();

?>
@if($kits->count() > 0 )
    <div class="p-4">
        <div class="border-t border-gray-300 dark:border-gray-700 my-4"></div>
        <h2 class="text-l font-semibold text-gray-800 dark:text-gray-200 mb-4">
            Included in Kits
        </h2>
        <div class="space-y-2">
            @foreach($kits as $item)
                <a 
                    href="{{ route('filament.admin.resources.kits.edit', ['tenant' => Auth()->user()->teams()->first()->id, 'record' => $item->id]) }}" 
                    class="block p-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition"
                >
                    <p class="text-gray-800 dark:text-gray-200 font-medium">
                        {{$item->name}}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
@endif
