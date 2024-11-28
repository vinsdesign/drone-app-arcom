<?php

namespace App\Filament\Pages;

use App\Filament\Resources\SettingsResource;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use App\Helpers\TranslationHelper;


class Settings extends Page 
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $resource = SettingsResource::class;
    protected static string $view = 'filament.pages.settings';
    public function getBreadcrumb(): ?string
    {
        return null;
    }

    public function getHeading(): string
    {
        return TranslationHelper::translateIfNeeded('Settings');
        // return GoogleTranslate::trans('Settings', session('locale') ?? 'en');
    }
    public function getTitle(): string
    {
        return TranslationHelper::translateIfNeeded('Settings');
        // return GoogleTranslate::trans('Settings', session('locale') ?? 'en');
    }

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
