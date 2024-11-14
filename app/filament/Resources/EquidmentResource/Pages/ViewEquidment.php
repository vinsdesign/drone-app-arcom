<?php

namespace App\Filament\Resources\EquidmentResource\Pages;

use App\Filament\Resources\EquidmentResource;
use App\Livewire\EquipmentStatistik;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEquidment extends ViewRecord
{
    protected static string $resource = EquidmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            EquipmentStatistik::class
        ];
    }
}
