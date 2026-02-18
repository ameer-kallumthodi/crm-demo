<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Team Registration Details - {{ $team->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1e3c72;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
            background: #fff;
        }
        .section-title {
            background-color: #f0f4f8;
            color: #1e3c72;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            border-left: 4px solid #1e3c72;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .row {
            width: 100%;
            margin-bottom: 10px;
        }
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        .col {
            float: left;
            width: 50%;
            box-sizing: border-box;
            padding-right: 15px;
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 4px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .value {
            color: #000;
            font-size: 13px;
            border-bottom: 1px solid #eee;
            padding-bottom: 2px;
            display: block;
            min-height: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #1e3c72;
            color: #fff;
            font-size: 12px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Team Registration Details</h1>
        <p>Generated on {{ date('d M Y, h:i A') }}</p>
    </div>

    <!-- Institutional Legal Details -->
    <div class="section">
        <div class="section-title">Institutional Legal Details</div>
        <div class="row">
            <div class="col">
                <span class="label">Legal Name</span>
                <span class="value">{{ $detail->legal_name ?? 'N/A' }}</span>
            </div>
            <div class="col">
                <span class="label">Institution Category</span>
                <span class="value">{{ $detail->institution_category ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <span class="label">Registration Number</span>
                <span class="value">{{ $detail->registration_number ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Registered Address -->
    <div class="section">
        <div class="section-title">Registered Address</div>
        <div class="row">
            <div class="col">
                <span class="label">Building Name</span>
                <span class="value">{{ $detail->building_name ?? 'N/A' }}</span>
            </div>
            <div class="col">
                <span class="label">Street Name</span>
                <span class="value">{{ $detail->street_name ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <span class="label">Locality</span>
                <span class="value">{{ $detail->locality_name ?? 'N/A' }}</span>
            </div>
            <div class="col">
                <span class="label">City</span>
                <span class="value">{{ $detail->city ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <span class="label">District</span>
                <span class="value">{{ $detail->district ?? 'N/A' }}</span>
            </div>
            <div class="col">
                <span class="label">State</span>
                <span class="value">{{ $detail->state ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <span class="label">PIN Code</span>
                <span class="value">{{ $detail->pin_code ?? 'N/A' }}</span>
            </div>
            <div class="col">
                <span class="label">Country</span>
                <span class="value">{{ $detail->country ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Communication Officer Details -->
    <div class="section">
        <div class="section-title">Communication Officer Details</div>
        <div class="row">
            <div class="col">
                <span class="label">Name</span>
                <span class="value">{{ $detail->comm_officer_name ?? 'N/A' }}</span>
            </div>
            <div class="col">
                <span class="label">Official Email</span>
                <span class="value">{{ $detail->comm_officer_email ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <span class="label">Mobile Number</span>
                <span class="value">{{ $detail->comm_officer_mobile ?? 'N/A' }}</span>
            </div>
            <div class="col">
                <span class="label">WhatsApp Number</span>
                <span class="value">{{ $detail->comm_officer_whatsapp ?? 'N/A' }}</span>
            </div>
        </div>
        @if($detail->comm_officer_alt_mobile)
        <div class="row">
            <div class="col">
                <span class="label">Alternate Mobile</span>
                <span class="value">{{ $detail->comm_officer_alt_mobile }}</span>
            </div>
        </div>
        @endif
    </div>

    <!-- Authorized Stakeholder Details -->
    <div class="section">
        <div class="section-title">Authorized Stakeholder Details</div>
        <div class="row">
            <div class="col">
                <span class="label">Name</span>
                <span class="value">{{ $detail->auth_person_name ?? 'N/A' }}</span>
            </div>
            <div class="col">
                <span class="label">Designation</span>
                <span class="value">{{ $detail->auth_person_designation ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <span class="label">Mobile Number</span>
                <span class="value">{{ $detail->auth_person_mobile ?? 'N/A' }}</span>
            </div>
            <div class="col">
                <span class="label">Email ID</span>
                <span class="value">{{ $detail->auth_person_email ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Interested Courses -->
    <div class="section">
        <div class="section-title">Interested Courses & Delivery Structures</div>
        @if(count($interestedCourses) > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 40%">Course</th>
                        <th style="width: 60%">Delivery Structures</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($interestedCourses as $item)
                        <tr>
                            <td><strong>{{ $item['course'] }}</strong></td>
                            <td>
                                @if(count($item['structures']) > 0)
                                    <ul style="margin: 0; padding-left: 20px;">
                                        @foreach($item['structures'] as $structure)
                                            <li>{{ $structure }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span style="color: #999; font-style: italic;">No specific structures selected</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="padding: 10px; color: #666; font-style: italic;">No course preferences found.</p>
        @endif
    </div>

    <div class="footer">
        <p>This document was automatically generated by Skill Park CRM. &copy; {{ date('Y') }} All rights reserved.</p>
    </div>
</body>
</html>
