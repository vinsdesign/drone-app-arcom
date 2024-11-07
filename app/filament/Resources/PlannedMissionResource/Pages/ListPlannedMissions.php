<?php

namespace App\Filament\Resources\PlannedMissionResource\Pages;

use App\Filament\Resources\PlannedMissionResource;
use App\Livewire\HeaderWidget\HeaderPlannedMission;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlannedMissions extends ListRecords
{
    protected static string $resource = PlannedMissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return[
            HeaderPlannedMission::class,
        ];
    }
}
