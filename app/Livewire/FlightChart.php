<?php

namespace App\Livewire;

use App\Models\fligh;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Helpers\TranslationHelper;

class FlightChart extends ChartWidget
{
    protected static ?string $heading = 'Flights in the Last 30 Days';
    protected static bool $isLazy = false;
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        $teams = fligh::where('teams_id', $tenant_id)
        ->whereBetween('start_date_flight', [now()->subDays(30), now()])
        ->get();
        $data = $teams->groupBy(function ($item) {
            return Carbon::parse($item->start_date_flight)->format('Y-m-d');
        })->map(function ($group) {
            return $group->count();
        });
 
    return [
        'datasets' => [
            [
                'label' => TranslationHelper::translateIfNeeded('Flight PerDay'),
                'data' => $data->map(fn ($value) => round($value))->values(),
                'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.4,
            ],
        ],
        'labels' => $data->map(fn ($value) => round($value))->keys(),
    ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    //option header
    protected function getOptions(): array
    {
        $tenant_id = Auth()->User()->teams()->first()->id;
        //drone
        $totalFlight= fligh::where('teams_id', $tenant_id)
        ->whereBetween('start_date_flight', [now()->subDays(30), now()])
        ->count('name');

        return [
        'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => "$totalFlight Flight",
                    'font' => [
                        'size' => 14,
                    ],
                ],
            ],
        ];
    }
}
