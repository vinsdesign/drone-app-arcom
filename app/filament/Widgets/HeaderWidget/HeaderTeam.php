<?php

namespace App\Livewire\HeaderWidget;

use Filament\Widgets\Widget;

class HeaderTeam extends Widget
{
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.header-teams';
}