<?php

namespace App\Livewire;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh_location;
use Filament\Widgets\ChartWidget;

class TabInventoryDrone extends ChartWidget
{
    // protected static ?string $heading = 'Drone Inventory';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $dataDrone = drone::where('teams_id', $tenant_id)->count('name');

        return [
            'datasets' => [
                [
                    'label' => 'Drones',
                    'data' => [$dataDrone],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Drones']
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
;
        $totalDrone = drone::where('teams_id', $tenant_id)->count('name');

        return [
        'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => "$totalDrone Drone",
                    'font' => [
                        'size' => 12,
                    ],
                ],
            ],
        ];
    }
}