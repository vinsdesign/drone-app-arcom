<?php

namespace App\Livewire;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh_location;
use Filament\Widgets\ChartWidget;

class TabInventoryLocation extends ChartWidget
{
    // protected static ?string $heading = 'Location Inventory';
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
    protected function getOptions(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        //drone
;
        $totalLocation = fligh_location::where('teams_id', $tenant_id)->count('name');

        return [
        'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => "$totalLocation Location",
                    'font' => [
                        'size' => 12,
                    ],
                ],
            ],
        ];
    }
}