<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AFlight extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.text-widget';
}
