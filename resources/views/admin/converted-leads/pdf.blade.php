<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Converted Lead Details</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1f2937; }
        .container { width: 100%; }
        .header { display: table; width: 100%; margin-bottom: 16px; }
        .header .left { display: table-cell; vertical-align: middle; }
        .header .right { display: table-cell; text-align: right; vertical-align: middle; }
        .title { font-size: 18px; margin: 0; color: #111827; }
        .muted { color: #6b7280; }
        .section { margin-bottom: 14px; }
        .section-title { font-size: 13px; color: #374151; margin: 0 0 6px; text-transform: uppercase; letter-spacing: .03em; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 8px 10px; border: 1px solid #e5e7eb; }
        tr:nth-child(even) td { background: #fafafa; }
        th { background: #f3f4f6; color: #374151; font-weight: bold; }
        .grid-2 { width: 100%; }
        .grid-2 td.label { width: 28%; color: #6b7280; font-weight: 700; }
        .grid-2 td.value { width: 22%; color: #1f2937; }
        .small { font-size: 11px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left">
                <h1 class="title">Converted Lead Details</h1>
                <div class="small muted">Reference #: {{ $convertedLead->id }}</div>
            </div>
            <div class="right small muted">
                Generated: {{ now()->format('d-m-Y h:i A') }}
            </div>
        </div>

        <!-- Personal Information -->
        <div class="section">
            <h3 class="section-title">Personal Information</h3>
            <table class="grid-2">
                <tr>
                    <td class="label">Name</td>
                    <td class="value">{{ $convertedLead->name }}</td>
                    <td class="label">Register Number</td>
                    <td class="value">{{ $convertedLead->register_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Phone</td>
                    <td class="value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                    <td class="label">Email</td>
                    <td class="value">{{ $convertedLead->email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">DOB</td>
                    <td class="value">
                        @php
                            $dobDisplay = $convertedLead->dob ? (strtotime($convertedLead->dob) ? date('d-m-Y', strtotime($convertedLead->dob)) : $convertedLead->dob) : 'N/A';
                        @endphp
                        {{ $dobDisplay }}
                    </td>
                    <td class="label">Remarks</td>
                    <td class="value">{{ $convertedLead->remarks ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Academic Information -->
        <div class="section">
            <h3 class="section-title">Academic Information</h3>
            <table class="grid-2">
                <tr>
                    <td class="label">Course</td>
                    <td class="value">{{ $convertedLead->course->title ?? 'N/A' }}</td>
                    <td class="label">Batch</td>
                    <td class="value">{{ $convertedLead->batch->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Admission Batch</td>
                    <td class="value">{{ $convertedLead->admissionBatch->title ?? 'N/A' }}</td>
                    <td class="label">Subject</td>
                    <td class="value">{{ $convertedLead->subject->title ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Academic Assistant</td>
                    <td class="value">{{ $convertedLead->academicAssistant->name ?? 'N/A' }}</td>
                    <td class="label"></td>
                    <td class="value"></td>
                </tr>
            </table>
        </div>

        <!-- Conversion & Account Information -->
        <div class="section">
            <h3 class="section-title">Conversion & Account Information</h3>
            <table class="grid-2">
                <tr>
                    <td class="label">Converted By</td>
                    <td class="value">{{ $convertedLead->createdBy->name ?? 'N/A' }}</td>
                    <td class="label">Converted Date</td>
                    <td class="value">{{ $convertedLead->created_at->format('d-m-Y h:i A') }}</td>
                </tr>
                <tr>
                    <td class="label">Username</td>
                    <td class="value">{{ $convertedLead->username ?? 'N/A' }}</td>
                    <td class="label">Password</td>
                    <td class="value">{{ $convertedLead->password ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td class="value">{{ $convertedLead->status ?? 'N/A' }}</td>
                    <td class="label">ID Card</td>
                    <td class="value">{{ $convertedLead->id_card ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">REG. FEE</td>
                    <td class="value">{{ $convertedLead->reg_fee ?? 'N/A' }}</td>
                    <td class="label">EXAM FEE</td>
                    <td class="value">{{ $convertedLead->exam_fee ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Ref No</td>
                    <td class="value">{{ $convertedLead->ref_no ?? 'N/A' }}</td>
                    <td class="label">Enroll No</td>
                    <td class="value">{{ $convertedLead->enroll_no ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">TMA</td>
                    <td class="value">{{ $convertedLead->tma ?? 'N/A' }}</td>
                    <td class="label"></td>
                    <td class="value"></td>
                </tr>
            </table>
        </div>

        <!-- Lead Details (if present) -->
        @if($convertedLead->leadDetail)
        <div class="section">
            <h3 class="section-title">Lead Details</h3>
            <table class="grid-2">
                <tr>
                    <td class="label">Father's Name</td>
                    <td class="value">{{ $convertedLead->leadDetail->father_name ?? 'N/A' }}</td>
                    <td class="label">Mother's Name</td>
                    <td class="value">{{ $convertedLead->leadDetail->mother_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">DOB</td>
                    <td class="value">{{ $convertedLead->leadDetail->date_of_birth ? $convertedLead->leadDetail->date_of_birth->format('d-m-Y') : 'N/A' }}</td>
                    <td class="label">Second Language</td>
                    <td class="value">{{ $convertedLead->leadDetail->second_language ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Personal Phone</td>
                    <td class="value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->personal_code, $convertedLead->leadDetail->personal_number) }}</td>
                    <td class="label">Parent Phone</td>
                    <td class="value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->parents_code, $convertedLead->leadDetail->parents_number) }}</td>
                </tr>
                <tr>
                    <td class="label">WhatsApp</td>
                    <td class="value">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->leadDetail->whatsapp_code, $convertedLead->leadDetail->whatsapp_number) }}</td>
                    <td class="label">Batch</td>
                    <td class="value">{{ optional($convertedLead->leadDetail->batch)->title ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Detailed Lead Activities Report -->
        @if(isset($leadActivities) && $leadActivities->count() > 0)
        <div class="section">
            <h3 class="section-title">Lead Activities</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 18%">Date & Time</th>
                        <th style="width: 16%">Type</th>
                        <th>Details</th>
                        <th style="width: 18%">Status</th>
                        <th style="width: 18%">By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leadActivities as $activity)
                    <tr>
                        <td>{{ $activity->created_at->format('d-m-Y h:i A') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</td>
                        <td>
                            @if($activity->description)
                                <div><strong>Desc:</strong> {{ $activity->description }}</div>
                            @endif
                            @if($activity->reason)
                                <div><strong>Reason:</strong> {{ $activity->formatted_reason ?? $activity->reason }}</div>
                            @endif
                            @if($activity->remarks)
                                <div class="small muted"><strong>Remarks:</strong> {{ $activity->remarks }}</div>
                            @endif
                            @if($activity->followup_date)
                                <div class="small muted"><strong>Followup:</strong> {{ $activity->followup_date->format('d-m-Y') }}</div>
                            @endif
                            @if($activity->rating)
                                <div class="small muted"><strong>Rating:</strong> {{ $activity->rating }}/10</div>
                            @endif
                        </td>
                        <td>{{ $activity->leadStatus->title ?? 'N/A' }}</td>
                        <td>{{ $activity->createdBy->name ?? 'System' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="small muted">Note: Uploaded Documents are intentionally excluded from this PDF.</div>
    </div>
</body>
</html>


