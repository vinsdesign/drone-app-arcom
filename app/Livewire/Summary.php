<?php

namespace App\Livewire;
use Filament\Widgets\Widget;
class Summary extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;
    protected static string $view = "filament.tabWidget.summary";
}