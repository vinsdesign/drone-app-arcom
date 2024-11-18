<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Helpers\TranslationHelper;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    public function mount($record): void
    {
        parent::mount($record);
        if ($this->record->locked === 'locked') {
            Notification::make()
            ->title(TranslationHelper::translateIfNeeded('Access Denied'))
            ->body(TranslationHelper::translateIfNeeded('This record is locked and cannot be edited.'))
            ->danger()
            ->send();

            $this->redirect(DocumentResource::getUrl('index'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
