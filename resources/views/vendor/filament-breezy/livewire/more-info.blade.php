@php
    use App\Helpers\TranslationHelper;
    $translatedTitle = TranslationHelper::translateIfNeeded('More Information'); 
    $translatedDescription = TranslationHelper::translateIfNeeded('Manage your More Information'); 
@endphp

<x-filament-breezy::grid-section title="{{ $translatedTitle }}" description="{{ $translatedDescription }}">
    <x-filament::card>
        <form wire:submit.prevent="submit" class="space-y-6">
 
            {{ $this->form }}
 
            <div class="text-right">
                <x-filament::button type="submit" form="submit" class="align-right">
                    {!! TranslationHelper::translateIfNeeded('Update')!!}
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</x-filament-breezy::grid-section>

