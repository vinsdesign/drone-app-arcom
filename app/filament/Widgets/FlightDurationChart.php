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
        $data = Trend::model(fligh::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perDay()
        ->sum('duration_minute');
 
    return [
        'datasets' => [
            [
                'label' => 'Total Duration Minute',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
