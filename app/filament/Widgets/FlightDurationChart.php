<?php

namespace App\Filament\Widgets;

use App\Models\fligh;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class FlightDurationChart extends ChartWidget
{
    protected static ?string $heading = 'Duration Flight Hours';
    protected static string $color = 'success';
    protected static bool $isLazy = false;
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $teams = fligh::where('teams_id', $tenant_id)
        ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
        ->get();
        $data = $teams->groupBy(function ($item) {
            return Carbon::parse($item->start_date_flight)->format('Y-m-d');
        })->map(function ($group) {
            $totalSeconds = 0;
            foreach ($group as $flight) {
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $flight->duration)) {
                    list($hours, $minutes, $seconds) = explode(':', $flight->duration);

                    $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
                }
            }
            $totalMinutes = $totalSeconds / 60;
            $totalHours = $totalMinutes / 60;
            $formatTotalHour = round($totalHours,1);
            

            return $formatTotalHour;
        });
 
    return [
        'datasets' => [
            [
                'label' => 'Total Flight Duration (Hours)',
                'data' => $data->values(),
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 2,
                'fill' => false, // untuk line chart
                'tension' => 0.4, // membuat garis lebih halus
            ],
        ],
        'labels' => $data->keys(),
    ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
