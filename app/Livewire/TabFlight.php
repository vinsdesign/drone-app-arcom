<?php

namespace App\Livewire;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh;
use App\Models\fligh_location;
use App\Models\project;
use Filament\Widgets\ChartWidget;

class TabFlight extends ChartWidget
{
    // protected static ?string $heading = 'Drone Inventory';
    protected static bool $isLazy = false;
    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $flightProject = fligh::where('teams_id', $tenant_id)
        ->selectRaw('projects_id, COUNT(id) as flight_count')
        ->groupBy('projects_id')
        ->with('projects')
        ->get();

        $label=[];
        $data = [];
        $color = [];
        foreach ($flightProject as $project) {
            $label[] = $project->projects->case;
            $data[] = $project->flight_count;
            $color[] = '#' . substr(md5(uniqid(rand(), true)), 0, 6);
        };
        return [
            'datasets' => [
                [
                    'label' => 'Flight Per Project',
                    'data' => $data,
                    'backgroundColor' => $color,
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' =>$label

        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    protected function getOptions(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        //drone
        $totalProject = fligh::where('teams_id', $tenant_id)
        ->distinct('projects_id')
        ->count('projects_id');
        $totalFlight = fligh::where('teams_id', $tenant_id)->count('name');

        return [
        'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => "Total $totalFlight Flights Across $totalProject Projects",
                    'font' => [
                        'size' => 16,
                    ],
                ],
            ],
        ];
    }
}