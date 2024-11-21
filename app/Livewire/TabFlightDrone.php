<?php

namespace App\Livewire;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh;
use App\Models\fligh_location;
use App\Models\project;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Widget;

class TabFlightDrone extends Widget
{
    // protected static ?string $heading = 'Drone Inventory';
    protected static bool $isLazy = false;
    protected static string $view = 'component.chartjs.doughnut-tab-fligh-drone';

    // protected function getData(): array
    // {
    //     $tenant_id = Auth()->User()->teams()->first()->id;
    //     //drone
    //     $flightsDrone = fligh::where('teams_id', $tenant_id)
    //     ->selectRaw('drones_id, COUNT(id) as flight_count')
    //     ->groupBy('drones_id')
    //     ->with('drones') // Pastikan ada relasi drone di model flight
    //     ->get();

    //     $droneLabels = [];
    //     $droneData = [];
    //     $droneColors = [];

    //     foreach ($flightsDrone as $flight) {
    //         $droneLabels[] = $flight->drones->name;
    //         $droneData[] = $flight->flight_count;
    //         $droneColors[] = '#' . substr(md5(uniqid(rand(), true)), 0, 6);
    //     }
    //     //end
    //     return [
    //         'datasets' => [
    //             [
    //                 'label' => 'Flight Per Drone',
    //                 'data' => $droneData,
    //                 'backgroundColor' => $droneColors,
    //                 'borderColor' => 'rgba(255, 99, 132, 1)',
    //                 'borderWidth' => 2,
    //             ],
    //         ],
    //         'labels' => $droneLabels,
    //     ];
        
    // }

    // protected function getType(): string
    // {
    //     return 'doughnut';
    // }
    // protected function getOptions(): array
    // {
    //     $tenant_id = Auth()->User()->teams()->first()->id;
    //     //drone
    //     $totalDrones = fligh::where('teams_id', $tenant_id)
    //     ->distinct('drones_id')
    //     ->count('drones_id');
    //     $totalFlight = fligh::where('teams_id', $tenant_id)->count('name');

    //     return [
    //     'plugins' => [
    //             'title' => [
    //                 'display' => true,
    //                 'text' => "Total $totalFlight Flights with $totalDrones Drones.",
    //                 'font' => [
    //                     'size' => 16,
    //                 ],
    //             ],
    //         ],
    //     ];
    // }
}