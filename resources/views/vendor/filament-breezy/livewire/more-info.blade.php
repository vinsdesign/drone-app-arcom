<x-filament-breezy::grid-section title="More Information" description="Manage your More Information">
    <x-filament::card>
        <form wire:submit.prevent="submit" class="space-y-6">
 
            {{ $this->form }}
 
            <div class="text-right">
                <x-filament::button type="submit" form="submit" class="align-right">
                    Update
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</x-filament-breezy::grid-section>

