<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Wise Sales Report</title>
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
        <h1>Course Wise Sales Report</h1>
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
                <td>{{ count($reports) }}</td>
            </tr>
            <tr>
                <td class="label">Total Sales Count:</td>
                <td>{{ number_format(collect($reports)->sum('sales_count')) }}</td>
            </tr>
            <tr>
                <td class="label">Total Sale Amount:</td>
                <td>₹{{ number_format(round(collect($reports)->sum('total_sale_amount'))) }}</td>
            </tr>
            <tr>
                <td class="label">Total Received Amount:</td>
                <td>₹{{ number_format(round(collect($reports)->sum('received_amount'))) }}</td>
            </tr>
        </table>
    </div>

    @if(count($reports) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Course Name</th>
                    <th style="width: 18%;" class="text-center">Total Sales Count<br><small>(Converted Leads)</small></th>
                    <th style="width: 18%;" class="text-end">Total Sale Amount<br><small>(Invoice Total)</small></th>
                    <th style="width: 19%;" class="text-end">Received Amount<br><small>(Payments Collected & Approved)</small></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $index => $report)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $report['course']->title }}</strong>
                            @if($report['course']->code)
                                <br><small style="color: #666;">Code: {{ $report['course']->code }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($report['sales_count']) }}</td>
                        <td class="text-end">₹{{ number_format(round($report['total_sale_amount'])) }}</td>
                        <td class="text-end">₹{{ number_format(round($report['received_amount'])) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f5f5f5; font-weight: bold;">
                    <td colspan="2">Total</td>
                    <td class="text-center">{{ number_format(collect($reports)->sum('sales_count')) }}</td>
                    <td class="text-end">₹{{ number_format(round(collect($reports)->sum('total_sale_amount'))) }}</td>
                    <td class="text-end">₹{{ number_format(round(collect($reports)->sum('received_amount'))) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p style="color: #666; padding: 20px; text-align: center;">No data found for the selected filters.</p>
    @endif

    @if(count($reports) > 0)
        <div style="margin-top: 30px;">
            <h3 style="font-size: 14px; margin-bottom: 10px;">Summary Statistics</h3>
            <table>
                <tr>
                    <td class="label">Average Sales per Course:</td>
                    <td class="text-right">
                        {{ number_format(collect($reports)->sum('sales_count') / count($reports), 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Average Sale Amount per Course:</td>
                    <td class="text-right">
                        ₹{{ number_format(round(collect($reports)->sum('total_sale_amount') / count($reports))) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Average Received Amount per Course:</td>
                    <td class="text-right">
                        ₹{{ number_format(round(collect($reports)->sum('received_amount') / count($reports))) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Top Course by Sales Count:</td>
                    <td>
                        @php
                            $topSales = collect($reports)->sortByDesc('sales_count')->first();
                        @endphp
                        {{ $topSales['course']->title }} ({{ number_format($topSales['sales_count']) }} sales)
                    </td>
                </tr>
                <tr>
                    <td class="label">Top Course by Sale Amount:</td>
                    <td>
                        @php
                            $topAmount = collect($reports)->sortByDesc('total_sale_amount')->first();
                        @endphp
                        {{ $topAmount['course']->title }} (₹{{ number_format(round($topAmount['total_sale_amount'])) }})
                    </td>
                </tr>
            </table>
        </div>
    @endif
</body>
</html>

