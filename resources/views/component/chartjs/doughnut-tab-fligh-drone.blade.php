<?php
    $tenant_id = Auth()->User()->teams()->first()->id;
    $topDrone = App\Models\Fligh::where('teams_id', $tenant_id)
                ->selectRaw('drones_id, COUNT(id) as flight_count')
                ->groupBy('drones_id')
                ->orderByDesc('flight_count')
                ->limit(3)
                ->with('drones')
                ->get();
    $topDroneCount = App\Models\Fligh::where('teams_id', $tenant_id)
                ->selectRaw('drones_id, COUNT(id) as flight_count')
                ->groupBy('drones_id')
                ->orderByDesc('flight_count')
                ->limit(3)
                ->with('drones')
                ->get()
                ->count();
    
    $otherTotalDrone = App\Models\Fligh::where('teams_id', $tenant_id)
                ->whereNotIn('drones_id', $topDrone->pluck('drones_id'))
                ->count();

    
    $labels=[];
    $datas = [];
    foreach ($topDrone as $drone) {
        $labels[] = $drone->drones->name;
        $datas[] = $drone->flight_count;
    };
    if($topDroneCount == 3){
        $data = [$datas[0], $datas[1], $datas[2],$otherTotalDrone];
        $label = [$labels[0],$labels[1],$labels[2],'Other'];
        $color = [
            '#FFA500',
            '#ADD8E6',
            '#6A5ACD',
            '#32CD32'   
        ];
    }elseif($topDroneCount == 2){
        $data = [$datas[0], $datas[1],$otherTotalDrone];
        $label = [$labels[0],$labels[1],'Other'];
        $color = [
            '#FFA500',
            '#ADD8E6',
            '#32CD32'   
        ];
    }elseif($topDroneCount == 1){
        $data = [$datas[0],$otherTotalDrone];
        $label = [$labels[0],'Other'];
        $color = [
            '#FFA500',
            '#32CD32'   
        ];
    }elseif($topDroneCount == 0){
        $data = [$otherTotalDrone];
        $label = ['Other'];
        $color = [
            '#32CD32'   
        ];
    }

    //drone
    $totalDrones = App\Models\fligh::where('teams_id', $tenant_id)
    ->distinct('drones_id')
    ->count('drones_id');
    $totalFlight = App\Models\fligh::where('teams_id', $tenant_id)->count('name');

        
?>
<x-filament-widgets::widget>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <p class="text-center uppercase font-semibold mb-2 text-sm text-gray-800 dark:text-gray-200">Total {{$totalFlight}} Flights with {{$totalDrones}} Drones</p>
    <canvas id="tabflightdrone"></canvas>
</x-filament-widgets::widget>
<script>
    // Dummy data
    const chartLabelsFlightDrone = @json($label);
    const chartDataFlightDrone = @json($data);
    const chartColorsFlightDrone = @json($color);
    const dataFlightDrone = {
        labels: chartLabelsFlightDrone,
        datasets: [{
            label: 'Flight Data',
            data: chartDataFlightDrone,
            backgroundColor: chartColorsFlightDrone,
            borderColor: chartColorsFlightDrone,
            borderWidth: 1
        }]
    };

    const configFlightDrone = {
        type: 'doughnut',
        data: dataFlightDrone,
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || 'Unknown';
                            const value = context.raw || 0;
                            return `${label}: ${value} Flight`;
                        }
                    },
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    borderColor: '#ccc',
                    borderWidth: 1,
                    titleColor: '#000',
                    bodyColor: '#000',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 12
                    },
                    padding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 12
                        },
                        padding: 10,
                        boxWidth: 10,
                        boxHeight: 10,
                    }
                }
            }
        }
    };

    // Render the chart
    const ctxFlightDrone = document.getElementById('tabflightdrone').getContext('2d');
    new Chart(ctxFlightDrone, configFlightDrone);
</script> 