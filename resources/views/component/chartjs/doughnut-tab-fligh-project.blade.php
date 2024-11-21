<?php
        $tenant_id = Auth()->User()->teams()->first()->id;
        $topProjects = App\Models\Fligh::where('teams_id', $tenant_id)
                    ->selectRaw('projects_id, COUNT(id) as flight_count')
                    ->groupBy('projects_id')
                    ->orderByDesc('flight_count')
                    ->limit(3)
                    ->with('projects')
                    ->get();

        $topProjectsCount = App\Models\Fligh::where('teams_id', $tenant_id)
        ->selectRaw('projects_id, COUNT(id) as flight_count')
        ->groupBy('projects_id')
        ->orderByDesc('flight_count')
        ->limit(3)
        ->with('projects')
        ->get()
        ->count();
        
        $otherTotalProject = App\Models\Fligh::where('teams_id', $tenant_id)
                    ->whereNotIn('projects_id', $topProjects->pluck('projects_id'))
                    ->count();
 
        
        $labels=[];
        $datas = [];
        foreach ($topProjects as $project) {
            $labels[] = $project->projects->case;
            $datas[] = $project->flight_count;
        };
        if($topProjectsCount == 3){
            $data = [$datas[0], $datas[1], $datas[2],$otherTotalProject];
            $label = [$labels[0],$labels[1],$labels[2],'Other'];
            $color = [
                '#FFA500',
                '#ADD8E6',
                '#6A5ACD',
                '#32CD32'   
            ];
        }elseif($topProjectsCount == 2){
            $data = [$datas[0], $datas[1],$otherTotalProject];
            $label = [$labels[0],$labels[1],'Other'];
            $color = [
                '#FFA500',
                '#ADD8E6',
                '#32CD32'   
            ];
        }elseif($topProjectsCount == 1){
            $data = [$datas[0],$otherTotalProject];
            $label = [$labels[0],'Other'];
            $color = [
                '#FFA500',
                '#32CD32'   
            ];
        }elseif($topProjectsCount == 0){
            $data = [$otherTotalProject];
            $label = ['Other'];
            $color = [
                '#32CD32'   
            ];
        }
    $tenant_id = Auth()->User()->teams()->first()->id;

    $totalProject = App\Models\fligh::where('teams_id', $tenant_id)
        ->distinct('projects_id')
        ->count('projects_id');
    $totalFlight = App\Models\fligh::where('teams_id', $tenant_id)->count('name');
?>
<x-filament-widgets::widget>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <p class="text-center uppercase font-semibold mb-2 text-sm text-gray-800 dark:text-gray-200">Total {{$totalFlight}} Flights Across {{$totalProject}} Projects</p>
    <canvas id="tabflightProject"></canvas>
</x-filament-widgets::widget>
<script>
    const chartLabelsFlightProject = @json($label);
    const chartDataFlightProject = @json($data);
    const chartColorsFlightProject = @json($color);
    const dataFlight = {
        labels: chartLabelsFlightProject,
        datasets: [{
            label: 'Flight Data Project',
            data: chartDataFlightProject,
            backgroundColor: chartColorsFlightProject,
            borderColor: chartColorsFlightProject,
            borderWidth: 1
        }]
    };

    const configFlightProject = {
        type: 'doughnut',
        data: dataFlight,
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
    const ctxFlightProject = document.getElementById('tabflightProject').getContext('2d');
    new Chart(ctxFlightProject, configFlightProject);
</script> 