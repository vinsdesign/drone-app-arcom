<?php

namespace App\Filament\Resources\MaintenceResource\Pages;

use App\Filament\Resources\MaintenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintence extends EditRecord
{
    protected static string $resource = MaintenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
