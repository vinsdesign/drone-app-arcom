<?php

namespace App\Filament\Resources\BattreiResource\Pages;

use App\Filament\Resources\BattreiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Livewire\BatteryStatistik;

class ViewBattrei extends ViewRecord
{
    protected static string $resource = BattreiResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            BatteryStatistik::class
        ];
    }
}
