<?php

namespace App\Filament\Resources\KitsResource\Pages;

use App\Filament\Resources\KitsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKits extends ListRecords
{
    protected static string $resource = KitsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
