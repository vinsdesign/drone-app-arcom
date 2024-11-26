<?php
namespace App\Filament\Resources\FlighLocationResource\Pages;

use App\Filament\Resources\FlighLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Livewire\LocationStatistik;

class ViewFlighLocation extends ViewRecord
{
    protected static string $resource = FlighLocationResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            LocationStatistik::class
        ];
    }
}
