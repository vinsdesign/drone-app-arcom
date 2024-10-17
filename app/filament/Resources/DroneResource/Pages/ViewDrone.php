<?php

namespace App\Filament\Resources\DroneResource\Pages;

use App\Filament\Resources\DroneResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Widgets\FlightChart;
class ViewDrone extends ViewRecord
{
    protected static string $resource = DroneResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            FlightChart::class,
        ];
    }
}
