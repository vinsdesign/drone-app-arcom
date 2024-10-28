<?php

namespace App\Livewire;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh_location;
use App\Models\maintence_drone;
use App\Models\maintence_eq;
use Filament\Widgets\ChartWidget;

class TabMaintenance extends ChartWidget
{
    // protected static ?string $heading = 'Drone Inventory';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $maintenanceDroneCounts = maintence_drone::where('teams_id', $tenant_id)
        ->select('status', \DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status');

        $maintenanceEqCounts = maintence_eq::where('teams_id', $tenant_id)
        ->select('status', \DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status');

        $maintenanceCounts = [];
        foreach (['schedule', 'in_progress', 'completed'] as $status) {
            $maintenanceCounts[$status] = ($maintenanceDroneCounts[$status] ?? 0) + ($maintenanceEqCounts[$status] ?? 0);
        }
        // dd($maintenanceCounts);
        $backgroundColors = [
            'rgba(255, 0, 0, 0.8)',
            'rgba(54, 162, 235, 0.9)',
            'rgba(75, 192, 192, 0.8)',
        ];

        $borderColors = [
            'rgba(255, 0, 0, 1)',  
            'rgba(54, 162, 235, 1)',    
            'rgba(75, 192, 192, 1)',     
        ];
        

        $labels = array_keys($maintenanceCounts);
        $data = array_values($maintenanceCounts);


        return [
            'datasets' => [
                [
                    'label' => 'Total Maintenance',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' =>$labels
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
        $maintenanceDroneTotal = maintence_drone::where('teams_id', $tenant_id)->count('name');
        $maintenanceEqTotal = maintence_eq::where('teams_id', $tenant_id)->count('name');
        $total = $maintenanceDroneTotal + $maintenanceEqTotal;

        return [
        'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => "$total Maintenance",
                    'font' => [
                        'size' => 16,
                    ],
                ],
            ],
        ];
    }
}