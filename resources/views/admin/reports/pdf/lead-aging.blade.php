<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lead Aging Report</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            color: #007bff;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 3px 0;
            color: #666;
            font-size: 12px;
        }
        .summary {
            margin-bottom: 20px;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
            font-weight: bold;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-item {
            flex: 1;
            margin-right: 10px;
        }
        .summary-item:last-child {
            margin-right: 0;
        }
        .summary-label {
            font-weight: bold;
            color: #495057;
        }
        .summary-value {
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }
        td {
            font-size: 8px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7px;
            font-weight: bold;
            border-radius: 3px;
            color: white;
            text-align: center;
            min-width: 40px;
        }
        .badge-primary { background-color: #007bff; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-orange { background-color: #fd7e14; }
        .status-group {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .status-group h4 {
            margin: 0 0 8px 0;
            color: #333;
            background-color: #e9ecef;
            padding: 8px 12px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .aging-breakdown {
            margin: 8px 0;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 3px;
            font-size: 9px;
        }
        .aging-breakdown strong {
            color: #495057;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #6c757d;
        }
        .table-danger {
            background-color: #f8d7da;
        }
        .table-warning {
            background-color: #fff3cd;
        }
        .page-break {
            page-break-before: always;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .lead-title {
            word-wrap: break-word;
            word-break: break-word;
            max-width: 120px;
        }
        .phone-number {
            font-family: 'Courier New', monospace;
            font-size: 8px;
        }
        .status-badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 6px;
            border-radius: 2px;
            text-align: center;
            min-width: 30px;
        }
        .days-badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 6px;
            border-radius: 2px;
            text-align: center;
            min-width: 25px;
            font-weight: bold;
        }
        .category-badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 6px;
            border-radius: 2px;
            text-align: center;
            min-width: 35px;
        }
        .date-cell {
            font-size: 7px;
            white-space: nowrap;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        .summary-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px;
            text-align: center;
        }
        .summary-card-title {
            font-size: 8px;
            color: #6c757d;
            margin-bottom: 4px;
            font-weight: bold;
        }
        .summary-card-value {
            font-size: 14px;
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lead Aging Report</h1>
        <p>Date Range: {{ $fromDate }} to {{ $toDate }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary">
        <h3>Overall Summary</h3>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-card-title">Total Leads</div>
                <div class="summary-card-value">{{ $agingData['summary']['total_leads'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-title">Average Days in Status</div>
                <div class="summary-card-value">{{ $agingData['summary']['avg_days_in_status'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-title">Maximum Days in Status</div>
                <div class="summary-card-value">{{ $agingData['summary']['max_days_in_status'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-title">Leads Over 7 Days</div>
                <div class="summary-card-value">{{ $agingData['summary']['leads_over_7_days'] }} ({{ $agingData['summary']['over_7_days_percentage'] }}%)</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-title">Leads Over 14 Days</div>
                <div class="summary-card-value">{{ $agingData['summary']['leads_over_14_days'] }} ({{ $agingData['summary']['over_14_days_percentage'] }}%)</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-title">Report Period</div>
                <div class="summary-card-value">{{ date('M d, Y', strtotime($fromDate)) }} - {{ date('M d, Y', strtotime($toDate)) }}</div>
            </div>
        </div>
    </div>

    @foreach($agingData['status_groups'] as $index => $statusGroup)
    @if($index > 0)
    <div class="page-break"></div>
    @endif
    <div class="status-group">
        <h4>{{ $statusGroup['status_name'] }} ({{ $statusGroup['total_leads'] }} leads)</h4>
        
        <div class="aging-breakdown">
            <strong>Aging Breakdown:</strong>
            Fresh (0-1 days): {{ $statusGroup['aging_breakdown']['fresh'] }} | 
            Recent (2-3 days): {{ $statusGroup['aging_breakdown']['recent'] }} | 
            Moderate (4-7 days): {{ $statusGroup['aging_breakdown']['moderate'] }} | 
            Old (8-14 days): {{ $statusGroup['aging_breakdown']['old'] }} | 
            Very Old (15+ days): {{ $statusGroup['aging_breakdown']['very_old'] }}
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Lead Name</th>
                    <th style="width: 12%;">Phone</th>
                    <th style="width: 10%;">Source</th>
                    <th style="width: 12%;">Telecaller</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 8%;">Days</th>
                    <th style="width: 10%;">Category</th>
                    <th style="width: 9%;">First Activity</th>
                    <th style="width: 9%;">Last Activity</th>
                </tr>
            </thead>
            <tbody>
                @php $serialNumber = 1; @endphp
                @forelse($statusGroup['leads'] as $lead)
                <tr class="{{ $lead->days_in_current_status > 14 ? 'table-danger' : ($lead->days_in_current_status > 7 ? 'table-warning' : '') }}">
                    <td class="text-center">{{ $serialNumber++ }}</td>
                    <td class="lead-title">{{ Str::limit($lead->title, 18) }}</td>
                    <td class="phone-number">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</td>
                    <td>
                        <span class="status-badge badge-primary">{{ Str::limit($lead->source_name ?: 'Unknown', 10) }}</span>
                    </td>
                    <td>
                        @if($lead->telecaller_name)
                            <span class="status-badge badge-info">{{ Str::limit($lead->telecaller_name, 12) }}</span>
                        @else
                            <span class="status-badge badge-secondary">Unassigned</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge badge-primary">{{ Str::limit($lead->status_name, 10) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="days-badge {{ $lead->days_in_current_status > 14 ? 'badge-danger' : ($lead->days_in_current_status > 7 ? 'badge-warning' : 'badge-success') }}">
                            {{ $lead->days_in_current_status }}
                        </span>
                    </td>
                    <td>
                        @if($lead->aging_category == 'Fresh (0-1 days)')
                            <span class="category-badge badge-success">Fresh</span>
                        @elseif($lead->aging_category == 'Recent (2-3 days)')
                            <span class="category-badge badge-info">Recent</span>
                        @elseif($lead->aging_category == 'Moderate (4-7 days)')
                            <span class="category-badge badge-warning">Moderate</span>
                        @elseif($lead->aging_category == 'Old (8-14 days)')
                            <span class="category-badge badge-orange">Old</span>
                        @else
                            <span class="category-badge badge-danger">Very Old</span>
                        @endif
                    </td>
                    <td class="text-center date-cell">{{ $lead->first_activity_in_status->format('d-m-Y h:i A') }}</td>
                    <td class="text-center date-cell">{{ $lead->last_activity_date->format('d-m-Y h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="no-data">No leads in this status.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endforeach
</body>
</html>
