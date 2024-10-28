<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Income Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        h3 {
            text-align: center;
        }
        .team-info {
            margin-bottom: 20px;
            text-align: right;
        }
        .total {
            font-weight: bold;
            margin-top: 10px;
        }
        hr {
            margin: 20px 0;
        }
        .grid-header {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 10px;
        font-weight: bold;
        border-bottom: 2px solid #4ed34e;
        }
        
        .grid-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            margin-bottom: 10px;
            padding: 5px;
            border-bottom: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Income Expense Report</h3>
        <p style="text-align: center">{{ $reportDate }}</p>
        <div class="team-info">
            @foreach($team as $teams)
                <p>{{ $teams->name ?? null}}</p>
                <p>{{ $teams->address ?? null}}</p>
            @endforeach
            <p class="report-date" style="text-align: left;">
                <strong>Reporting Period: </strong>{{ $startDate ?? null }} <strong>to</strong> {{ $endDate ?? null }}
            </p>
        </div>
        <hr>

        <div>
            <h4 style="color: #4ed34e">INCOME</h4>
            <!-- Header Row -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr style="background-color: #4ed34e; color: white; font-weight: bold;">
                        <th style="padding: 8px; text-align: left;">Date</th>
                        <th style="padding: 8px; text-align: left;">Project Case</th>
                        <th style="padding: 8px; text-align: left;">Currency</th>
                        <th style="padding: 8px; text-align: left;">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($project as $projects)
                        <tr>
                            <td style="padding: 8px;">{{ $projects->created_at->format('d-m-y') }}</td>
                            <td style="padding: 8px;">{{ $projects->case }}</td>
                            <td style="padding: 8px;">{{ $projects->currencies->name }}</td>
                            <td style="padding: 8px;">Rp. {{ number_format($projects->revenue, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="clear: both;"></div>
            <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                <div style="width: 25%; border-top: 1px solid black; margin-left: auto;"></div>
            </div>
            <div class="total" style="margin-left: 58%">
                @php
                    $totalIncome = $project->sum('revenue');
                @endphp
                <strong style="color: #4ed34e">Total Income:</strong> Rp. {{ number_format($totalIncome, 0, ',', '.') }}
            </div>
        </div>

        <br>
        <div>
            <h4 style="color: #f20000">EXPENSE</h4>
            <h5 style="color: #b32113; display: flex; align-items: center;">
                <span style="width: 8px; height: 8px; background-color: red; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                Maintenance Drone
            </h5>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr style="background-color: #b32113; color: white; font-weight: bold;">
                        <th style="padding: 8px; text-align: left;">Date</th>
                        <th style="padding: 8px; text-align: left;">Maintenance Name</th>
                        <th style="padding: 8px; text-align: left;">Currency</th>
                        <th style="padding: 8px; text-align: left;">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($maintenance_drone as $m_drone)
                        <tr>
                            <td style="padding: 8px;">{{ $m_drone->date ?? null}}</td>
                            <td style="padding: 8px;">{{ $m_drone->name ?? null}}</td>
                            <td style="padding: 8px;">{{ $m_drone->currencies->name ?? null}}</td>
                            <td style="padding: 8px;">Rp. {{ number_format($m_drone->cost, 0, ',', '.') ?? null}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="clear: both;"></div>
            <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                <div style="width: 25%; border-top: 1px solid black; margin-left: auto;"></div>
            </div>
            <div class="total" style="margin-left: 60%">
                @php
                    $totalDroneEx = $maintenance_drone->sum('cost');
                @endphp
                <strong>Sub Total :</strong> Rp. {{ number_format($totalDroneEx, 0, ',', '.') }}
            </div>

            <br>
            <h5 style="color: #b32113; display: flex; align-items: center;">
                <span style="width: 8px; height: 8px; background-color: red; border-radius: 50%; display: inline-block; margin-right: 8px;"></span>
                Maintenance Equipment & Battery
            </h5>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr style="background-color: #b32113; color: white; font-weight: bold;">
                        <th style="padding: 8px; text-align: left;">Date</th>
                        <th style="padding: 8px; text-align: left;">Maintenance Name</th>
                        <th style="padding: 8px; text-align: left;">Currency</th>
                        <th style="padding: 8px; text-align: left;">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($maintenance_eq as $m_eq)
                        <tr>
                            <td style="padding: 8px;">{{ $m_eq->date ?? null}}</td>
                            <td style="padding: 8px;">{{ $m_eq->name ?? null}}</td>
                            <td style="padding: 8px;">{{ $m_eq->currencies->name ?? null}}</td>
                            <td style="padding: 8px;">Rp. {{ number_format($m_eq->cost, 0, ',', '.') ?? null}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="clear: both;"></div>
            <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                <div style="width: 25%; border-top: 1px solid black; margin-left: auto;"></div>
            </div>
            <div class="total" style="margin-left: 60%">
                @php
                    $totalEqEx = $maintenance_eq->sum('cost');
                @endphp
                <strong>Sub Total :</strong> Rp. {{ number_format($totalEqEx, 0, ',', '.') }}
            </div>
            <div style="clear: both;"></div>
            <div style="display: flex; justify-content: flex-end; margin-top: 10px;">
                <div style="width: 33%; border-top: 1px solid black; margin-left: auto;"></div>
            </div>
            <div class="total" style="margin-left: 48%">
                @php
                    $totalAll = $totalDroneEx + $totalEqEx;
                @endphp
                <strong style="color: #f20000">Total Expense:</strong> Rp. {{ number_format($totalAll, 0, ',', '.') }}
            </div>
            <hr>
            <div class="total" style="font-size: 25px">
                @php
                    $netIncome = $totalIncome - $totalAll;
                @endphp
                <strong>Net Income:</strong> Rp. {{ number_format($netIncome, 0, ',', '.') }}
            </div>
        </div>
    </div>
</body>
</html>
