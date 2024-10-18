<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Report</title>
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
        table {
            width: 100%;
            border: 1px solid #cbd5e0;
            border-collapse: collapse;
            margin-top: 16px;
            page-break-inside: avoid;
        }
        table, th, td {
            padding: 8px;
            border: 1px solid black;
        }
        th {
            background-color: #acd1af;
        }
        .header-title {
            font-size: 25px;
            text-align: center;
            margin: 0;
            background-color: #315a39;
            color: white;
        }
        .summary-row {
            background-color: #acd1af;
        }
        .detail-row{
            font-size: 15px;
            color: #555;
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
        <p class="report-date"><strong>Reporting Period: </strong>{{ $startDate }} <strong>to</strong> {{ $endDate }}</p>
        @foreach($team as $teams)
            <p style="text-align: left; margin: 0;"><strong>Company: </strong>{{ $teams->name }} ({{ $teams->owner }})</p>
            <p style="text-align: left; margin: 0;"><strong>Address: </strong>{{ $teams->address }}</p>
            <p style="text-align: left; margin: 0;"><strong>Website: </strong>{{ $teams->website }}</p>
            <p style="text-align: left; margin: 0;"><strong>Contact Email: </strong>{{ $teams->email }}</p>
            <p style="text-align: left; margin: 0;"><strong>Contact Phone: </strong>{{ $teams->phone }}</p>
        @endforeach

        <table class="personnel-table">
            <thead>
                    <th colspan="3" class="header-title">Personnel</th>
                <tr>
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

        <table class="drone-table">
            <thead>
                    <th colspan="3" class="header-title">Drone</th>
                <tr class="drone">
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

        @foreach($flight as $index => $flights)
            <table class="flight-table">
                <thead>
                        <th colspan="6" class="header-title">Flight {{ $index + 1 }}</th>
                    <tr class="flight">
                        <th>Name</th>
                        <th>Date Flight</th>
                        <th>Pilot</th>
                        <th>Duration</th>
                        <th>Type</th>
                        <th>Customers</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $flights->name }}</td>
                        <td>{{ $flights->date_flight }}</td>
                        <td>{{ $flights->users->name }}</td>
                        <td>{{ $flights->duration }}</td>
                        <td>{{ $flights->type }}</td>
                        <td>{{ $flights->customers->name }}</td>
                    </tr>
                    <tr class="summary-row">
                        <td colspan="6"><strong>Flight Details:</strong></td>
                    </tr>
                    <tr class="detail-row">
                        <td colspan="6">
                            <strong>Drones:</strong> {{ $flights->drones->name }}/{{ $flights->drones->geometry }} &nbsp;&nbsp;
                            <strong>2nd Pilot:</strong> {{ $flights->instructor }} &nbsp;&nbsp;
                            <strong>OPS:</strong> {{ $flights->ops }}
                            <br>
                            <strong>VO:</strong> {{ $flights->vo }} &nbsp;&nbsp;
                            <strong>PO:</strong> {{ $flights->po }} &nbsp;&nbsp;
                            <strong>Kits:</strong> {{ $flights->kits->name ?? ''}}
                        </td>
                    </tr>
                    <tr class="detail-row">
                        <td colspan="6">
                            <strong>Battery:</strong> {{ $flights->battreis->name ?? '' }} &nbsp;&nbsp;
                            <strong>Equipment:</strong> {{ $flights->equidments->name ?? '' }} 
                            <br>
                            <strong>Fuel Used:</strong> {{ $flights->fuel_used }} &nbsp;&nbsp;
                            <strong>Landings:</strong> {{ $flights->landings }}&nbsp;&nbsp;
                            <strong>Pre-Volt:</strong> {{ $flights->pre_volt }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>
