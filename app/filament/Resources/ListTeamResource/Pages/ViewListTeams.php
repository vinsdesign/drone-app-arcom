<?php 
namespace App\Filament\Resources\ListTeamResource\Pages;

use App\Filament\Resources\ListTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Drone;

class ViewListTeams extends ViewRecord
{
    
    protected static string $resource = ListTeamResource::class;
    

    // public function mount(int|string $record): void
    // {
    //     // Panggil parent mount
    //     parent::mount($record);

    //     // Emit event dengan ID drone
    //     $this->dispatch('droneIdReceived', $record);
    // }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
