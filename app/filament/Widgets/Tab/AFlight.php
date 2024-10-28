<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AFlight extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 3;

    protected static string $view = 'filament.widgets.text-widget';
}
