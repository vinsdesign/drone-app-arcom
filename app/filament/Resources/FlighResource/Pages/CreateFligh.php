<?php

namespace App\Filament\Resources\FlighResource\Pages;

use App\Filament\Resources\FlighResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFligh extends CreateRecord
{
    protected static string $resource = FlighResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
