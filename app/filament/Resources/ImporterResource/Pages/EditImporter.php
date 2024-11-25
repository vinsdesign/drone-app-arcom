<?php

namespace App\Filament\Resources\ImporterResource\Pages;

use App\Filament\Resources\ImporterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImporter extends EditRecord
{
    protected static string $resource = ImporterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
