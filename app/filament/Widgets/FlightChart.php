<?php

namespace App\Filament\Widgets;

use App\Models\fligh;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class FlightChart extends ChartWidget
{
    protected static ?string $heading = 'Flight';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $teams = fligh::where('teams_id', $tenant_id)

        ->whereBetween('Start_date_flight', [now()->startOfYear(), now()->endOfYear()])
        ->get();
        $data = $teams->groupBy(function ($item) {
            return Carbon::parse($item->date_flight)->format('Y-m-d');
        })->map(function ($group) {
            return $group->count();
        });
 
    return [
        'datasets' => [
            [
                'label' => 'Flight Perhari',
                'data' => $data->map(fn ($value) => round($value))->values(),
            ],
        ],
        'labels' => $data->map(fn ($value) => round($value))->keys(),
    ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
