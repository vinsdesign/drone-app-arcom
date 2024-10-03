<?php

namespace App\Filament\Widgets;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh_location;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEq = equidment::count('name');
        $totalBattery = battrei::count('name');
        $totalDrone = drone::count('name');
        $totallocation = fligh_location::count('name');
        return [
            Stat::make('Total Drone', $totalDrone),
            Stat::make('Total Batteries', $totalBattery),
            Stat::make('Total Equipment', $totalEq),
            Stat::make('Total Locations', $totallocation),
        ];
    }
}
