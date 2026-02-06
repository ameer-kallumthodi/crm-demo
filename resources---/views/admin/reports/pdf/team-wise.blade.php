<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Team-Wise Detailed Report</title>
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
            margin: 5px 0;
            font-size: 14px;
        }
        .team-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .team-header {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }
        .team-header h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        .summary-item h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #666;
        }
        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
            color: #333;
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
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .segment-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .segment-item {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        .segment-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            margin-bottom: 10px;
        }
        .segment-card.top-performers {
            background-color: #d4edda;
        }
        .segment-card.underperformers {
            background-color: #f8d7da;
        }
        .segment-card.new-joiners {
            background-color: #fff3cd;
        }
        .segment-card.experienced {
            background-color: #d1ecf1;
        }
        .segment-card h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .segment-card .count {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .time-analysis {
            margin-bottom: 20px;
        }
        .time-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
        }
        .time-card.morning {
            background-color: #fff3cd;
        }
        .time-card.evening {
            background-color: #d1ecf1;
        }
        .time-card h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .time-stats {
            display: table;
            width: 100%;
        }
        .time-stat {
            display: table-cell;
            width: 33.33%;
            text-align: center;
        }
        .time-stat .label {
            font-size: 12px;
            color: #666;
        }
        .time-stat .value {
            font-size: 16px;
            font-weight: bold;
        }
        .product-region {
            display: table;
            width: 100%;
        }
        .product-region .section {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        .section h4 {
            margin: 0 0 10px 0;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .small-table {
            font-size: 10px;
        }
        .small-table th,
        .small-table td {
            padding: 4px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Team-Wise Detailed Report</h1>
        <p>Report Period: {{ $fromDate }} to {{ $toDate }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @foreach($reportData as $index => $teamData)
        <div class="team-section {{ $index > 0 ? 'page-break' : '' }}">
            <div class="team-header">
                <h2>{{ $teamData['team']->name }}</h2>
                <p><strong>Team Lead:</strong> {{ $teamData['team']->teamLead ? $teamData['team']->teamLead->name : 'N/A' }}</p>
            </div>

            <!-- Team Summary -->
            <div class="summary-grid">
                <div class="summary-item">
                    <h4>Total Members</h4>
                    <div class="number">{{ $teamData['team']->total_members }}</div>
                </div>
                <div class="summary-item">
                    <h4>Active Members</h4>
                    <div class="number">{{ $teamData['team']->active_members }}</div>
                </div>
                <div class="summary-item">
                    <h4>Total Leads</h4>
                    <div class="number">{{ $teamData['total_leads'] }}</div>
                </div>
                <div class="summary-item">
                    <h4>Conversion Rate</h4>
                    <div class="number">{{ $teamData['conversion_rate'] }}%</div>
                </div>
            </div>

            <!-- Individual Telecaller Performance -->
            <h3>Individual Telecaller Performance</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Experience Level</th>
                        <th>Total Leads</th>
                        <th>Converted Leads</th>
                        <th>Conversion Rate</th>
                        <th>Total Calls</th>
                        <th>Avg Call Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teamData['telecaller_performance'] as $perf)
                        <tr>
                            <td>{{ $perf['user']->name }}</td>
                            <td>{{ $perf['experience_level'] }}</td>
                            <td>{{ $perf['total_leads'] }}</td>
                            <td>{{ $perf['converted_leads'] }}</td>
                            <td>{{ $perf['conversion_rate'] }}%</td>
                            <td>{{ $perf['total_calls'] }}</td>
                            <td>{{ $perf['avg_call_duration'] }} min</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Segment Analysis -->
            <div class="segment-grid">
                <div class="segment-item">
                    <h3>Performance Segments</h3>
                    <div class="segment-card top-performers">
                        <h4>Top Performers</h4>
                        <div class="count">{{ count($teamData['segments']['top_performers']) }}</div>
                        <div>Avg Conversion: {{ count($teamData['segments']['top_performers']) > 0 ? round(collect($teamData['segments']['top_performers'])->avg('conversion_rate'), 2) : 0 }}%</div>
                    </div>
                    <div class="segment-card underperformers">
                        <h4>Underperformers</h4>
                        <div class="count">{{ count($teamData['segments']['underperformers']) }}</div>
                        <div>Avg Conversion: {{ count($teamData['segments']['underperformers']) > 0 ? round(collect($teamData['segments']['underperformers'])->avg('conversion_rate'), 2) : 0 }}%</div>
                    </div>
                </div>
                <div class="segment-item">
                    <h3>Experience Segments</h3>
                    <div class="segment-card new-joiners">
                        <h4>New Joiners</h4>
                        <div class="count">{{ count($teamData['segments']['new_joiners']) }}</div>
                        <div>Avg Conversion: {{ count($teamData['segments']['new_joiners']) > 0 ? round(collect($teamData['segments']['new_joiners'])->avg('conversion_rate'), 2) : 0 }}%</div>
                    </div>
                    <div class="segment-card experienced">
                        <h4>Experienced</h4>
                        <div class="count">{{ count($teamData['segments']['experienced']) }}</div>
                        <div>Avg Conversion: {{ count($teamData['segments']['experienced']) > 0 ? round(collect($teamData['segments']['experienced'])->avg('conversion_rate'), 2) : 0 }}%</div>
                    </div>
                </div>
            </div>

            <!-- Time-Based Analysis -->
            <div class="time-analysis">
                <h3>Time Slot Performance</h3>
                <div class="time-card morning">
                    <h4>Morning Performance (6 AM - 12 PM)</h4>
                    <div class="time-stats">
                        <div class="time-stat">
                            <div class="label">Leads</div>
                            <div class="value">{{ $teamData['time_analysis']['morning']['leads'] }}</div>
                        </div>
                        <div class="time-stat">
                            <div class="label">Conversions</div>
                            <div class="value">{{ $teamData['time_analysis']['morning']['conversions'] }}</div>
                        </div>
                        <div class="time-stat">
                            <div class="label">Conversion Rate</div>
                            <div class="value">{{ $teamData['time_analysis']['morning']['conversion_rate'] }}%</div>
                        </div>
                    </div>
                </div>
                <div class="time-card evening">
                    <h4>Evening Performance (6 PM - 11 PM)</h4>
                    <div class="time-stats">
                        <div class="time-stat">
                            <div class="label">Leads</div>
                            <div class="value">{{ $teamData['time_analysis']['evening']['leads'] }}</div>
                        </div>
                        <div class="time-stat">
                            <div class="label">Conversions</div>
                            <div class="value">{{ $teamData['time_analysis']['evening']['conversions'] }}</div>
                        </div>
                        <div class="time-stat">
                            <div class="label">Conversion Rate</div>
                            <div class="value">{{ $teamData['time_analysis']['evening']['conversion_rate'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product/Region Analysis -->
            <div class="product-region">
                <div class="section">
                    <h4>Product Performance</h4>
                    <table class="small-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Leads</th>
                                <th>Conversions</th>
                                <th>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teamData['product_region_analysis']['products'] as $product)
                                <tr>
                                    <td>{{ $product->course_name }}</td>
                                    <td>{{ $product->total_leads }}</td>
                                    <td>{{ $product->conversions }}</td>
                                    <td>{{ $product->total_leads > 0 ? round(($product->conversions / $product->total_leads) * 100, 2) : 0 }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="section">
                    <h4>Region Performance</h4>
                    <table class="small-table">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Leads</th>
                                <th>Conversions</th>
                                <th>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teamData['product_region_analysis']['regions'] as $region)
                                <tr>
                                    <td>{{ $region->country_name }}</td>
                                    <td>{{ $region->total_leads }}</td>
                                    <td>{{ $region->conversions }}</td>
                                    <td>{{ $region->total_leads > 0 ? round(($region->conversions / $region->total_leads) * 100, 2) : 0 }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>
