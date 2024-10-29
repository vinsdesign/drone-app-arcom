<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class FooterDasboard extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 3;

    protected static string $view = 'footer.footer';
}
