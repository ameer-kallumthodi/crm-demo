@php
    $phoneHelper = \App\Helpers\PhoneNumberHelper::class;
    $filtersApplied = $filtersApplied ?? false;
@endphp

<div class="card border shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Followup Leads ({{ $leads->count() }})</h6>
        @if(!empty($followupStatusIds))
            <span class="badge bg-warning text-dark">Statuses: {{ implode(', ', $followupStatusIds) }}</span>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Sl. No</th>
                        <th>Lead Name</th>
                        <th>Phone</th>
                        <th>Telecaller</th>
                        <th>Followup Date</th>
                        <th>Remarks</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $index => $lead)
                        @php
                            $activity = $lead->latestFollowupActivity;
                            $displayRemarks = $activity && !empty($activity->remarks) ? $activity->remarks : $lead->remarks;
                            $displayFollowupDate = $lead->followup_date ? $lead->followup_date->format('d M Y') : '-';
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $lead->title ?? '-' }}</td>
                            <td>{{ $phoneHelper::display($lead->code, $lead->phone) }}</td>
                            <td>{{ $lead->telecaller->name ?? 'Unassigned' }}</td>
                            <td>{{ $displayFollowupDate }}</td>
                            <td style="white-space: pre-wrap;">{{ $displayRemarks ?: '-' }}</td>
                            <td>{{ $activity && $activity->reason ? $activity->reason : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                {{ $filtersApplied ? 'No followup leads found for the selected filters.' : 'Use the filters above to load followup leads.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

