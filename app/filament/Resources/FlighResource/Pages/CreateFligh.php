<?php

namespace App\Filament\Resources\FlighResource\Pages;

use App\Filament\Resources\FlighResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateFligh extends CreateRecord
{
    protected static string $resource = FlighResource::class;

    protected function getHeaderActions(): array{
        if (session()->has('notification')) {
            Notification::make()
                ->title('Successfully')
                ->body(session('notification'))
                ->success()
                ->send();
        
            session()->forget('notification');
        }

        if (session()->has('notificationError')) {
            Notification::make()
                ->title('can\'t create')
                ->body(session('notificationError'))
                ->danger()
                ->send();

            // Hapus session setelah digunakan
            session()->forget('notificationError');
        }
        return [
        
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
