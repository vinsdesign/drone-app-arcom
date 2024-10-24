<?php

namespace App\Filament\Resources\FlighResource\Pages;

use App\Filament\Resources\FlighResource;
use App\Livewire\HeaderWidget\HeaderFlight;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlighs extends ListRecords
{
    protected static string $resource = FlighResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return[
            HeaderFlight::class,
        ];
    }
}
