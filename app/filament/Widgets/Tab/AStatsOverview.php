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
    protected static bool $isLazy = false;
    protected static ?int $sort = 3;
    protected function getStats(): array
    {
        $tenant_id = Auth()->user()->teams()->first()->id;
        $FlightsCount = fligh::where('teams_id', $tenant_id)->count('name');
        $totalProject = project::where('teams_id', $tenant_id)->count('case');
        $totalDrone = drone::where('teams_id', $tenant_id)->count('name');
        $flights = fligh::where('teams_id', $tenant_id)->get();
         $totalDurationInSeconds = $flights->sum(function ($flight) {
                list($hours, $minutes, $seconds) = explode(':', $flight->duration);
                return ($hours * 3600) + ($minutes * 60) + $seconds;
            });
            
            $totalHours = floor($totalDurationInSeconds / 3600);
            $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
            $totalSeconds = $totalDurationInSeconds % 60;
            
            // Format total durasi
            $formattedTotalDuration = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);
        return [
            Stat::make('Total Flight', $FlightsCount),
            Stat::make('Total Project', $totalProject),
            Stat::make('Total Drone', $totalDrone),
            Stat::make('Total Flight Duration', $formattedTotalDuration),
        ];
    }
}
