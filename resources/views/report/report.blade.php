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
        .summary-row, .type1, .type2 {
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
            <p style="text-align: center">{{ $reportDate ?? null}}</p>
            @foreach($team as $teams)
                <p style="text-align: right; margin: 0;">{{ $teams->name ?? null}}</p>
                <p style="text-align: right; margin: 0;">{{ $teams->address ?? null}}</p>
            @endforeach
        </div>
        <hr>
        <p class="report-date"><strong>Reporting Period: </strong>{{ $startDate ?? null}} <strong>to</strong> {{ $endDate ?? null}}</p>
        @foreach($team as $teams)
            <p style="text-align: left; margin: 0;"><strong>Company: </strong>{{ $teams->name ?? null}} ({{ $teams->owner ?? null}})</p>
            <p style="text-align: left; margin: 0;"><strong>Address: </strong>{{ $teams->address ?? null}}</p>
            <p style="text-align: left; margin: 0;"><strong>Website: </strong>{{ $teams->website ?? null}}</p>
            <p style="text-align: left; margin: 0;"><strong>Contact Email: </strong>{{ $teams->email ?? null}}</p>
            <p style="text-align: left; margin: 0;"><strong>Contact Phone: </strong>{{ $teams->phone ?? null}}</p>
        @endforeach

        <table>
            <thead>
                <tr style="font-size: 22px; background-color: #315a39;">
                    <th>Total Personnel</th>
                    <th>Total Drone</th>
                    <th>Total flight</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPersonnel = $user->count(); 
                    $totalDrone = $drone->count();
                    $totalFlight = $flight->count(); 
                @endphp
                <tr style="font-size: 18px;">
                    <td>{{$totalPersonnel}}</td>
                    <td>{{$totalDrone}}</td>
                    <td>{{$totalFlight}}</td>
                </tr>
            </tbody>
        </table>

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
                        <td>{{ $users->name ?? null}}</td>
                        <td>{{ $users->email ?? null}}</td>
                        <td>{{ $users->phone ?? null}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="drone-table">
            <thead>
                    <th colspan="7" class="header-title">Drone</th>
                <tr class="drone">
                    <th colspan="2">Name</th>
                    <th colspan="2">Type</th>
                    <th colspan="3">Geometry</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $droneType = $drone->groupBy('type')->map(function ($group){
                        return $group->count();
                    });
                    $type1 = ['aircraft', 'autoPilot', 'boat', 'fixed_wing', 'flight controller', 'flying-wings', 'fpv'];
                    $type2 = ['hexsacopter', 'home-made', 'multi-rotors', 'quadcopter', 'rover', 'rpa', 'Submersible'];
                @endphp

                @foreach($drone as $drones)
                    <tr>
                        <td colspan="2">{{ $drones->name ?? null}}</td>
                        <td colspan="2">{{ $drones->type ?? null}}</td>
                        <td colspan="3">{{ $drones->geometry ?? null}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="7" style="text-align: center;" class="header-title"><strong>Total PerType:</strong></td>
                </tr>
                <tr class="type1">
                    @foreach($type1 as $types1)
                        <td><strong>{{ ucfirst(str_replace('_', ' ', $types1)) }}</strong></td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($type1 as $types1)
                        <td>{{ $droneType[$types1] ?? 0 }}</td>
                    @endforeach
                </tr>
                <tr class="type2">
                    @foreach($type2 as $types2)
                        <td><strong>{{ ucfirst(str_replace('_', ' ', $types2)) }}</strong></td>
                    @endforeach
                </tr>
                <tr>
                    @foreach($type2 as $types2)
                        <td>{{ $droneType[$types2] ?? 0 }}</td>
                    @endforeach
                </tr>
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
                        <td>{{ $flights->name ?? null}}</td>
                        <td>{{ $flights->start_date_flight ?? null }}</td>
                        <td>{{ $flights->users->name ?? null}}</td>
                        <td>{{ $flights->duration ?? null}}</td>
                        <td>{{ $flights->type ?? null}}</td>
                        <td>{{ $flights->customers->name ?? null}}</td>
                    </tr>
                    <tr class="summary-row">
                        <td colspan="6"><strong>Flight Details:</strong></td>
                    </tr>
                    <tr class="detail-row">
                        <td colspan="6">
                            <strong>Drones:</strong> {{ $flights->drones->name ?? null}}/{{ $flights->drones->geometry ?? null}} &nbsp;&nbsp;
                            <strong>2nd Pilot:</strong> {{ $flights->instructor ?? null}} &nbsp;&nbsp;
                            <strong>OPS:</strong> {{ $flights->ops ?? null}}
                            <br>
                            <strong>VO:</strong> {{ $flights->vo ?? null}} &nbsp;&nbsp;
                            <strong>PO:</strong> {{ $flights->po ?? null}} &nbsp;&nbsp;
                            <strong>Kits:</strong> {{ $flights->kits->name ?? null}}
                        </td>
                    </tr>
                    <tr class="detail-row">
                        <td colspan="6">
                            @foreach ($flights->battreis as $battery)
                                <strong>Battery:</strong> {{ $battery->name}}@if(!$loop->last), @endif
                            @endforeach
                            @foreach ($flights->equidments as $equipment)
                                <strong>Equipment:</strong> {{ $equipment->name}}@if(!$loop->last), @endif
                            @endforeach
                            <br>
                            <strong>Fuel Used:</strong> {{ $flights->fuel_used ?? null}} &nbsp;&nbsp;
                            <strong>Landings:</strong> {{ $flights->landings ?? null}}&nbsp;&nbsp;
                            <strong>Pre-Volt:</strong> {{ $flights->pre_volt ?? null}}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>
