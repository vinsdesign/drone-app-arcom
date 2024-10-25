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
    protected static bool $isLazy = false;
    protected static ?int $sort = 6;
    protected function getStats(): array
    {
        $tenant_id = Auth()->user()->teams()->first()->id;
        $totalEq = equidment::where('teams_id', $tenant_id)->count('name');
        $totalBattery = battrei::where('teams_id', $tenant_id)->count('name');
        $totalDrone = drone::where('teams_id', $tenant_id)->count('name');
        $totallocation = fligh_location::where('teams_id', $tenant_id)->count('name');
        return [
            Stat::make('Total Drone', $totalDrone),
            Stat::make('Total Batteries', $totalBattery),
            Stat::make('Total Equipment', $totalEq),
            Stat::make('Total Locations', $totallocation),
        ];
    }
}
