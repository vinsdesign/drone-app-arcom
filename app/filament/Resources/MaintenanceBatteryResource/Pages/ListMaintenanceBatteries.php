<?php

namespace App\Filament\Resources\MaintenanceBatteryResource\Pages;

use App\Filament\Resources\MaintenanceBatteryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceBatteries extends ListRecords
{
    protected static string $resource = MaintenanceBatteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
