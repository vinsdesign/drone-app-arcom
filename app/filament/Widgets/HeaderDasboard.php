<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class HeaderDasboard extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 1;

    protected static string $view = 'filament.widgets.header-dasboard';
}
