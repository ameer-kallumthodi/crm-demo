<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Telecaller Report - {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h3 {
            margin-top: 0;
            color: #333;
        }
        .summary-item {
            margin: 5px 0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Telecaller Behavior & Productivity Report</h1>
        <p>Report Period: {{ $startDate }} to {{ $endDate }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Telecaller</th>
                <th>Login Time</th>
                <th>Logout Time</th>
                <th>Duration (Hrs)</th>
                <th>Active (Hrs)</th>
                <th>Idle (Hrs)</th>
                <th>Logout Type</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
            <tr>
                <td>{{ $session->user->name }}</td>
                <td>{{ $session->login_time->format('M d, Y H:i') }}</td>
                <td>{{ $session->logout_time ? $session->logout_time->format('M d, Y H:i') : 'Active' }}</td>
                <td>
                    @if($session->total_duration_minutes)
                        {{ number_format($session->total_duration_minutes / 60, 1) }}
                    @else
                        {{ number_format($session->calculateTotalDuration() / 60, 1) }}
                    @endif
                </td>
                <td>
                    @if($session->active_duration_minutes)
                        {{ number_format($session->active_duration_minutes / 60, 1) }}
                    @else
                        {{ number_format($session->calculateActiveDuration() / 60, 1) }}
                    @endif
                </td>
                <td>
                    @if($session->idle_duration_minutes)
                        {{ number_format($session->idle_duration_minutes / 60, 1) }}
                    @else
                        {{ number_format($session->idleTimes()->sum('idle_duration_seconds') / 3600, 1) }}
                    @endif
                </td>
                <td>{{ ucfirst($session->logout_type) }}</td>
                <td>{{ $session->ip_address }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">No sessions found for the selected period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary Statistics</h3>
        <div class="summary-item"><strong>Total Sessions:</strong> {{ $sessions->count() }}</div>
        <div class="summary-item"><strong>Total Login Hours:</strong> {{ number_format($sessions->sum(function($s) { return $s->total_duration_minutes ? $s->total_duration_minutes / 60 : $s->calculateTotalDuration() / 60; }), 1) }}</div>
        <div class="summary-item"><strong>Total Active Hours:</strong> {{ number_format($sessions->sum(function($s) { return $s->active_duration_minutes ? $s->active_duration_minutes / 60 : $s->calculateActiveDuration() / 60; }), 1) }}</div>
        <div class="summary-item"><strong>Total Idle Hours:</strong> {{ number_format($sessions->sum(function($s) { return $s->idle_duration_minutes ? $s->idle_duration_minutes / 60 : $s->idleTimes()->sum('idle_duration_seconds') / 3600; }), 1) }}</div>
        <div class="summary-item"><strong>Average Session Duration:</strong> {{ $sessions->count() > 0 ? number_format($sessions->avg(function($s) { return $s->total_duration_minutes ? $s->total_duration_minutes / 60 : $s->calculateTotalDuration() / 60; }), 1) : 0 }} hours</div>
    </div>

    <div class="footer">
        <p>This report was generated automatically by the CRM Telecaller Tracking System.</p>
        <p>For questions or support, please contact the system administrator.</p>
    </div>
</body>
</html>
