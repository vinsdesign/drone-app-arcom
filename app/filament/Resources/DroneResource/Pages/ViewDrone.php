<?php 
namespace App\Filament\Resources\DroneResource\Pages;

use App\Filament\Resources\DroneResource;
use App\Livewire\DroneStatistik;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Drone;

class ViewDrone extends ViewRecord
{
    
    protected static string $resource = DroneResource::class;
    

    // public function mount(int|string $record): void
    // {
    //     // Panggil parent mount
    //     parent::mount($record);

    //     // Emit event dengan ID drone
    //     $this->dispatch('droneIdReceived', $record);
    // }
    protected function getHeaderWidgets(): array
    {
        return [
            DroneStatistik::class
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
