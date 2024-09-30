<?php

namespace App\Filament\Resources\BattreiResource\Pages;

use App\Filament\Resources\BattreiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBattreis extends ListRecords
{
    protected static string $resource = BattreiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
