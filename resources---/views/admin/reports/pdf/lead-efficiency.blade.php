<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lead Source Efficiency Report</title>
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
            background-color: #f5f5f5;
            font-weight: bold;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Lead Source Efficiency Report</h1>
        <p>Date Range: {{ $fromDate }} to {{ $toDate }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Lead Source</th>
                <th>Total Leads</th>
                <th>Converted Leads</th>
                <th>Follow-up Leads</th>
                <th>Interested Leads</th>
                <th>Converted Status Leads</th>
                <th>Conversion Rate (%)</th>
                <th>Follow-up Rate (%)</th>
                <th>Interested Rate (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($efficiencyData as $item)
            <tr>
                <td>{{ $item->source_name }}</td>
                <td>{{ $item->total_leads }}</td>
                <td>{{ $item->converted_leads }}</td>
                <td>{{ $item->follow_up_leads }}</td>
                <td>{{ $item->interested_leads }}</td>
                <td>{{ $item->converted_status_leads }}</td>
                <td>{{ $item->conversion_rate }}%</td>
                <td>{{ $item->follow_up_rate }}%</td>
                <td>{{ $item->interested_rate }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align: center;">No data available for the selected date range.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary</h3>
        <p><strong>Total Lead Sources:</strong> {{ $efficiencyData->count() }}</p>
        <p><strong>Total Leads:</strong> {{ $efficiencyData->sum('total_leads') }}</p>
        <p><strong>Total Converted:</strong> {{ $efficiencyData->sum('converted_leads') }}</p>
        <p><strong>Overall Conversion Rate:</strong> 
            {{ $efficiencyData->sum('total_leads') > 0 ? round(($efficiencyData->sum('converted_leads') / $efficiencyData->sum('total_leads')) * 100, 2) : 0 }}%
        </p>
    </div>
</body>
</html>
