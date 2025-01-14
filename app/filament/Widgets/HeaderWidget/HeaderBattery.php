<?php

namespace App\Filament\Widgets\HeaderWidget;

use Filament\Widgets\Widget;
use Filament\Facades\Filament;

class HeaderBattery extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.header-battery';

    public static function canView(): bool
    {
        if (request()->routeIs('filament.admin.pages.dashboard')) {
            return false;
        }
        return true;
    }
}