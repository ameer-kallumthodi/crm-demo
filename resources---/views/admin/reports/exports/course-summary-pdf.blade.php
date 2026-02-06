<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course-wise Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
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
        .report-info {
            margin-bottom: 20px;
        }
        .report-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-info td {
            padding: 5px 0;
            border: none;
        }
        .report-info .label {
            font-weight: bold;
            width: 150px;
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
            text-align: center;
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
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        .conversion-rate {
            font-weight: bold;
        }
        .conversion-rate.high {
            color: #28a745;
        }
        .conversion-rate.medium {
            color: #ffc107;
        }
        .conversion-rate.low {
            color: #dc3545;
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
        <h1>Course-wise Summary Report</h1>
        <p>Generated on {{ $generatedAt }}</p>
    </div>

    <div class="report-info">
        <table>
            <tr>
                <td class="label">Date Range:</td>
                <td>{{ $fromDate }} to {{ $toDate }}</td>
            </tr>
            <tr>
                <td class="label">Total Courses:</td>
                <td>{{ count($courseSummary) }}</td>
            </tr>
            <tr>
                <td class="label">Total Leads:</td>
                <td>{{ collect($courseSummary)->sum('total_leads') }}</td>
            </tr>
            <tr>
                <td class="label">Total Converted:</td>
                <td>{{ collect($courseSummary)->sum('converted_leads') }}</td>
            </tr>
            <tr>
                <td class="label">Total Follow-up:</td>
                <td>{{ collect($courseSummary)->sum('followup_leads') }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Course Name</th>
                <th style="width: 12%;" class="text-center">Total Leads</th>
                <th style="width: 12%;" class="text-center">Converted Leads</th>
                <th style="width: 12%;" class="text-center">Follow-up Leads</th>
                <th style="width: 12%;" class="text-center">Other Status</th>
                <th style="width: 12%;" class="text-center">Conversion Rate</th>
                <th style="width: 10%;" class="text-center">Performance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courseSummary as $index => $course)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $course['course_name'] }}</td>
                <td class="text-center">
                    <span class="badge badge-primary">{{ $course['total_leads'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-success">{{ $course['converted_leads'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-info">{{ $course['followup_leads'] }}</span>
                </td>
                <td class="text-center">
                    <span class="badge badge-secondary">{{ $course['other_leads'] }}</span>
                </td>
                <td class="text-center">
                    <span class="conversion-rate 
                        @if($course['conversion_rate'] >= 20) high
                        @elseif($course['conversion_rate'] >= 10) medium
                        @else low
                        @endif">
                        {{ $course['conversion_rate'] }}%
                    </span>
                </td>
                <td class="text-center">
                    @if($course['conversion_rate'] >= 20)
                        <span style="color: #28a745;">Excellent</span>
                    @elseif($course['conversion_rate'] >= 10)
                        <span style="color: #ffc107;">Good</span>
                    @else
                        <span style="color: #dc3545;">Needs Improvement</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if(count($courseSummary) > 0)
    <div style="margin-top: 30px;">
        <h3>Summary Statistics</h3>
        <table>
            <tr>
                <td class="label">Average Conversion Rate:</td>
                <td class="text-right">
                    {{ number_format(collect($courseSummary)->avg('conversion_rate'), 2) }}%
                </td>
            </tr>
            <tr>
                <td class="label">Highest Converting Course:</td>
                <td>
                    @php
                        $highest = collect($courseSummary)->sortByDesc('conversion_rate')->first();
                    @endphp
                    {{ $highest['course_name'] }} ({{ $highest['conversion_rate'] }}%)
                </td>
            </tr>
            <tr>
                <td class="label">Lowest Converting Course:</td>
                <td>
                    @php
                        $lowest = collect($courseSummary)->sortBy('conversion_rate')->first();
                    @endphp
                    {{ $lowest['course_name'] }} ({{ $lowest['conversion_rate'] }}%)
                </td>
            </tr>
            <tr>
                <td class="label">Courses with High Conversion (≥20%):</td>
                <td class="text-right">
                    {{ collect($courseSummary)->where('conversion_rate', '>=', 20)->count() }}
                </td>
            </tr>
            <tr>
                <td class="label">Courses with Low Conversion (<10%):</td>
                <td class="text-right">
                    {{ collect($courseSummary)->where('conversion_rate', '<', 10)->count() }}
                </td>
            </tr>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the CRM System</p>
        <p>For any questions or clarifications, please contact the system administrator</p>
    </div>
</body>
</html>
