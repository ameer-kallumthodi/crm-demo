<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Placement Details</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.45; color: #1f2937; }
        h1, h2, h3, p { margin: 0; }
        .header { border: 1px solid #d1d5db; background: #f8fafc; padding: 10px 12px; margin-bottom: 14px; }
        .header h1 { font-size: 18px; margin-bottom: 2px; }
        .muted { color: #6b7280; font-size: 10px; }
        .section { margin-bottom: 14px; page-break-inside: avoid; }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            background: #f3f4f6;
            margin-bottom: 6px;
        }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #d1d5db; padding: 6px 7px; vertical-align: top; word-wrap: break-word; }
        th { background: #f9fafb; text-align: left; font-weight: 700; color: #111827; }
        .kv td:first-child { width: 30%; font-weight: 700; background: #f9fafb; }
        .text-center { text-align: center; }
        .w-no { width: 6%; }
        .w-date { width: 16%; }
        .w-small { width: 9%; }
        .w-medium { width: 14%; }
        .empty { border: 1px solid #d1d5db; padding: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="text-align: center;">Placement Details</h1>
        <!-- <div class="muted">Generated on {{ now()->format('d M Y h:i A') }}</div> -->
    </div>

    <div class="section">
        <div class="section-title">Student Information</div>
        <table class="kv">
            <tr><td>Name</td><td>{{ $convertedLead->name ?? '—' }}</td></tr>
            <tr><td>Phone</td><td>{{ $convertedLead->phone ?? '—' }}</td></tr>
            <tr><td>Email</td><td>{{ $convertedLead->email ?? '—' }}</td></tr>
            <tr><td>Course</td><td>{{ $convertedLead->course?->title ?? '—' }}</td></tr>
            <tr><td>Batch</td><td>{{ $convertedLead->batch?->title ?? '—' }}</td></tr>
            <tr><td>Admission Batch</td><td>{{ $convertedLead->admissionBatch?->title ?? '—' }}</td></tr>
            <tr><td>Class Start Date</td><td>{{ $convertedLead->mentorDetails?->class_start_date ? $convertedLead->mentorDetails->class_start_date->format('d-m-Y') : '—' }}</td></tr>
            <tr><td>Class End Date</td><td>{{ $convertedLead->mentorDetails?->class_end_date ? $convertedLead->mentorDetails->class_end_date->format('d-m-Y') : '—' }}</td></tr>
            <tr><td>Specialization</td><td>{{ $convertedLead->mentorDetails?->specialization ?? '—' }}</td></tr>
            <tr><td>Remarks</td><td>{{ $convertedLead->mentorDetails?->placement_remarks ?? '—' }}</td></tr>
            <tr><td>Stage</td><td>{{ $convertedLead->getPlacementStage() }}</td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Mock Test Details</div>
        @if($convertedLead->placementMockTestDetails->isEmpty())
            <div class="empty">No mock test entries available.</div>
        @else
            @php
                $sortedMockTests = $convertedLead->placementMockTestDetails->sortBy('created_at');
            @endphp
            <table>
                <thead>
                    <tr>
                        <th class="w-no text-center">#</th>
                        <th class="w-small text-center">Speaking</th>
                        <th class="w-small text-center">Presentation</th>
                        <th class="w-small text-center">Character</th>
                        <th class="w-small text-center">Dedication</th>
                        <th class="w-small text-center">Total</th>
                        <th class="w-medium">Stage</th>
                        <th>Remark</th>
                        <th class="w-date">Added On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sortedMockTests as $entry)
                        @php
                            $rowTotal = $entry->speaking_capacity + $entry->presentation_skill + $entry->character + $entry->dedication;
                            $rowStage = $rowTotal < 35 ? 'Need Mock Test' : 'Passed Mock Test';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $entry->speaking_capacity }}/10</td>
                            <td class="text-center">{{ $entry->presentation_skill }}/10</td>
                            <td class="text-center">{{ $entry->character }}/10</td>
                            <td class="text-center">{{ $entry->dedication }}/10</td>
                            <td class="text-center">{{ $rowTotal }}/40</td>
                            <td>{{ $rowStage }}</td>
                            <td>{{ $entry->remark ?? '—' }}</td>
                            <td>{{ $entry->created_at ? $entry->created_at->format('d M Y h:i A') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Scheduled Interviews</div>
        @if($convertedLead->placementScheduledInterviews->isEmpty())
            <div class="empty">No interviews scheduled.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th class="w-no text-center">#</th>
                        <th>Company Name</th>
                        <th class="w-medium">Place</th>
                        <th class="w-date">Interview Date</th>
                        <th class="w-medium">Status</th>
                        <th class="w-date">Added On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($convertedLead->placementScheduledInterviews as $interview)
                        @php
                            $statusLabel = \App\Models\PlacementScheduledInterview::statusOptions()[$interview->status] ?? ucfirst((string) $interview->status);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $interview->company_name ?? '—' }}</td>
                            <td>{{ $interview->place ?? '—' }}</td>
                            <td>{{ $interview->interview_date ? $interview->interview_date->format('d M Y') : '—' }}</td>
                            <td>{{ $statusLabel }}</td>
                            <td>{{ $interview->created_at ? $interview->created_at->format('d M Y h:i A') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
