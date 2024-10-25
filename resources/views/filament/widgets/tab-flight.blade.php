<div>
    <h3>{{ $chartType }} Chart</h3>
    <canvas id="myChart"></canvas>
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: '{{ $chartType }}', // Mendapatkan jenis chart dari widget
        data: {
            labels: @json($labels), // Mendapatkan label dari widget
            datasets: @json($datasets), // Mendapatkan datasets dari widget
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Drone Inventory Chart',
                },
            },
        },
    });
</script>

@endpush
