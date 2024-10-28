<?php

namespace App\Livewire;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh_location;
use Filament\Widgets\ChartWidget;

class TabInventoryEquidment extends ChartWidget
{
    // protected static ?string $heading = 'Equipment Inventory';
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
    protected function getOptions(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        //drone
;
        $totalEquipment = equidment::where('teams_id', $tenant_id)->count('name');

        return [
        'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => "$totalEquipment Equipment",
                    'font' => [
                        'size' => 12,
                    ],
                ],
            ],
        ];
    }
}