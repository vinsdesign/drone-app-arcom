<x-filament-panels::page>
    <head>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <div class="container mt-5">
        <h2 class="mb-4 text-center" >Download Report</h2>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" action="{{ route('filament.report.download') }}" class="bg-light p-4 rounded shadow">
                    @csrf

                    <div class="form-group">
                        <label for="start_date">From:</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date">To:</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="background-color: #ff8303; border-color: #ff8303;">Download Report</button>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>
