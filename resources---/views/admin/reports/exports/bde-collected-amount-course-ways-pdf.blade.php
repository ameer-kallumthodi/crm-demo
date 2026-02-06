<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BDE Collected Amount Course Ways Report</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>BDE Collected Amount Course Ways Report</h1>
        <p>Generated on {{ $generatedAt }}</p>
    </div>

    <div class="report-info">
        <table>
            @if($selectedUser)
                <tr>
                    <td class="label">Post Sales User:</td>
                    <td>{{ $selectedUser->name }}</td>
                </tr>
            @endif
            @if($fromDate || $toDate)
                <tr>
                    <td class="label">Date Range:</td>
                    <td>
                        @if($fromDate)
                            From {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
                        @endif
                        @if($toDate)
                            @if($fromDate) - @endif
                            To {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                        @endif
                        @if(!$fromDate && !$toDate)
                            All dates
                        @endif
                    </td>
                </tr>
            @endif
            @if(count($reportData) > 0)
                <tr>
                    <td class="label">Total Courses:</td>
                    <td>{{ count($reportData) }}</td>
                </tr>
                <tr>
                    <td class="label">Grand Total Students:</td>
                    <td>{{ number_format($grandTotalStudents) }}</td>
                </tr>
                <tr>
                    <td class="label">Grand Total Amount:</td>
                    <td>₹{{ number_format($grandTotal, 2) }}</td>
                </tr>
            @endif
        </table>
    </div>

    @if(!$selectedUser)
        <p style="color: #666; padding: 20px; text-align: center;">Please select a Post Sales user to view the report.</p>
    @elseif(count($reportData) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Course Name</th>
                    <th style="width: 25%;" class="text-center">Student Count</th>
                    <th style="width: 25%;" class="text-end">Amount Collected</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                    <tr>
                        <td>{{ $row['course_name'] }}</td>
                        <td class="text-center">{{ number_format($row['student_count']) }}</td>
                        <td class="text-end">₹{{ number_format($row['total_amount'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f5f5f5; font-weight: bold;">
                    <td>Grand Total</td>
                    <td class="text-center">{{ number_format($grandTotalStudents) }}</td>
                    <td class="text-end">₹{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p style="color: #666; padding: 20px; text-align: center;">No approved payments found for the selected Post Sales user{{ ($fromDate || $toDate) ? ' and selected date range' : '' }}.</p>
    @endif
</body>
</html>

