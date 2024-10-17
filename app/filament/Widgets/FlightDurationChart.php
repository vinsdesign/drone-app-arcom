<?php

namespace App\Filament\Widgets;

use App\Models\fligh;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class FlightDurationChart extends ChartWidget
{
    protected static ?string $heading = 'Duration Flight Minute';
    protected static string $color = 'success';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $teams = Fligh::where('teams_id', $tenant_id)
        ->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])
        ->get();
        $data = $teams->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function ($group) {
            $totalSeconds = 0;
            foreach ($group as $flight) {
                if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $flight->duration)) {
                    list($hours, $minutes, $seconds) = explode(':', $flight->duration);

                    $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
                }
            }
            $totalMinutes = $totalSeconds / 60;

            return $totalMinutes;
        });
 
    return [
        'datasets' => [
            [
                'label' => 'Total Flight Duration (Minutes)',
                'data' => $data->values(),
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
