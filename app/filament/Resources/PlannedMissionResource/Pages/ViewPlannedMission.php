<?php
namespace App\Filament\Resources\PlannedMissionResource\Pages;

use App\Filament\Resources\PlannedMissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlannedMission extends ViewRecord
{
    protected static string $resource = PlannedMissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
