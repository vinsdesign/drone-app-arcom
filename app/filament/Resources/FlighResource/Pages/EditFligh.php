<?php

namespace App\Filament\Resources\FlighResource\Pages;

use App\Filament\Resources\FlighResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFligh extends EditRecord
{
    protected static string $resource = FlighResource::class;

    // public function mount($record): void
    // {
    //     parent::mount($record);
    //     if ($this->record->locked_flight) {
    //         $this->redirect(FlighResource::getUrl('index'));
    //     }
    // }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
