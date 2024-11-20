<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #315a39;
            color: white;
        }
        .row-span {
            font-size: 15px;
            color: #555;
        }
        .drone, .battery, .equipment {
            background-color: #acd1af;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <div class="team-info">
            <h3 style="text-align: center; font-size: 26px;">Inventory Report</h3>
            <p style="text-align: center">{{ $reportDate }}</p>
            @foreach($team as $teams)
                <p style="text-align: right; margin: 0;">{{ $teams->name }}</p>
                <p style="text-align: right; margin: 0;">{{ $teams->address }}</p>
            @endforeach
        </div>
        <hr>
        <br>

        <table>
            <thead>
                <tr style="font-size: 22px;">
                    <th>Total Drone</th>
                    <th>Total Battery</th>
                    <th>Total Equipment</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalDrone = $drone->count(); 
                    $totalBattery = $battery->count();
                    $totalEquipment = $equipment->count(); 
                @endphp
                <tr style="font-size: 18px;">
                    <td>{{$totalDrone}}</td>
                    <td>{{$totalBattery}}</td>
                    <td>{{$totalEquipment}}</td>
                </tr>
            </tbody>
        </table>

    <table>
        <thead>
            <th colspan="6" style="text-align: center; font-size: 25px">Drone</th>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Legal ID</th>
                <th>Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($drone as $drones)
                <tr class="drone">
                    <td>{{ $drones->name ?? null}}</td>
                    <td>{{ $drones->type ?? null}}</td>
                    <td>{{ $drones->brand ?? null}}</td>
                    <td>{{ $drones->model ?? null}}</td>
                    <td>{{ $drones->idlegal ?? null}}</td>
                    <td>{{ $drones->total_flying_time ?? '00:00:00'}}</td>
                </tr>
                <tr>
                    <td colspan="6" class="row-span">
                        <strong>Status:</strong> {{ $drones->status ?? null}} 
                        <br>
                        <strong>Owner:</strong> {{ $drones->users->name ?? null}}
                        <br>
                        <strong>Initial Flight Count:</strong> {{ $drones->initial_flight ?? null}} &nbsp;&nbsp; 
                        <strong>Initial Flying Time:</strong> {{ $drones->initial_flight_time ?? '00:00:00'}}
                        <br>
                        <strong>Inventory/Asset:</strong> {{ $drones->inventory_asset ?? null}} &nbsp;&nbsp;
                        <strong>Serial Printed:</strong> {{ $drones->serial_p ?? null}} &nbsp;&nbsp;
                        <strong>Serial Internal:</strong> {{ $drones->serial_i ?? null}}
                        <br>
                        <strong>Description:</strong> {{ $drones->description ?? null}}
                    </td>
                </tr>
                @endforeach
        </tbody>
    </table>

<table>
    <thead>
        <th colspan="5" style="text-align: center; font-size: 25px">Battery</th>
        <tr>
            <th>Name</th>
            <th>Model</th>
            <th>For Drone</th>
            <th>Purchase Date</th>
            <th>Life Span</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($battery as $batteries)
            <tr class="battery">
                <td>{{ $batteries->name ?? null}}</td>
                <td>{{ $batteries->model ?? null}}</td>
                <td>{{ $batteries->drone->name ?? null}}</td>
                <td>{{ $batteries->purchase_date ?? null}}</td>
                <td>{{ $batteries->life_span ?? null}}</td>
            </tr>
            <tr>
                <td colspan="5" class="row-span">
                    <strong>Status:</strong> {{ $batteries->status ?? null}} &nbsp;&nbsp; 
                    <strong>Owner:</strong> {{ $batteries->users->name ?? null}} &nbsp;&nbsp; 
                    <br>
                    <strong>Initial Cycle Count:</strong> {{ $batteries->initial_Cycle_count ?? null}} &nbsp;&nbsp; 
                    <strong>Inventory/Asset:</strong> {{ $batteries->asset_inventory ?? null}}
                    <br>
                    <strong>Weight:</strong> {{ $batteries->wight ?? null}} &nbsp;&nbsp; 
                    <strong>Firmware:</strong> {{ $batteries->firmware_version ?? null}} &nbsp;&nbsp; 
                    <strong>Hardware:</strong> {{ $batteries->hardware_version ?? null}}
                    <br>
                    <strong>Description:</strong> {{ $batteries->description ?? null}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <th colspan="5" style="text-align: center; font-size: 25px">Equipment</th>
        <tr>
            <th>Name</th>
            <th>Model</th>
            <th>Type</th>
            <th>Purchase Date</th>
            <th>For Drone</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($equipment as $equipments)
            <tr class="equipment">
                <td>{{ $equipments->name ?? null}}</td>
                <td>{{ $equipments->model ?? null}}</td>
                <td>{{ $equipments->type ?? null}}</td>
                <td>{{ $equipments->purchase_date ?? null}}</td>
                <td>{{ $equipments->drones->name ?? null}}</td>
            </tr>
            <tr>
                <td colspan="5" class="row-span">
                    <strong>Status:</strong> {{ $equipments->status ?? null}} 
                    <br>
                    <strong>Owner:</strong> {{ $equipments->users->name ?? null}} &nbsp;&nbsp;
                    <strong>Inventory/Asset number:</strong> {{ $equipments->inventory_asset ?? null}}
                    <br>
                    <strong>Description:</strong> {{ $equipments->description ?? null}} &nbsp;&nbsp;
                    
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
