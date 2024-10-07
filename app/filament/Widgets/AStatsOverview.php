<?php

namespace App\Filament\Widgets;
use App\Models\fligh;
use App\Models\drone;
use App\Models\project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $FlightsCount = fligh::count('name');
        $totalProject = project::count('case');
        $totalDrone = drone::count('name');
        $totalDuration = fligh::sum('duration_hour');
        $totalDurationM = fligh::sum('duration_minute');
        $totalDurasi = $totalDuration*60+$totalDurationM;
        return [
            Stat::make('Total Flight', $FlightsCount),
            Stat::make('Total Project', $totalProject),
            Stat::make('Total Drone', $totalDrone),
            Stat::make('Total Durasi', $totalDurasi),
        ];
    }
}
