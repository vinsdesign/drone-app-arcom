<?php

namespace App\Livewire;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh_location;
use Filament\Widgets\ChartWidget;

class TabInventoryBatteri extends ChartWidget
{
    // protected static ?string $heading = 'Batteri Inventory';
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
    protected function getOptions(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        //drone
;
        $totalBatteri = battrei::where('teams_id', $tenant_id)->count('name');

        return [
        'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => "$totalBatteri Battereis",
                    'font' => [
                        'size' => 12,
                    ],
                ],
            ],
        ];
    }
}