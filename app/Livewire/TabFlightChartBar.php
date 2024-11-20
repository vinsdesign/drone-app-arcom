<?php

namespace App\Livewire;

use App\Models\fligh;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\Drone;
use Filament\Widgets\Widget;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Route;
use Filament\Support\RawJs;
class TabFlightChartBar extends Widget
{
    protected static bool $isLazy = false;
    protected static string $view = "component.chartjs.flight-duration-total";
//     protected function getData(): array
//     {
//         $tenant_id = Auth()->user()->teams()->first()->id;
//         $flights = fligh::where('teams_id', $tenant_id)
//             ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
//             ->get();
    
//         $data = $flights->groupBy(function ($item) {
//             return Carbon::parse($item->start_date_flight)->format('Y-m');
//         })->map(function ($group) {
//             $totalFlights = $group->count();
//             $totalDuration = $group->sum(function ($flight) {
//                 list($hours, $minutes, $seconds) = explode(':', $flight->duration);
//                 return ($hours * 3600) + ($minutes * 60) + $seconds;
//             });
//             $totalHours = $totalDuration / 3600;
//             $formatTotalHour = round($totalHours,1);

//             return [
//                 'totalFlights' => $totalFlights,
//                 'totalDuration' => $formatTotalHour,
//             ];
//         });
    
//         return [
//             'datasets' => [
//                 [
//                     'label' => 'Flights Total ',
//                     'data' => $data->pluck('totalFlights')->values()->toArray(),
//                     'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
//                     'AxisID' => 'left-y-axis',
//                 ],
//                 [
//                     'label' => 'Flight Duration (Hours)',
//                     'data' => $data->pluck('totalDuration')->values()->toArray(),
//                     'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
//                     'AxisID' => 'right-y-axis',
//                 ],
//             ],
//             'labels' => $data->keys()->toArray(),
//         ];
//     }
//     // protected function getOptions():array
//     // {
//     //     $tenant_id = Auth()->user()->teams()->first()->id;
//     //     $flights = fligh::where('teams_id', $tenant_id)
//     //         ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
//     //         ->get();
//     //         $data = $flights->groupBy(function ($item) {
//     //             return Carbon::parse($item->start_date_flight)->format('Y-m'); // Grup per bulan
//     //         })->map(function ($group) {
//     //             $totalFlights = $group->count();
//     //             $totalDurationInSeconds = $group->sum(function ($flight) {
//     //                 list($hours, $minutes, $seconds) = explode(':', $flight->duration);
//     //                 return ($hours * 3600) + ($minutes * 60) + $seconds;
//     //             });
//     //             $totalHours = floor($totalDurationInSeconds / 3600);
//     //             $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
//     //             $totalSeconds = $totalDurationInSeconds % 60;
            
//     //             return [
//     //                 'totalFlights' => $totalFlights,
//     //                 'totalDuration' => sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds),
//     //             ];
//     //         });
            
//     //         $totalFlightsPerMonth = $data->pluck('totalFlights')->sum();
//     //         $totalDurationInSeconds = $flights->sum(function ($flight) {
//     //             list($hours, $minutes, $seconds) = explode(':', $flight->duration);
//     //             return ($hours * 3600) + ($minutes * 60) + $seconds;
//     //         });
            
//     //         $totalHours = floor($totalDurationInSeconds / 3600);
//     //         $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
//     //         $totalSeconds = $totalDurationInSeconds % 60;
            
//     //         // Format total durasi
//     //         $formattedTotalDuration = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);
//     //     return [
//     //         'plugins' => [
//     //             'title' => [
//     //                 'display' => true,
//     //                 'text' => 'Flights Total: ' . $totalFlightsPerMonth . ' | Duration Total: ' . $formattedTotalDuration,
//     //                 'font' => [
//     //                     'size' => 16,
//     //                 ],
//     //             ],
//     //         ],
//     //     ];
        
//     // }

//     protected function getOptions(): RawJs
//     {
//         $width = 1;
//         $height = 1000;
//         return RawJs::make(<<<JS
//             {
//                 responsive: true,
//                 maintainAspectRatio: false,
//                 width: {$width}, // Set width dynamically
//                 height: {$height}, // Set height dynamically
//                 scales: {
//                     y: [
//                         {
//                             id: 'left-y-axis', // Identifier for the left Y-axis
//                             position: 'left', // Position Y-axis on the left
//                             ticks: {
//                                 callback: (value) => '€' + value, // Format left Y-axis ticks with a currency symbol
//                             },
//                         },
//                         {
//                             id: 'right-y-axis', // Identifier for the right Y-axis
//                             position: 'right', // Position Y-axis on the right
//                             ticks: {
//                                 callback: (value) => value + ' hrs', // Format right Y-axis ticks with a unit (e.g., hours)
//                             },
//                         },
//                     ],
//                     x: {
//                         position: 'bottom', // Position X-axis at the bottom
//                     },
//                 },
//             }
//         JS);
//     }
//     public function getDescription(): ?string
// {
//     return 'Total flights and monthly duration';
// }
//     protected function getType(): string
//     {
//         return 'bar';
//     }
}
