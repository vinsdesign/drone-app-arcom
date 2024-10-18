<?php

namespace App\Filament\Resources\DroneResource\Pages;

use App\Filament\Resources\DroneResource;
use App\Livewire\DroneStatistik;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\drone;
class ViewDrone extends ViewRecord
{
    protected static string $resource = DroneResource::class;
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
