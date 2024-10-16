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
        $flights = fligh::where('teams_id', $tenant_id)->get();
        $totalSeconds = 0;
        foreach ($flights as $flight) {
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $flight->duration)) {
                list($hours, $minutes, $seconds) = explode(':', $flight->duration);
                $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
            }
        }
        $totalDuration = \Carbon\CarbonInterval::seconds($totalSeconds)->cascade()->format('%H:%I:%S');
        return [
            Stat::make('Total Flight', $FlightsCount),
            Stat::make('Total Project', $totalProject),
            Stat::make('Total Drone', $totalDrone),
            Stat::make('Total Flight Duration', $totalDuration),
        ];
    }
}
