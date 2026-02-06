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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
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
                    <h4>{{ $reports['telecaller']->sum('count') ?? 0 }}</h4>
                    <p>Total Leads</p>
                </div>
                <div class="summary-item">
                    <h4>{{ $reports['telecaller']->count() ?? 0 }}</h4>
                    <p>Active Telecallers</p>
                </div>
                <div class="summary-item">
                    <h4>{{ $reports['telecaller']->avg('count') ? round($reports['telecaller']->avg('count'), 1) : 0 }}</h4>
                    <p>Avg Leads per Telecaller</p>
                </div>
                <div class="summary-item">
                    <h4>{{ $reports['telecaller']->max('count') ?? 0 }}</h4>
                    <p>Highest Leads</p>
                </div>
            </div>
        </div>
    </div>

    <div class="telecaller-performance">
        <h3>Telecaller Performance</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Telecaller Name</th>
                    <th>Team</th>
                    <th>Total Leads</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports['telecaller'] as $index => $telecaller)
                    @php
                        $total = $reports['telecaller']->sum('count');
                        $percentage = $total > 0 ? round(($telecaller->count / $total) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
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
