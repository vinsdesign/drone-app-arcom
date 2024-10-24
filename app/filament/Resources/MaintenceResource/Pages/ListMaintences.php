<?php

namespace App\Filament\Resources\MaintenceResource\Pages;

use App\Filament\Resources\MaintenceResource;
use App\Livewire\HeaderWidget\HeaderMaintenanceDrone;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintences extends ListRecords
{
    protected static string $resource = MaintenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array{
        return [
            HeaderMaintenanceDrone::class,
        ];
    }
}
