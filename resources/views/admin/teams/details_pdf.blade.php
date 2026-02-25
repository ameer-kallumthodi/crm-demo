<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>TEAM REGISTRATION DETAILS - {{ $team->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .doc-header {
            padding-bottom: 8px;
            margin-bottom: 0;
            border-bottom: 1px solid #ccc;
        }

        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .doc-header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .doc-header-table td:first-child {
            width: 15%;
            padding: 0;
        }

        .doc-header-table td:nth-child(2) {
            text-align: center;
            width: 70%;
        }

        .doc-header-table td:last-child {
            text-align: right;
            width: 15%;
        }

        .doc-header .logo-img {
            max-height: 60px;
            width: auto;
            display: block;
            margin-left: auto;
        }

        .doc-header .org-name {
            font-size: 22px;
            font-weight: bold;
            color: #5F26B9;
            margin: 0;
            text-transform: uppercase;
        }

        .doc-header .tagline {
            font-size: 9px;
            color: #000;
            margin: 4px 0 0;
        }

        .contact-bar {
            border-bottom: 1px solid #ddd;
            padding: 6px 0;
            margin-bottom: 12px;
            font-size: 10px;
            color: #555;
            width: 100%;
        }

        .contact-bar table {
            width: 100%;
            border-collapse: collapse;
        }

        .contact-bar td {
            padding: 0;
            vertical-align: middle;
            color: #555;
        }

        .contact-bar td:first-child {
            text-align: left;
            width: 33%;
        }

        .contact-bar td:nth-child(2) {
            text-align: center;
            width: 34%;
        }

        .contact-bar td:last-child {
            text-align: right;
            width: 33%;
        }

        .section-bar {
            background:rgb(110, 52, 202);
            color: #fff;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin: 0 0 8px 0;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .info-table tr {
            border-bottom: 1px dotted #999;
        }

        .info-table td {
            padding: 5px 8px;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 38%;
            font-weight: bold;
            color: #555;
        }

        .info-table .value {
            font-weight: normal;
            color: #000;
        }

        /* GENERAL INFORMATION: 2 columns per row (Label | Value | Label | Value) */
        .info-table.info-table-2col td {
            width: 25%;
        }
        .info-table.info-table-2col td:nth-child(1),
        .info-table.info-table-2col td:nth-child(3) {
            font-weight: bold;
            color: #555;
            width: 22%;
        }
        .info-table.info-table-2col td:nth-child(2),
        .info-table.info-table-2col td:nth-child(4) {
            font-weight: normal;
            color: #000;
            width: 28%;
        }

        /* Banking values: allow long text to wrap so they show properly in PDF */
        .info-table .bank-value {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .team-details-row {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .team-details-row td {
            padding: 6px 12px;
            vertical-align: top;
            border-bottom: 1px dotted #999;
            font-size: 11px;
        }

        .team-details-row .label {
            color: #555;
            font-weight: bold;
        }

        .team-details-row .val {
            color: #000;
            font-weight: bold;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            border: 1px solid #333;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #333;
            padding: 8px 10px;
            text-align: left;
        }

        .data-table th {
            background: #e0e0e0;
            font-size: 11px;
            text-transform: uppercase;
        }

        .data-table td {
            font-size: 10px;
        }

        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 28px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

    <!-- Header: NATDEMY centered on page, logo on right (3 columns: spacer | title | logo) -->
    <div class="doc-header">
        <table class="doc-header-table">
            <tr>
                <td></td>
                <td>
                    <h1 class="org-name">Natdemy</h1>
                    <p class="tagline">Team Registration & Partner Management</p>
                </td>
                <td>
                    @if(file_exists(public_path('images/natdemy-logo.png')))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/natdemy-logo.png'))) }}" alt="Natdemy" class="logo-img" />
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Contact bar -->
    <div class="contact-bar">
        <table>
            <tr>
                <td>Phone: {{ config('app.contact_phone', '+91 9899108107') }}</td>
                <td>Website: {{ config('app.contact_website', 'www.natdemy.com') }}</td>
                <td>Email: {{ config('app.contact_email', 'support@natdemy.com') }}</td>
            </tr>
        </table>
    </div>

    <!-- TEAM REGISTRATION DETAILS (full width, left to right) -->
    <div class="section-bar">TEAM REGISTRATION DETAILS</div>
    <table class="team-details-row" style="width: 100%;">
        <tr>
            <td style="width: 25%;"><span class="label">Team Name</span><br><span class="val">{{ $team->name ?? '—' }}</span></td>
            <td style="width: 25%;"><span class="label">Generated On</span><br><span class="val">{{ date('d M Y') }}</span></td>
            <td style="width: 25%;"><span class="label">Partner ID</span><br><span class="val">{{ $detail->b2b_partner_id ?? '—' }}</span></td>
            <td style="width: 25%;"><span class="label">Category</span><br><span class="val">{{ $detail->institution_category ?? '—' }}</span></td>
        </tr>
    </table>

    <!-- GENERAL INFORMATION (2 columns per row: Label | Value | Label | Value) -->
    <div class="section-bar">GENERAL INFORMATION</div>
    <table class="info-table info-table-2col">
        <tr>
            <td>Legal Name</td>
            <td class="value">{{ $detail->legal_name ?? '—' }}</td>
            <td>Institution Category</td>
            <td class="value">{{ $detail->institution_category ?? '—' }}</td>
        </tr>
        <tr>
            <td>Telephone</td>
            <td class="value">{{ $detail->telephone ?? '—' }}</td>
            <td>Building Name</td>
            <td class="value">{{ $detail->building_name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Street Name</td>
            <td class="value">{{ $detail->street_name ?? '—' }}</td>
            <td>Locality</td>
            <td class="value">{{ $detail->locality_name ?? '—' }}</td>
        </tr>
        <tr>
            <td>City</td>
            <td class="value">{{ $detail->city ?? '—' }}</td>
            <td>District</td>
            <td class="value">{{ $detail->district ?? '—' }}</td>
        </tr>
        <tr>
            <td>State</td>
            <td class="value">{{ $detail->state ?? '—' }}</td>
            <td>PIN Code</td>
            <td class="value">{{ $detail->pin_code ?? '—' }}</td>
        </tr>
        <tr>
            <td>Country</td>
            <td class="value">{{ $detail->country ?? '—' }}</td>
            <td>Communication Officer Name</td>
            <td class="value">{{ $detail->comm_officer_name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Communication Officer Email</td>
            <td class="value">{{ $detail->comm_officer_email ?? '—' }}</td>
            <td>Communication Officer Mobile</td>
            <td class="value">{{ $detail->comm_officer_mobile ?? '—' }}</td>
        </tr>
        <tr>
            <td>Communication Officer WhatsApp</td>
            <td class="value">{{ $detail->comm_officer_whatsapp ?? '—' }}</td>
            <td>Alternate Mobile</td>
            <td class="value">{{ $detail->comm_officer_alt_mobile ?? '—' }}</td>
        </tr>
        <tr>
            <td>Authorized Person Name</td>
            <td class="value">{{ $detail->auth_person_name ?? '—' }}</td>
            <td>Authorized Person Designation</td>
            <td class="value">{{ $detail->auth_person_designation ?? '—' }}</td>
        </tr>
        <tr>
            <td>Authorized Person Mobile</td>
            <td class="value">{{ $detail->auth_person_mobile ?? '—' }}</td>
            <td>Authorized Person Email</td>
            <td class="value">{{ $detail->auth_person_email ?? '—' }}</td>
        </tr>
    </table>

    <!-- PARTNER & OFFICE INFORMATION (2 columns per row) -->
    <div class="section-bar">PARTNER & OFFICE INFORMATION</div>
    <table class="info-table info-table-2col">
        <tr>
            <td>B2B Partner ID</td>
            <td class="value">{{ $detail->b2b_partner_id ?? '—' }}</td>
            <td>B2B Code</td>
            <td class="value">{{ $detail->b2b_code ?? '—' }}</td>
        </tr>
        <tr>
            <td>Date of Joining</td>
            <td class="value">{{ $detail->date_of_joining ?? '—' }}</td>
            <td>Partner Status</td>
            <td class="value">{{ $detail->partner_status ?? '—' }}</td>
        </tr>
        <tr>
            <td>B2B Officer Name</td>
            <td class="value">{{ $detail->b2b_officer_name ?: 'Anshad Tk' }}</td>
            <td>Employee ID</td>
            <td class="value">{{ $detail->employee_id ?: 'FTM010' }}</td>
        </tr>
        <tr>
            <td>Designation</td>
            <td class="value">{{ $detail->designation ?: 'B2B Manager' }}</td>
            <td>Official Contact Number</td>
            <td class="value">{{ $detail->official_contact_number ?: '+91 95679 81443' }}</td>
        </tr>
        <tr>
            <td>WhatsApp Business Number</td>
            <td class="value">{{ $detail->whatsapp_business_number ?: '+91 95679 81443' }}</td>
            <td>Official Email ID</td>
            <td class="value">{{ $detail->official_email_id ?: 'btob@natdemy.com' }}</td>
        </tr>
        <tr>
            <td>Working Days</td>
            <td class="value">{{ $detail->working_days ?: 'Monday – Saturday' }}</td>
            <td>Office Hours</td>
            <td class="value">{{ $detail->office_hours ?: '09:00 AM – 05:00 PM' }}</td>
        </tr>
    </table>

    <!-- OFFICE ADDRESS (same labels and defaults as details page section 3) -->
    <div class="section-bar">OFFICE ADDRESS</div>
    <table class="info-table info-table-2col">
        <tr>
            <td>Building Name / Floor / Room Number</td>
            <td class="value">Nisa Pre College of Arts</td>
            <td>Street / Road Name</td>
            <td class="value">Murikkal Road</td>
        </tr>
        <tr>
            <td>Locality / Area Name</td>
            <td class="value">Palathingal</td>
            <td>City</td>
            <td class="value">Parappanangadi</td>
        </tr>
        <tr>
            <td>PIN Code</td>
            <td class="value">676303</td>
            <td>District</td>
            <td class="value">Malappuram</td>
        </tr>
        <tr>
            <td>State</td>
            <td class="value">Kerala</td>
            <td>Country</td>
            <td class="value">India</td>
        </tr>
    </table>

    <!-- BANKING & PAYMENT DETAILS (separate section so it shows properly) -->
    <div class="section-bar page-break">BANKING & PAYMENT DETAILS</div>
    <table class="info-table info-table-2col">
        <tr>
            <td>Account Holder Name</td>
            <td class="value bank-value">FUTURE AND TREE EDU OLUTION PVT LTD</td>
            <td>Bank Name</td>
            <td class="value bank-value">Axis Bank, Kallai Road, Kozhikode</td>
        </tr>
        <tr>
            <td>Account Number</td>
            <td class="value bank-value">921020041902527</td>
            <td>IFSC Code</td>
            <td class="value bank-value">UTIB0001908</td>
        </tr>
    </table>

    <!-- INTERESTED COURSES & DELIVERY STRUCTURES (table like Qualification Information) -->
    <div class="section-bar">INTERESTED COURSES & DELIVERY STRUCTURES</div>
    @if(count($interestedCourses) > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35%">Course</th>
                <th style="width: 65%">Delivery Structures</th>
            </tr>
        </thead>
        <tbody>
            @foreach($interestedCourses as $item)
            <tr>
                <td><strong>{{ $item['course'] }}</strong></td>
                <td>
                    @if(count($item['structures']) > 0)
                    {{ implode(', ', $item['structures']) }}
                    @else
                    —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="padding: 8px 0; color: #666; font-style: italic;">No course preferences recorded.</p>
    @endif


    <div class="footer-note">
        This document was generated by Natdemy. &copy; {{ date('Y') }} All rights reserved.
    </div>
</body>

</html>