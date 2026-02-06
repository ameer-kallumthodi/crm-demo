<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Support Feedback - {{ $convertedLead->name ?? 'Student' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin-bottom: 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .student-info {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .feedback-content {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            white-space: pre-wrap;
            font-family: monospace;
            font-size: 13px;
            line-height: 1.8;
            border-left: 4px solid #28a745;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #6c757d;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .label {
            font-weight: bold;
            color: #495057;
        }
        .value {
            color: #212529;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-primary {
            background-color: #007bff;
            color: white;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 10px 6px 0;
            vertical-align: top;
        }
        .info-table .label {
            width: 180px;
            white-space: nowrap;
        }
        .section-heading {
            margin-top: 0;
            color: #007bff;
        }
        .feedback-heading {
            margin-top: 0;
            color: #28a745;
        }
        .feedback-body {
            margin-top: 10px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">📋 Support Feedback Notification</h2>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">New feedback submitted for student support</p>
    </div>
    
    <div class="content">
        <div class="student-info">
            <h3 class="section-heading">Student Information</h3>
            <table class="info-table">
                <tr>
                    <td class="label">Student ID:</td>
                    <td class="value">#{{ $convertedLead->id }}</td>
                </tr>
                <tr>
                    <td class="label">Name:</td>
                    <td class="value">{{ $convertedLead->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Register Number:</td>
                    <td class="value">{{ $convertedLead->register_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Course:</td>
                    <td class="value">{{ $convertedLead->course?->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Batch:</td>
                    <td class="value">{{ $convertedLead->batch?->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Admission Batch:</td>
                    <td class="value">{{ $convertedLead->admissionBatch?->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Phone:</td>
                    <td class="value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td class="value">{{ $convertedLead->email ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="feedback-content">
            <h3 class="feedback-heading">Feedback Details</h3>
            <table class="info-table" style="margin-bottom: 10px;">
                <tr>
                    <td class="label">Type:</td>
                    <td class="value">
                        <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $feedback->feedback_type ?? 'general')) }}</span>
                        @if($feedback->priority ?? null)
                        <span class="badge badge-warning" style="margin-left: 6px;">Priority: {{ ucfirst($feedback->priority) }}</span>
                        @endif
                        @if($feedback->feedback_status ?? null)
                        <span class="badge badge-success" style="margin-left: 6px;">Status: {{ ucfirst(str_replace('_', ' ', $feedback->feedback_status)) }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Submitted:</td>
                    <td class="value">{{ $feedback->created_at?->format('d M Y, H:i A') ?? 'N/A' }}</td>
                </tr>
                @if($feedback->follow_up_date ?? null)
                <tr>
                    <td class="label">Follow-up Date:</td>
                    <td class="value">{{ \Carbon\Carbon::parse($feedback->follow_up_date)->format('d M Y') }}</td>
                </tr>
                @endif
                @if($feedback->notes ?? null)
                <tr>
                    <td class="label">Additional Notes:</td>
                    <td class="value">{{ $feedback->notes }}</td>
                </tr>
                @endif
            </table>
            <div>
                <span class="label">Feedback Content:</span>
                <div class="feedback-body">{{ $feedback->feedback_content ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p style="margin: 0;"><strong>This is an automated notification from CRM System</strong></p>
        <p style="margin: 5px 0 0 0;">Submitted by: {{ $feedback->createdBy?->name ?? 'System' }} on {{ $feedback->created_at?->format('d M Y, H:i A') ?? 'N/A' }}</p>
    </div>
</body>
</html>

