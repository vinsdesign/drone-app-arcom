<?php

namespace App\Filament\Widgets;

use App\Models\fligh;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class FlightDurationChart extends ChartWidget
{
    protected static ?string $heading = 'Duration Minute';
    protected static string $color = 'success';

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $teams = Fligh::where('teams_id', $tenant_id)
        ->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()])
        ->get();
        $data = $teams->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function ($group) {
            return $group->sum('duration_minute');
        });
 
    return [
        'datasets' => [
            [
                'label' => 'Total Duration Minute',
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
