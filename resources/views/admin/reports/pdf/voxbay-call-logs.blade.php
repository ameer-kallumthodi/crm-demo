<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Voxbay Call Logs Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
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
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .summary {
            margin-bottom: 30px;
        }
        .summary h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-row {
            display: table-row;
        }
        .stats-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .stats-cell h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #333;
        }
        .stats-cell .number {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
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
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Voxbay Call Logs Report</h1>
        <p>Date Range: {{ $fromDate }} to {{ $toDate }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <!-- Summary Statistics -->
    <div class="summary">
        <h2>Summary Statistics</h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell">
                    <h3>Total Calls</h3>
                    <div class="number">{{ $reportData['stats']['total_calls'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Answered Calls</h3>
                    <div class="number">{{ $reportData['stats']['answered_calls'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Answer Rate</h3>
                    <div class="number">
                        {{ $reportData['stats']['total_calls'] > 0 ? 
                            round(($reportData['stats']['answered_calls'] / $reportData['stats']['total_calls']) * 100, 2) : 0 }}%
                    </div>
                </div>
                <div class="stats-cell">
                    <h3>Total Duration</h3>
                    <div class="number">{{ gmdate('H:i:s', $reportData['stats']['total_duration']) }}</div>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell">
                    <h3>Incoming Calls</h3>
                    <div class="number">{{ $reportData['stats']['incoming_calls'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Outgoing Calls</h3>
                    <div class="number">{{ $reportData['stats']['outgoing_calls'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Busy Calls</h3>
                    <div class="number">{{ $reportData['stats']['busy_calls'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Avg Duration</h3>
                    <div class="number">{{ gmdate('H:i:s', $reportData['stats']['average_duration']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Telecaller Performance -->
    <div class="summary">
        <h2>Telecaller Performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Telecaller</th>
                    <th>Team</th>
                    <th class="text-center">Total Calls</th>
                    <th class="text-center">Incoming</th>
                    <th class="text-center">Outgoing</th>
                    <th class="text-center">Answered</th>
                    <th class="text-center">Answer Rate</th>
                    <th class="text-center">Total Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['telecaller_stats'] as $stat)
                <tr>
                    <td>{{ $stat['telecaller_name'] }}</td>
                    <td>{{ $stat['team_name'] }}</td>
                    <td class="text-center">{{ $stat['total_calls'] }}</td>
                    <td class="text-center">{{ $stat['incoming_calls'] }}</td>
                    <td class="text-center">{{ $stat['outgoing_calls'] }}</td>
                    <td class="text-center">{{ $stat['answered_calls'] }}</td>
                    <td class="text-center">
                        <span class="badge {{ $stat['answer_rate'] >= 70 ? 'badge-success' : ($stat['answer_rate'] >= 50 ? 'badge-warning' : 'badge-danger') }}">
                            {{ $stat['answer_rate'] }}%
                        </span>
                    </td>
                    <td class="text-center">{{ gmdate('H:i:s', $stat['total_duration']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Team Performance -->
    <div class="summary page-break">
        <h2>Team Performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Team</th>
                    <th class="text-center">Members</th>
                    <th class="text-center">Total Calls</th>
                    <th class="text-center">Incoming</th>
                    <th class="text-center">Outgoing</th>
                    <th class="text-center">Answered</th>
                    <th class="text-center">Answer Rate</th>
                    <th class="text-center">Total Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['team_stats'] as $stat)
                <tr>
                    <td>{{ $stat['team_name'] }}</td>
                    <td class="text-center">{{ $stat['total_members'] }}</td>
                    <td class="text-center">{{ $stat['total_calls'] }}</td>
                    <td class="text-center">{{ $stat['incoming_calls'] }}</td>
                    <td class="text-center">{{ $stat['outgoing_calls'] }}</td>
                    <td class="text-center">{{ $stat['answered_calls'] }}</td>
                    <td class="text-center">
                        <span class="badge {{ $stat['answer_rate'] >= 70 ? 'badge-success' : ($stat['answer_rate'] >= 50 ? 'badge-warning' : 'badge-danger') }}">
                            {{ $stat['answer_rate'] }}%
                        </span>
                    </td>
                    <td class="text-center">{{ gmdate('H:i:s', $stat['total_duration']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Recent Calls -->
    <div class="summary page-break">
        <h2>Recent Calls (Last 50)</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Telecaller</th>
                    <th>Destination</th>
                    <th>Status</th>
                    <th class="text-center">Duration</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['recent_calls'] as $callLog)
                <tr>
                    <td>{{ $callLog->id }}</td>
                    <td>
                        <span class="badge {{ $callLog->type == 'incoming' ? 'badge-info' : ($callLog->type == 'outgoing' ? 'badge-success' : 'badge-warning') }}">
                            {{ ucfirst($callLog->type) }}
                        </span>
                    </td>
                    <td>{{ $callLog->telecaller_name }}</td>
                    <td>{{ $callLog->destinationNumber ?? 'N/A' }}</td>
                    <td>
                        @php
                            $status = strtoupper($callLog->status ?? 'UNKNOWN');
                            $badgeClass = 'badge-info';
                            if ($status == 'ANSWER') $badgeClass = 'badge-success';
                            elseif (in_array($status, ['CANCEL', 'BUSY'])) $badgeClass = 'badge-danger';
                            elseif ($status == 'NO ANSWER') $badgeClass = 'badge-warning';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                    </td>
                    <td class="text-center">{{ $callLog->formatted_duration }}</td>
                    <td>{{ $callLog->date ? $callLog->date->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $callLog->start_time ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
