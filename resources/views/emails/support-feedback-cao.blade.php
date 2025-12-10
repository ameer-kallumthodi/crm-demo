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
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">📋 Support Feedback Notification</h2>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">New feedback submitted for student support</p>
    </div>
    
    <div class="content">
        <div class="student-info">
            <h3 style="margin-top: 0; color: #007bff;">Student Information</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td class="label" style="padding: 5px 10px 5px 0; width: 150px;">Student ID:</td>
                    <td class="value">#{{ $convertedLead->id }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 5px 10px 5px 0;">Name:</td>
                    <td class="value">{{ $convertedLead->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 5px 10px 5px 0;">Register Number:</td>
                    <td class="value">{{ $convertedLead->register_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 5px 10px 5px 0;">Course:</td>
                    <td class="value">{{ $convertedLead->course?->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 5px 10px 5px 0;">Batch:</td>
                    <td class="value">{{ $convertedLead->batch?->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 5px 10px 5px 0;">Phone:</td>
                    <td class="value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                </tr>
                <tr>
                    <td class="label" style="padding: 5px 10px 5px 0;">Email:</td>
                    <td class="value">{{ $convertedLead->email ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="feedback-content">
            <h3 style="margin-top: 0; color: #28a745;">Feedback Details</h3>
            <div style="margin-bottom: 15px;">
                <span class="label">Type:</span>
                <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $feedback->feedback_type ?? 'general')) }}</span>
                @if($feedback->priority ?? null)
                <span class="badge badge-warning" style="margin-left: 10px;">Priority: {{ ucfirst($feedback->priority) }}</span>
                @endif
                @if($feedback->feedback_status ?? null)
                <span class="badge badge-success" style="margin-left: 10px;">Status: {{ ucfirst(str_replace('_', ' ', $feedback->feedback_status)) }}</span>
                @endif
            </div>
            <div style="margin-bottom: 15px;">
                <span class="label">Submitted:</span>
                <span class="value">{{ $feedback->created_at?->format('d M Y, H:i A') ?? 'N/A' }}</span>
            </div>
            @if($feedback->follow_up_date ?? null)
            <div style="margin-bottom: 15px;">
                <span class="label">Follow-up Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($feedback->follow_up_date)->format('d M Y') }}</span>
            </div>
            @endif
            @if($feedback->notes ?? null)
            <div style="margin-bottom: 15px;">
                <span class="label">Additional Notes:</span>
                <div class="value" style="margin-top: 5px; padding: 10px; background-color: #f8f9fa; border-radius: 4px;">{{ $feedback->notes }}</div>
            </div>
            @endif
            <div style="margin-top: 20px;">
                <span class="label">Feedback Content:</span>
                <div style="margin-top: 10px; padding: 15px; background-color: #f8f9fa; border-radius: 4px; border: 1px solid #dee2e6;">{{ $feedback->feedback_content ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p style="margin: 0;"><strong>This is an automated notification from CRM System</strong></p>
        <p style="margin: 5px 0 0 0;">Submitted by: {{ $feedback->createdBy?->name ?? 'System' }} on {{ $feedback->created_at?->format('d M Y, H:i A') ?? 'N/A' }}</p>
    </div>
</body>
</html>

