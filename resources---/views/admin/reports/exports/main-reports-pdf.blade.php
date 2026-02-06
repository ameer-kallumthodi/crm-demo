<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportType }} - {{ $fromDate }} to {{ $toDate }}</title>
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
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 30px;
        }
        .summary h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-row {
            display: table-row;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        .summary-item h4 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .summary-item p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .report-section {
            margin-bottom: 30px;
        }
        .report-section h3 {
            color: #333;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
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
        <h1>{{ $reportType }}</h1>
        <p>Report Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</p>
        <p>Generated on: {{ $generatedAt }}</p>
    </div>

    <div class="summary">
        <h3>Report Summary</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-item">
                    <h4>{{ $reports['lead_status']->sum('count') }}</h4>
                    <p>Total Leads</p>
                </div>
                <div class="summary-item">
                    <h4>{{ $reports['lead_status']->where('title', 'Converted')->first()->count ?? 0 }}</h4>
                    <p>Converted Leads</p>
                </div>
                <div class="summary-item">
                    <h4>{{ $reports['lead_source']->count() }}</h4>
                    <p>Lead Sources</p>
                </div>
                <div class="summary-item">
                    <h4>{{ $reports['team']->count() }}</h4>
                    <p>Active Teams</p>
                </div>
            </div>
        </div>
    </div>

    <div class="report-section">
        <h3>Lead Status Report</h3>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports['lead_status'] as $status)
                    @php
                        $total = $reports['lead_status']->sum('count');
                        $percentage = $total > 0 ? round(($status->count / $total) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $status->title }}</td>
                        <td>{{ $status->count }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="report-section">
        <h3>Lead Source Report</h3>
        <table>
            <thead>
                <tr>
                    <th>Source</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports['lead_source'] as $source)
                    @php
                        $total = $reports['lead_source']->sum('count');
                        $percentage = $total > 0 ? round(($source->count / $total) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $source->title }}</td>
                        <td>{{ $source->count }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="report-section">
        <h3>Team Report</h3>
        <table>
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports['team'] as $team)
                    @php
                        $total = $reports['team']->sum('count');
                        $percentage = $total > 0 ? round(($team->count / $total) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $team->title }}</td>
                        <td>{{ $team->count }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="report-section">
        <h3>Telecaller Report</h3>
        <table>
            <thead>
                <tr>
                    <th>Telecaller</th>
                    <th>Team</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports['telecaller'] as $telecaller)
                    @php
                        $total = $reports['telecaller']->sum('count');
                        $percentage = $total > 0 ? round(($telecaller->count / $total) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $telecaller->name }}</td>
                        <td>{{ $telecaller->team_name ?? 'No Team' }}</td>
                        <td>{{ $telecaller->count }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report was generated automatically by the CRM System</p>
    </div>
</body>
</html>
