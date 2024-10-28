<?php

namespace App\Livewire;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh_location;
use Filament\Widgets\ChartWidget;

class TabInventoryDrone extends ChartWidget
{
    protected static ?string $heading = 'Drone Inventory';
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
}

class TabInventoryBatteri extends ChartWidget
{
    protected static ?string $heading = 'Batteri Inventory';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $dataBatteri = battrei::where('teams_id', $tenant_id)->count('name');

        return [
            'datasets' => [
                [
                    'label' => 'Batteries',
                    'data' => [$dataBatteri],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Batteries']
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

class TabInventoryEquidment extends ChartWidget
{
    protected static ?string $heading = 'Equipment Inventory';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $dataEqupment = equidment::where('teams_id', $tenant_id)->count('name');

        return [
            'datasets' => [
                [
                    'label' => 'Equipment',
                    'data' => [$dataEqupment],
                    'backgroundColor' => 'rgba(255, 206, 86, 0.6)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Equipment']
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

class TabInventoryLocation extends ChartWidget
{
    protected static ?string $heading = 'Location Inventory';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $datalocation = fligh_location::where('teams_id', $tenant_id)->count('name');

        return [
            'datasets' => [
                [
                    'label' => 'Locations',
                    'data' => [$datalocation],
                    'backgroundColor' => 'rgba(153, 102, 255, 0.6)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Locations']
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
