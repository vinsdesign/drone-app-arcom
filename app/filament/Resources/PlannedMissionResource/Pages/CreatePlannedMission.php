<?php

namespace App\Filament\Resources\PlannedMissionResource\Pages;

use App\Filament\Resources\PlannedMissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePlannedMission extends CreateRecord
{
    protected static string $resource = PlannedMissionResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}