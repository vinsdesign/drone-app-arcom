<?php

namespace App\Filament\Resources\BattreiResource\Pages;

use App\Filament\Resources\BattreiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBattrei extends EditRecord
{
    protected static string $resource = BattreiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
