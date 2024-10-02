<?php

namespace App\Filament\Resources\MaintenanceBatteryResource\Pages;

use App\Filament\Resources\MaintenanceBatteryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceBattery extends EditRecord
{
    protected static string $resource = MaintenanceBatteryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
