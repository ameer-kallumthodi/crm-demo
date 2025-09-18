<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lead Stage Movement Report</title>
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
        .status-group {
            margin-bottom: 30px;
        }
        .status-group h4 {
            margin: 0 0 10px 0;
            color: #333;
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lead Stage Movement Report</h1>
        <p>Date Range: {{ $fromDate }} to {{ $toDate }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary">
        <h3>Overall Summary</h3>
        <p><strong>Total Leads:</strong> {{ $stageData['summary']['total_leads'] }}</p>
        <p><strong>Stuck Leads:</strong> {{ $stageData['summary']['total_stuck_leads'] }} ({{ $stageData['summary']['stuck_percentage'] }}%)</p>
        <p><strong>Follow-up Leads:</strong> {{ $stageData['summary']['total_follow_up_leads'] }} ({{ $stageData['summary']['follow_up_percentage'] }}%)</p>
        <p><strong>Converted Leads:</strong> {{ $stageData['summary']['total_converted_leads'] }} ({{ $stageData['summary']['conversion_percentage'] }}%)</p>
    </div>

    @foreach($stageData['status_groups'] as $statusGroup)
    <div class="status-group">
        <h4>{{ $statusGroup['status_name'] }} ({{ $statusGroup['total_leads'] }} leads, {{ $statusGroup['stuck_leads'] }} stuck)</h4>
        
        <table>
            <thead>
                <tr>
                    <th>SL No</th>
                    <th>Lead ID</th>
                    <th>Lead Title</th>
                    <th>Phone</th>
                    <th>Source</th>
                    <th>Telecaller</th>
                    <th>Days Since Last Update</th>
                    <th>Last Activity Date</th>
                    <th>Is Stuck</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @php $serialNumber = 1; @endphp
                @forelse($statusGroup['leads'] as $lead)
                <tr>
                    <td>{{ $serialNumber++ }}</td>
                    <td>{{ $lead->id }}</td>
                    <td>{{ $lead->title }}</td>
                    <td>{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</td>
                    <td>{{ $lead->source_name ?: 'Unknown' }}</td>
                    <td>{{ $lead->telecaller_name ?: 'Unassigned' }}</td>
                    <td>{{ $lead->days_since_last_update }}</td>
                    <td>{{ $lead->last_activity_date->format('d-m-Y h:i A') }}</td>
                    <td>{{ $lead->is_stuck ? 'Yes' : 'No' }}</td>
                    <td>{{ $lead->created_at->format('d-m-Y h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center;">No leads in this status.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endforeach
</body>
</html>
