<?php

namespace App\Filament\Resources\KitsResource\Pages;

use App\Filament\Resources\KitsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKits extends EditRecord
{
    protected static string $resource = KitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
