<?php

namespace App\Filament\Resources\DroneResource\Pages;

use App\Filament\Resources\DroneResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDrone extends ViewRecord
{
    protected static string $resource = DroneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
