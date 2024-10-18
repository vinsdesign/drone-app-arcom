<?php

namespace App\Filament\Resources\MaintenanceBatteryResource\Pages;

use App\Filament\Resources\MaintenanceBatteryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMaintenanceBattery extends CreateRecord
{
    protected static string $resource = MaintenanceBatteryResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
