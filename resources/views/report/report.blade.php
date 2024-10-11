<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body style="background-color: #f7fafc; margin: 0; font-family: Arial, sans-serif;">
    <div style="max-width: 850px; margin: auto; padding: 5px; background-color: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 8px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h3 style="text-align: center; font-size: 24px; font-weight: bold;">DroneLogbook Report</h3>
            <p style="text-align: center; margin: 0;">{{ $reportDate }}</p>
            @foreach($team as $teams)
                <p style="text-align: right; margin: 0;">{{ $teams->name }}</p>
                <p style="text-align: right; margin: 0;">{{ $teams->address }}</p>
            @endforeach
        </div>
        <hr style="margin: 16px 0; border-color: #e2e8f0;">
        <p style="text-align: right; margin: 16px 0;">Reporting Period: {{ $startDate }} to {{ $endDate }}</p>
        @foreach($team as $teams)
            <p style="text-align: left; margin: 0;">Company: {{ $teams->name }} ({{ $teams->owner }})</p>
            <p style="text-align: left; margin: 0;">Address: {{ $teams->address }}</p>
            <p style="text-align: left; margin: 0;">Website: {{ $teams->website }}</p>
            <p style="text-align: left; margin: 0;">Contact Email: {{ $teams->email }}</p>
            <p style="text-align: left; margin: 0;">Contact Phone: {{ $teams->phone }}</p>
        @endforeach

        <h2 style="margin-top: 16px; font-size: 20px; font-weight: 600; text-align: center;">Personnel</h2>
        <table style="width: 100%; border: 1px solid #cbd5e0; border-collapse: collapse; margin-top: 16px;">
            <thead>
                <tr style="background-color: #edf2f7;">
                    <th style="padding: 8px; border: 1px solid #cbd5e0;">Name</th>
                    <th style="padding: 8px; border: 1px solid #cbd5e0;">Email</th>
                    <th style="padding: 8px; border: 1px solid #cbd5e0;">Phone</th>
                </tr>
            </thead>
            <tbody>
                @foreach($user as $users)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;">{{ $users->name }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;">{{ $users->email }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;">{{ $users->phone }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2 style="margin-top: 16px; font-size: 20px; font-weight: 600; text-align: center;">Flight</h2>

        @foreach($flight as $flights)
            <table style="width: 100%; border: 1px solid #cbd5e0; border-collapse: collapse; margin-top: 16px;">
                <thead>
                    <tr style="background-color: #edf2f7;">
                        <th style="padding: 8px; border: 1px solid #cbd5e0;">Name</th>
                        <th style="padding: 8px; border: 1px solid #cbd5e0;">Date Flight</th>
                        <th style="padding: 8px; border: 1px solid #cbd5e0;">Pilot</th>
                        <th style="padding: 8px; border: 1px solid #cbd5e0;">Duration(H)</th>
                        <th style="padding: 8px; border: 1px solid #cbd5e0;">Duration(M)</th>
                        <th style="padding: 8px; border: 1px solid #cbd5e0;">Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;">{{ $flights->name }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;">{{ $flights->date_flight }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;">{{ $flights->users->name }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;">{{ $flights->duration_hour }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;">{{ $flights->duration_minute }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;">{{ $flights->type }}</td>
                    </tr>
                    <tr style="background-color: #edf2f7;">
                        <td colspan="7" style="padding: 8px; border: 1px solid #cbd5e0;">
                            <strong>Flight Details:</strong>
                        </td>
                    </tr>
                    <tr style="background-color: #f9fafb;">
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>Customers:</b> {{ $flights->customers->name }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>Drones:</b> {{ $flights->drones->name }}/{{ $flights->drones->geometry }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>2nd Pilot:</b> {{ $flights->instructor }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>OPS:</b> {{ $flights->ops }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>VO:</b> {{ $flights->vo }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>PO:</b> {{ $flights->po }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>Battery:</b> {{ $flights->battreis->name }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>Equipment:</b> {{ $flights->equidments->name }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>Fuel Used:</b> {{ $flights->fuel_used }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>Landings:</b> {{ $flights->landings }}</td>
                        <td style="padding: 8px; border: 1px solid #cbd5e0;"><b>Pre-Volt:</b> {{ $flights->pre_volt }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>
