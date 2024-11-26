<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ManualImportResource;
use App\Filament\Resources\SettingsResource;
use App\Livewire\HeaderWidget\HeaderManualImport;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;


class ManualImport extends Page 
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $resource = ManualImportResource::class;
    protected static string $view = 'filament.pages.manual-import';
    
    public function mount()
        {
            if (session()->has('success')) {
                Notification::make()
                    ->title(session('success'))
                    ->success()
                    ->send();
            }
        }
        protected function getHeaderWidgets(): array{
            return[
                HeaderManualImport::class,
            ];
        }
        public function getBreadcrumb(): ?string
        {
            return null;
        }
}
