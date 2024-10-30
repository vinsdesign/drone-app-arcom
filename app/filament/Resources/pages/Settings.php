<?php

namespace App\Filament\Pages;

use App\Filament\Resources\SettingsResource;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;


class Settings extends Page 
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $resource = SettingsResource::class;
    protected static string $view = 'filament.pages.settings';

    public function mount()
{
    if (session()->has('success')) {
        Notification::make()
            ->title(session('success'))
            ->success()
            ->send();
    }
}
}
