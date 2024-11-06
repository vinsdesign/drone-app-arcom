<?php

namespace App\Filament\Resources\PlannedMissionResource\Pages;

use App\Filament\Resources\PlannedMissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlannedMissions extends ListRecords
{
    protected static string $resource = PlannedMissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
