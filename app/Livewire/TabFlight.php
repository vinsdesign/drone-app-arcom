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

class TabFlight extends Widget
{
    // protected static ?string $heading = 'Drone Inventory';
    protected static bool $isLazy = false;
    protected static string $view = 'component.chartjs.doughnut-tab-fligh-project';
    // protected function getData(): array
    // {
    //     $tenant_id = Auth()->User()->teams()->first()->id;
    //     $topProjects = Fligh::where('teams_id', $tenant_id)
    //                 ->selectRaw('projects_id, COUNT(id) as flight_count')
    //                 ->groupBy('projects_id')
    //                 ->orderByDesc('flight_count')
    //                 ->limit(3)
    //                 ->with('projects')
    //                 ->get();
        
    //     $otherTotalFlights = Fligh::where('teams_id', $tenant_id)
    //                 ->whereNotIn('projects_id', $topProjects->pluck('projects_id'))
    //                 ->count();
 
        
    //     $labels=[];
    //     $datas = [];
    //     foreach ($topProjects as $project) {
    //         $labels[] = $project->projects->case;
    //         $datas[] = $project->flight_count;
    //     };
    //     $data = [$datas[0], $datas[1], $datas[2],$otherTotalFlights];
    //     $label = [$labels[0],$labels[1],$labels[2],'Other'];
    //     $color = [
    //         '#FFA500',
    //         '#ADD8E6',
    //         '#6A5ACD',
    //         '#32CD32'   
    //     ];
   
        
    //     return [
    //         'datasets' => [
    //             [
    //                 'label' => 'Flight Per Project',
    //                 'data' => $data,
    //                 'backgroundColor' => $color,
    //                 'borderColor' => 'rgba(255, 99, 132, 1)',
    //                 'borderWidth' => 2,
    //             ],
    //         ],
    //         'labels' =>$label

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
    //     $totalProject = fligh::where('teams_id', $tenant_id)
    //     ->distinct('projects_id')
    //     ->count('projects_id');
    //     $totalFlight = fligh::where('teams_id', $tenant_id)->count('name');

    //     return [
    //     'plugins' => [
    //             'title' => [
    //                 'display' => true,
    //                 'text' => "Total $totalFlight Flights Across $totalProject Projects",
    //                 'font' => [
    //                     'size' => 16,
    //                 ],
    //             ],
    //         ],
    //     ];
    // }
}