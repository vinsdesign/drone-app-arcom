<?php

namespace App\Filament\Widgets;
use App\Models\fligh;
use App\Models\drone;
use App\Models\project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AStatsOverview extends BaseWidget
{
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected function getStats(): array
    {
        $tenant_id = Auth()->user()->teams()->first()->id;
        $FlightsCount = fligh::where('teams_id', $tenant_id)->count('name');
        $totalProject = project::where('teams_id', $tenant_id)->count('case');
        $totalDrone = drone::where('teams_id', $tenant_id)->count('name');
        $totalDuration = fligh::where('teams_id', $tenant_id)->sum('duration_hour');
        $totalDurationM = fligh::where('teams_id', $tenant_id)->sum('duration_minute');
        $totalDurasi = $totalDuration*60+$totalDurationM;
        return [
            Stat::make('Total Flight', $FlightsCount),
            Stat::make('Total Project', $totalProject),
            Stat::make('Total Drone', $totalDrone),
            Stat::make('Total Durasi', $totalDurasi),
        ];
    }
}
