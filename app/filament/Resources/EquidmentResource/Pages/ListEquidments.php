<?php

namespace App\Filament\Resources\EquidmentResource\Pages;

use App\Filament\Resources\EquidmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Livewire\HeaderWidget\HeaderEquipment;

class ListEquidments extends ListRecords
{
    protected static string $resource = EquidmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            HeaderEquipment::class
        ];
    }
}
