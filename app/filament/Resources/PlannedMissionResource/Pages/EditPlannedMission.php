<?php

namespace App\Filament\Resources\PlannedMissionResource\Pages;

use App\Filament\Resources\PlannedMissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlannedMission extends EditRecord
{
    protected static string $resource = PlannedMissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
