<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class InventoryOverview extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 5;
    protected static string $view = 'filament.widgets.inventory-overview';
}
