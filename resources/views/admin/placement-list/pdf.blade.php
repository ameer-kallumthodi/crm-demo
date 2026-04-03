<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Placement Details</title>
    <style>
        @page { margin: 28px 24px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #222;
            margin: 0;
            padding: 6px;
        }
        .header {
            border: 1px solid #d8e1f2;
            background: #f4f7ff;
            padding: 12px 10px 10px 10px;
            margin-bottom: 16px;
            text-align: center;
            border-radius: 6px;
        }
        .title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #1f3b73;
            letter-spacing: 0.5px;
        }
        .subtitle {
            margin-top: 4px;
            color: #666;
            font-size: 11px;
        }
        .mini-meta {
            margin-top: 6px;
            color: #4d5e80;
            font-size: 10px;
        }
        .section {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .section-title {
            margin: 0 0 6px 0;
            background: #eef3ff;
            border: 1px solid #d8e1f2;
            color: #1f3b73;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 8px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border-radius: 4px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #d8d8d8;
            padding: 6px 7px;
            vertical-align: top;
            word-wrap: break-word;
        }
        th {
            background: #f2f5fb;
            color: #203760;
            font-weight: 700;
            text-align: left;
        }
        tbody tr:nth-child(even) { background: #fcfdff; }
        .kv td:first-child {
            width: 28%;
            background: #f7f9fd;
            font-weight: 700;
            color: #1e3359;
        }
        .text-center { text-align: center; }
        .muted { color: #777; }
        .empty {
            border: 1px solid #d8d8d8;
            padding: 8px;
            color: #777;
            background: #fcfcfc;
            border-radius: 4px;
        }
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
            border: 1px solid transparent;
        }
        .badge-success { background: #e9f8ee; border-color: #bfe6cc; color: #1f7a3f; }
        .badge-warning { background: #fff6df; border-color: #f2dd9a; color: #8a6500; }
        .badge-info { background: #e8f4ff; border-color: #b7d8f5; color: #0d5f9a; }
        .badge-secondary { background: #f1f3f5; border-color: #d7dce1; color: #58636f; }
        .footer-note {
            margin-top: 10px;
            text-align: center;
            color: #8b8b8b;
            font-size: 9px;
        }
    </style>
</head>
<body>
    @php
        $stage = $convertedLead->getPlacementStage();
        $stageBadgeClass = 'badge-secondary';
        if ($stage === 'Placed') {
            $stageBadgeClass = 'badge-success';
        } elseif ($stage === 'Need Mock Test') {
            $stageBadgeClass = 'badge-warning';
        } elseif ($stage === 'Passed Mock Test') {
            $stageBadgeClass = 'badge-info';
        }
    @endphp
    <div class="header">
        <h1 class="title">Placement Details</h1>
        <div class="mini-meta">
            Course: {{ $convertedLead->course?->title ?? '—' }} | Batch: {{ $convertedLead->batch?->title ?? '—' }} |
            Stage: <span class="badge {{ $stageBadgeClass }}">{{ $stage }}</span>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Student Information</h3>
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
            <tr><td>Stage</td><td><span class="badge {{ $stageBadgeClass }}">{{ $stage }}</span></td></tr>
        </table>
    </div>

    <div class="section">
        <h3 class="section-title">Mock Test Details</h3>
        @if($convertedLead->placementMockTestDetails->isEmpty())
            <div class="empty">No mock test entries available.</div>
        @else
            @php
                $sortedMockTests = $convertedLead->placementMockTestDetails->sortBy('created_at');
            @endphp
            <table>
                <thead>
                    <tr>
                        <th style="width:4%;">#</th>
                        <th style="width:9%;" class="text-center">Speaking</th>
                        <th style="width:11%;" class="text-center">Presentation</th>
                        <th style="width:9%;" class="text-center">Character</th>
                        <th style="width:9%;" class="text-center">Dedication</th>
                        <th style="width:8%;" class="text-center">Total</th>
                        <th style="width:13%;">Stage</th>
                        <th style="width:20%;">Remark</th>
                        <th style="width:17%;">Added On</th>
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
                            <td>
                                @if($rowStage === 'Need Mock Test')
                                    <span class="badge badge-warning">{{ $rowStage }}</span>
                                @else
                                    <span class="badge badge-info">{{ $rowStage }}</span>
                                @endif
                            </td>
                            <td>{{ $entry->remark ?? '—' }}</td>
                            <td>{{ $entry->created_at ? $entry->created_at->format('d M Y h:i A') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <h3 class="section-title">Scheduled Interviews</h3>
        @if($convertedLead->placementScheduledInterviews->isEmpty())
            <div class="empty">No interviews scheduled.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:5%;">#</th>
                        <th style="width:28%;">Company Name</th>
                        <th style="width:18%;">Place</th>
                        <th style="width:16%;">Interview Date</th>
                        <th style="width:13%;">Status</th>
                        <th style="width:20%;">Added On</th>
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
                            <td>
                                @if($interview->status === \App\Models\PlacementScheduledInterview::STATUS_PLACED)
                                    <span class="badge badge-success">{{ $statusLabel }}</span>
                                @elseif($interview->status === \App\Models\PlacementScheduledInterview::STATUS_NOT_PLACED)
                                    <span class="badge badge-warning">{{ $statusLabel }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ $statusLabel }}</span>
                                @endif
                            </td>
                            <td>{{ $interview->created_at ? $interview->created_at->format('d M Y h:i A') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <h3 class="section-title">Remarks history</h3>
        @if($convertedLead->placementRemarkHistories->isEmpty())
            <div class="empty">No remarks history recorded.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:22%;">When</th>
                        <th style="width:22%;">Updated by</th>
                        <th style="width:56%;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($convertedLead->placementRemarkHistories as $hist)
                        <tr>
                            <td>{{ $hist->created_at ? $hist->created_at->format('d-m-Y h:i A') : '—' }}</td>
                            <td>{{ $hist->user?->name ?? '—' }}</td>
                            <td>
                                @if($hist->remarks !== null && $hist->remarks !== '')
                                    {!! nl2br(e($hist->remarks)) !!}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="footer-note">This is a system-generated placement report. Generated on {{ now()->format('d M Y h:i A') }}.</div>
</body>
</html>
