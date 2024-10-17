<?php

namespace App\Filament\Resources\DroneResource\Pages;

use App\Filament\Resources\DroneResource;
use Filament\Actions;
use App\Filament\Widgets\FlightChart;

class WidgetDrone extends FlightChart
{
    protected static string $resource = DroneResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            FlightChart::class,
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
