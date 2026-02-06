<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Post Sales Month Ways Report</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ public_path("fonts/DejaVuSans.ttf") }}') format('truetype');
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
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
            width: 200px;
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
        .text-end {
            text-align: right;
        }
        .user-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .user-header {
            background-color: #f0f0f0;
            padding: 10px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Post Sales Month Ways Report</h1>
        <p>Generated on {{ $generatedAt }}</p>
    </div>

    <div class="report-info">
        <table>
            <tr>
                <td class="label">Date Range:</td>
                <td>{{ $fromDate }} to {{ $toDate }}</td>
            </tr>
            <tr>
                <td class="label">Total Post Sales Users:</td>
                <td>{{ count($reports) }}</td>
            </tr>
        </table>
    </div>

    @if(count($reports) > 0)
        @foreach($reports as $report)
            <div class="user-section">
                <div class="user-header">
                    {{ $report['user']->name }}
                    @if($report['user']->is_head == 1)
                        <span style="background-color: #007bff; color: white; padding: 2px 8px; border-radius: 3px; font-size: 10px; margin-left: 10px;">Head</span>
                    @endif
                </div>
                
                @if(count($report['data']) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50%;">Course Name</th>
                                <th style="width: 25%;" class="text-center">Student Count</th>
                                <th style="width: 25%;" class="text-end">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['data'] as $row)
                                <tr>
                                    <td>{{ $row['course_name'] }}</td>
                                    <td class="text-center">{{ number_format($row['student_count']) }}</td>
                                    <td class="text-end">₹{{ number_format($row['total_amount'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f5f5f5; font-weight: bold;">
                                <td>Total</td>
                                <td class="text-center">{{ number_format(collect($report['data'])->sum('student_count')) }}</td>
                                <td class="text-end">₹{{ number_format(collect($report['data'])->sum('total_amount'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                @else
                    <p style="color: #666; padding: 10px;">No data found for this user in the selected period.</p>
                @endif
            </div>
        @endforeach
    @else
        <p style="color: #666; padding: 20px; text-align: center;">No post sales users found.</p>
    @endif
</body>
</html>

