<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ImporterResource;
use App\Livewire\HeaderWidget\HeaderImport;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;


class ImporterPage extends Page 
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $resource = ImporterResource::class;
    protected static string $view = 'filament.pages.dji-importer';
    

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
                HeaderImport::class,
            ];
        }
        public function getBreadcrumb(): ?string
        {
            return null;
        }
}
