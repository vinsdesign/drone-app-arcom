<?php

namespace App\Livewire;

use App\Models\fligh;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\Drone;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Route;
class DroneStatistik extends ChartWidget
{
    protected static bool $isLazy = false;
    protected static ?string $heading = 'Drone Activity Statistics';
    protected int|string|array $columnSpan = 'full';
    public $Drone_id;
    public $Drone;
    protected $listeners = ['showDroneStatistik'];

    public function showDroneStatistik($id)
    {
    // dd($this->Drone = $id);
    $this->Drone_id = $id;
    $this->Drone = Drone::find($id);
    session(['drone_id' => $id]);
    // $this->emitSelf('dataUpdated');
    if (request()->routeIs('filament.admin.resources.drones.view')) {
        return; 
    }return redirect()->route('filament.admin.resources.drones.view', [
        'tenant' => Auth()->user()->teams()->first()->id,
        'record' => $id
    ]);

}
    //langsung ke load tidak berhenti sehingga menghasilkan null saat mengirim id ke sini, solusi alernative buatblade jangan di panggil dulu di action gunakan action lain viewdrone disana yang bermasalaha
    /// render
    protected function getData(): array
    {
        //aman boys, gunakan session untuk mendapat menyimpan id Drone
        $droneId = session('drone_id');
        // dd($droneId);
        $tenant_id = Auth()->user()->teams()->first()->id;
        $flights = fligh::where('teams_id', $tenant_id)->where('drones_id', $droneId)
            ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
            ->get();
    
        $data = $flights->groupBy(function ($item) {
            return Carbon::parse($item->date_flight)->format('Y-m-d');
        })->map(function ($group) {
            $totalFlights = $group->count();
            $totalDuration = $group->sum(function ($flight) {
                list($hours, $minutes, $seconds) = explode(':', $flight->duration);
                return ($hours * 3600) + ($minutes * 60) + $seconds;
            });
            $totalHours = $totalDuration / 3600;
            $formatTotalHour = round($totalHours,1);

            return [
                'totalFlights' => $totalFlights,
                'totalDuration' => $formatTotalHour,
            ];
        });
    
        return [
            'datasets' => [
                [
                    'label' => 'Flights Total ',
                    'data' => $data->pluck('totalFlights')->values()->toArray(),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'yAxisID' => 'totalFlights',
                ],
                [
                    'label' => 'Flight Duration (Hours)',
                    'data' => $data->pluck('totalDuration')->values()->toArray(),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
                    'yAxisID' => 'flightDuration',
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }
    protected function getOptions(): array
    {
        $droneId = session('drone_id');
        $tenant_id = Auth()->user()->teams()->first()->id;
        $flights = fligh::where('teams_id', $tenant_id)->where('drones_id', $droneId)
            ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
            ->get();
            $data = $flights->groupBy(function ($item) {
                return Carbon::parse($item->date_flight)->format('Y-m'); // Grup per bulan
            })->map(function ($group) {
                $totalFlights = $group->count();
                $totalDurationInSeconds = $group->sum(function ($flight) {
                    list($hours, $minutes, $seconds) = explode(':', $flight->duration);
                    return ($hours * 3600) + ($minutes * 60) + $seconds;
                });
                $totalHours = floor($totalDurationInSeconds / 3600);
                $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
                $totalSeconds = $totalDurationInSeconds % 60;
            
                return [
                    'totalFlights' => $totalFlights,
                    'totalDuration' => sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds),
                ];
            });
            
            $totalFlightsPerMonth = $data->pluck('totalFlights')->sum();
            $totalDurationInSeconds = $flights->sum(function ($flight) {
                list($hours, $minutes, $seconds) = explode(':', $flight->duration);
                return ($hours * 3600) + ($minutes * 60) + $seconds;
            });
            
            $totalHours = floor($totalDurationInSeconds / 3600);
            $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
            $totalSeconds = $totalDurationInSeconds % 60;
            
            // Format total durasi
            $formattedTotalDuration = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);
        return [
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Flights Total: ' . $totalFlightsPerMonth . ' | Duration Total: ' . $formattedTotalDuration,
                    'font' => [
                        'size' => 16,
                    ],
                ],
            ],
        ];
        
    }
    public function getDescription(): ?string
{
    return 'Total flights and monthly duration';
}
    protected function getType(): string
    {
        return 'bar';
    }
}
