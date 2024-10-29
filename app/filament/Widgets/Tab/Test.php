<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class Test extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;
protected static string $view = "filament.tabWidget.format-tabel";

}