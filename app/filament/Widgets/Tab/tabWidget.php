<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class tabWidget extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.tab-widget';
}
