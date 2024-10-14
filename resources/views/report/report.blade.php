<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f7fafc;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 850px;
            margin: auto;
            padding: 5px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h3 {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .team-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        hr {
            margin: 16px 0;
            border-color: #e2e8f0;
        }
        .report-date {
            text-align: right;
            margin: 16px 0;
        }
        .personnel-table,
        .drone-table,
        .flight-table {
            width: 100%;
            border: 1px solid #cbd5e0;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .personnel-table th,
        .drone-table th,
        .flight-table th,
        .personnel-table td,
        .drone-table td,
        .flight-table td {
            padding: 8px;
            border: 1px solid #cbd5e0;
        }
        .header-row {
            background-color: #edf2f7;
        }
        .detail-row {
            background-color: #f9fafb;
        }
        .summary-row {
            background-color: #edf2f7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="team-info">
            <h3>DroneLogbook Report</h3>
            <p style="text-align: center">{{ $reportDate }}</p>
            @foreach($team as $teams)
                <p style="text-align: right; margin: 0;">{{ $teams->name }}</p>
                <p style="text-align: right; margin: 0;">{{ $teams->address }}</p>
            @endforeach
        </div>
        <hr>
        <p class="report-date">Reporting Period: {{ $startDate }} to {{ $endDate }}</p>
        @foreach($team as $teams)
            <p style="text-align: left; margin: 0;">Company: {{ $teams->name }} ({{ $teams->owner }})</p>
            <p style="text-align: left; margin: 0;">Address: {{ $teams->address }}</p>
            <p style="text-align: left; margin: 0;">Website: {{ $teams->website }}</p>
            <p style="text-align: left; margin: 0;">Contact Email: {{ $teams->email }}</p>
            <p style="text-align: left; margin: 0;">Contact Phone: {{ $teams->phone }}</p>
        @endforeach

        <h2 style="margin-top: 16px; font-size: 20px; font-weight: 600; text-align: center;">Personnel</h2>
        <table class="personnel-table">
            <thead>
                <tr class="header-row">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user as $users)
                    <tr>
                        <td>{{ $users->name }}</td>
                        <td>{{ $users->email }}</td>
                        <td>{{ $users->phone }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2 style="margin-top: 16px; font-size: 20px; font-weight: 600; text-align: center;">Drone</h2>
        <table class="drone-table">
            <thead>
                <tr class="header-row">
                    <th>Name</th>
                    <th>Type</th>
                    <th>Geometry</th>
                </tr>
            </thead>
            <tbody>
                @foreach($drone as $drones)
                    <tr>
                        <td>{{ $drones->name }}</td>
                        <td>{{ $drones->type }}</td>
                        <td>{{ $drones->geometry }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2 style="margin-top: 16px; font-size: 20px; font-weight: 600; text-align: center;">Flight</h2>

        @foreach($flight as $flights)
            <table class="flight-table">
                <thead>
                    <tr class="header-row">
                        <th>Name</th>
                        <th>Date Flight</th>
                        <th>Pilot</th>
                        <th>Duration(H)</th>
                        <th>Duration(M)</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $flights->name }}</td>
                        <td>{{ $flights->date_flight }}</td>
                        <td>{{ $flights->users->name }}</td>
                        <td>{{ $flights->duration_hour }}</td>
                        <td>{{ $flights->duration_minute }}</td>
                        <td>{{ $flights->type }}</td>
                    </tr>
                    <tr class="summary-row">
                        <td colspan="6"><strong>Flight Details:</strong></td>
                    </tr>
                    <tr class="detail-row">
                        <td><b>Customers:</b> {{ $flights->customers->name }}</td>
                        <td><b>Drones:</b> {{ $flights->drones->name }}/{{ $flights->drones->geometry }}</td>
                        <td><b>2nd Pilot:</b> {{ $flights->instructor }}</td>
                        <td><b>OPS:</b> {{ $flights->ops }}</td>
                        <td><b>VO:</b> {{ $flights->vo }}</td>
                        <td><b>PO:</b> {{ $flights->po }}</td>
                    </tr>
                    <tr>
                        <td><b>Battery:</b> {{ $flights->battreis->name }}</td>
                        <td><b>Equipment:</b> {{ $flights->equidments->name }}</td>
                        <td><b>Fuel Used:</b> {{ $flights->fuel_used }}</td>
                        <td><b>Landings:</b> {{ $flights->landings }}</td>
                        <td><b>Pre-Volt:</b> {{ $flights->pre_volt }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>
