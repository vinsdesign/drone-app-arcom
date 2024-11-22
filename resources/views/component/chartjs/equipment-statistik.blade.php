<?php
    $tenant_id = Auth()->user()->teams()->first()->id;
        $equipmentID = session('equipment_id');

        $pivotEquipment_id = DB::table('fligh_equidment')->where('equidment_id', $equipmentID)
        ->select('fligh_id')
        ->distinct()
        ->pluck('fligh_id');

        $kitsEquioment_id = DB::table('equidment_kits')
            ->where('equidment_id', $equipmentID)
            ->distinct()
            ->pluck('kits_id');        
        
        $flightsEquipmentPivot = App\Models\fligh::where('teams_id', $tenant_id)
            ->whereIn('id', $pivotEquipment_id)
            ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
            ->get();

        $flightsKitsEquipment = App\Models\fligh::where('teams_id', $tenant_id)
            ->whereIn('kits_id', $kitsEquioment_id)
            ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
            ->get();

        $flights = $flightsEquipmentPivot->merge($flightsKitsEquipment);

        // dd($flights);
        
        
        
        $data = $flights->groupBy(function ($item) {
            return Carbon\Carbon::parse($item->start_date_flight)->format('Y-m');
        })->map(function ($group) {
            // Hitung total penerbangan
            $totalFlights = $group->count();
        
            //konversi ke hours
            $totalDuration = $group->sum(function ($flight) {
                list($hours, $minutes, $seconds) = explode(':', $flight->duration);
                return ($hours * 3600) + ($minutes * 60) + $seconds;
            });
            $totalHours = $totalDuration / 3600;
            $formatTotalHour = round($totalHours, 1);
        
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
    <div class="flex justify-center items-center">
        <div class="rounded-lg shadow-lg p-4 w-full max-w-md sm:max-w-lg md:max-w-2xl lg:max-w-1/2 dark:bg-gray-900">
            <canvas id="dualAxisChart"></canvas>
        </div>
    </div>
    
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

