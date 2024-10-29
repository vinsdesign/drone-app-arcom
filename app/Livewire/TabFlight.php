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
    // protected static string $view = 'filament.widgets.tab-flight';

    // public function render(): \Illuminate\Contracts\View\View
    // {
    //     return view(static::$view, [
    //         'datasets' => $this->getData()['datasets'],
    //         'labels' => $this->getData()['labels'],
    //         'chartType' => $this->getType(),
    //     ]);
    // }

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
        //drone
        // $flightsDrone = fligh::where('teams_id', $tenant_id)
        // ->selectRaw('drones_id, COUNT(id) as flight_count')
        // ->groupBy('drones_id')
        // ->with('drones') // Pastikan ada relasi drone di model flight
        // ->get();

        // $droneLabels = [];
        // $droneData = [];
        // $droneColors = [];

        // foreach ($flightsDrone as $flight) {
        //     $droneLabels[] = $flight->drones->name;
        //     $droneData[] = $flight->flight_count;
        //     $droneColors[] = '#' . substr(md5(uniqid(rand(), true)), 0, 6);
        // }
        //end
        return [
            'datasets' => [
                [
                    'label' => 'Flight Per Project',
                    'data' => $data,
                    'backgroundColor' => $color,
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                ],
                // [
                //     'label' => 'Flight Per Drone',
                //     'data' => [$droneData],
                //     'backgroundColor' => $droneColors,
                //     'borderColor' => 'rgba(255, 99, 132, 1)',
                //     'borderWidth' => 2,
                // ],
            ],
            'labels' => $label
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
                    'text' => "$totalProject Project \f $totalFlight Flight",
                    'font' => [
                        'size' => 16,
                    ],
                ],
            ],
        ];
    }
}