<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BatteryChargerResource;
use App\Livewire\HeaderWidget\HeaderImport;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;


class Charger extends Page 
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $resource = BatteryChargerResource::class;
    protected static string $view = 'filament.pages.battery-charger';
    

    public function mount()
        {
            if (session()->has('success')) {
                Notification::make()
                    ->title(session('success'))
                    ->success()
                    ->send();
            }
        }
        // protected function getHeaderWidgets(): array{
        //     return[
        //         HeaderImport::class,
        //     ];
        // }
        public function getBreadcrumb(): ?string
        {
            return null;
        }
}
