<?php

namespace App\Filament\Resources\FlighLocationResource\Pages;

use App\Filament\Resources\FlighLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlighLocation extends EditRecord
{
    protected static string $resource = FlighLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
