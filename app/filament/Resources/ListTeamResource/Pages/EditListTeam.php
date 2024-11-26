<?php

namespace App\Filament\Resources\ListTeamResource\Pages;

use App\Filament\Resources\ListTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListTeam extends EditRecord
{
    protected static string $resource = ListTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
