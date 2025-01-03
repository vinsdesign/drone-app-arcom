<?php

namespace App\Filament\Resources\FlighLocationResource\Pages;

use App\Filament\Resources\FlighLocationResource;
use App\Livewire\HeaderWidget\HeaderLocation;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlighLocations extends ListRecords
{
    protected static string $resource = FlighLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return[
            HeaderLocation::class,
        ];
    }
}
