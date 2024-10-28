<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

;

class TabWidgetOverview extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;
protected static string $view = "filament.tabWidget.tab-widget-overview";
// public function getDisplayName(): string {
//     return "Custom name TAB NAMe";
// }
}