<?php
    $tenant_id = Auth()->user()->teams()->first()->id;
            $flights = App\Models\fligh::where('teams_id', $tenant_id)
                ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
                ->get();
        
            $data = $flights->groupBy(function ($item) {
                return Carbon\Carbon::parse($item->start_date_flight)->format('Y-m');
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
           
        $chartDataFlight = json_encode($data);
?>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<x-filament-widgets::widget>
    <canvas id="dualAxisChart"></canvas>  
</x-filament-widgets::widget>
<script>
    const chartData = <?php echo $chartDataFlight; ?>;

    const labels = Object.keys(chartData);
    const totalFlightsData = Object.values(chartData).map(item => item.totalFlights);
    const totalDurationData = Object.values(chartData).map(item => item.totalDuration);

    const data = {
        labels: labels,
        datasets: [
            {
                label: 'Flight #',
                data: totalFlightsData,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                yAxisID: 'y1',
            },
            {
                label: 'Flying Time',
                data: totalDurationData,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                yAxisID: 'y2', 
            },
        ],
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            scales: {
                y1: {
                    type: 'linear',
                    position: 'left', 
                    ticks: {
                        color: 'rgba(54, 162, 235, 1)',
                    },
                    grid: {
                        drawOnChartArea: true,
                        color: 'rgba(200, 200, 200, 0.5)',
                        lineWidth: 1, 
                    },
                    title: {
                        display: true,
                        text: 'Flight #',
                        color: 'rgba(54, 162, 235, 1)',
                    },
                },
                y2: {
                    type: 'linear',
                    position: 'right',
                    ticks: {
                        color: 'rgba(75, 192, 192, 1)',
                    },
                    title: {
                        display: true,
                        text: 'Flying Time',
                        color: 'rgba(75, 192, 192, 1)',
                    },
                },
                x: {
                    ticks: {
                        color: 'rgba(160, 160, 160, 1)' ,
                    },
                },
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const index = context.dataIndex;
                            const flightCount = totalFlightsData[index];
                            const flyingTime = totalDurationData[index];
                            return `Flight #: ${flightCount}, Flying Time: ${flyingTime} hrs`;
                        },
                    },
                },
            },
        },
    };

    const ctx = document.getElementById('dualAxisChart').getContext('2d');
    new Chart(ctx, config);
</script>