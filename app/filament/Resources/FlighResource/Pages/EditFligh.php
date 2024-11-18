<?php

namespace App\Filament\Resources\FlighResource\Pages;

use App\Filament\Resources\FlighResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Helpers\TranslationHelper;

class EditFligh extends EditRecord
{
    protected static string $resource = FlighResource::class;


    public function mount($record): void
    {
        parent::mount($record);
        if ($this->record->locked_flight === 'locked') {
            Notification::make()
            ->title(TranslationHelper::translateIfNeeded('Access Denied'))
            ->body(TranslationHelper::translateIfNeeded('This record is locked and cannot be edited.'))
            ->danger()
            ->send();

            $this->redirect(FlighResource::getUrl('index'));
        }
    }
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
