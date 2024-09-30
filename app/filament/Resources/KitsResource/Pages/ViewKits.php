<?php

namespace App\Filament\Resources\KitsResource\Pages;

use App\Filament\Resources\KitsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKits extends ViewRecord
{
    protected static string $resource = KitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
