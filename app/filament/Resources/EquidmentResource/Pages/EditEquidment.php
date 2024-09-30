<?php

namespace App\Filament\Resources\EquidmentResource\Pages;

use App\Filament\Resources\EquidmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEquidment extends EditRecord
{
    protected static string $resource = EquidmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
