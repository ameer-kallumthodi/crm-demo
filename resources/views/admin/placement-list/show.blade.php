@extends('layouts.mantis')

@section('title', 'Placement Details')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Placement Details</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.placement-list.index') }}">Placement List</a></li>
                    <li class="breadcrumb-item">Details</li>
                </ul>
                <div class="d-flex justify-content-end mt-2 gap-2">
                    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
                        <a href="{{ route('admin.placement-list.pdf', $convertedLead->id) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="ti ti-download"></i> Download PDF
                        </a>
                    @endif
                    <a href="{{ route('admin.placement-list.index') }}" class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Placement List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-user-check text-primary"></i> Placement Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label text-muted">Name</label>
                        <p class="fw-bold mb-0">{{ $convertedLead->name ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Phone</label>
                        <p class="fw-bold mb-0">{{ $convertedLead->phone ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Email</label>
                        <p class="fw-bold mb-0">{{ $convertedLead->email ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Course</label>
                        <p class="fw-bold mb-0">{{ $convertedLead->course?->title ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Batch</label>
                        <p class="fw-bold mb-0">{{ $convertedLead->batch?->title ?? '—' }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Specialization</label>
                        <p class="fw-bold mb-0">{{ $convertedLead->mentorDetails->specialization ?? '—' }}</p>
                    </div>
                    <div class="col-12">
                        <div class="d-inline-flex flex-wrap align-items-center gap-2 mb-2">
                            <label class="form-label text-muted mb-0">Remarks</label>
                            <button type="button" class="btn btn-sm btn-outline-secondary placement-remarks-edit-btn flex-shrink-0" data-id="{{ $convertedLead->id }}">
                                <i class="ti ti-refresh"></i> Update
                            </button>
                        </div>
                        <div class="placement-remarks-cell">
                            <span class="placement-remarks-display fw-bold d-block" style="white-space: pre-wrap;">{{ $convertedLead->mentorDetails?->placement_remarks ?: '—' }}</span>
                            <div class="placement-remarks-edit-wrap d-none mt-2">
                                <textarea class="form-control form-control-sm placement-remarks-input" rows="3" maxlength="2000" placeholder="Enter updated remarks"></textarea>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-primary placement-remarks-save-btn"><i class="ti ti-check"></i> Update</button>
                                    <button type="button" class="btn btn-sm btn-secondary placement-remarks-cancel-btn"><i class="ti ti-x"></i> Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Resume</label>
                        @if($convertedLead->mentorDetails && $convertedLead->mentorDetails->is_resume_verified && $convertedLead->mentorDetails->placement_resume)
                            <p class="mb-0">
                                <a href="{{ asset('storage/' . $convertedLead->mentorDetails->placement_resume) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="ti ti-file-text"></i> View / Download Resume
                                </a>
                            </p>
                        @else
                            <p class="fw-bold mb-0 text-muted">—</p>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Stage</label>
                        @php
                            $placementStage = $convertedLead->getPlacementStage();
                        @endphp
                        <p class="mb-0">
                            @if($placementStage === 'Placed')
                                <span class="badge bg-success">Placed</span>
                            @elseif($placementStage === 'Need Mock Test')
                                <span class="badge bg-warning text-dark">Need Mock Test</span>
                            @elseif($placementStage === 'Passed Mock Test')
                                <span class="badge bg-info">Passed Mock Test</span>
                            @else
                                <span class="badge bg-secondary">Pending</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mock Test Details --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-clipboard-list text-primary"></i> Mock Test Details</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#mockTestDetailsModal">
                    <i class="ti ti-plus"></i> Update Mock Test Details
                </button>
            </div>
            <div class="card-body">
                @if($convertedLead->placementMockTestDetails->isEmpty())
                    <p class="text-muted mb-0">No mock test entries yet. Click "Update Mock Test Details" to add.</p>
                @else
                    @php
                        $sortedMockTests = $convertedLead->placementMockTestDetails->sortBy('created_at');
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Mock Test</th>
                                    <th>Speaking Capacity</th>
                                    <th>Presentation Skill</th>
                                    <th>Character</th>
                                    <th>Dedication</th>
                                    <th>Total</th>
                                    <th>Stage</th>
                                    <th>Remark</th>
                                    <th>Added On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sortedMockTests as $entry)
                                @php
                                    $rowTotal = $entry->speaking_capacity + $entry->presentation_skill + $entry->character + $entry->dedication;
                                    $rowStage = $rowTotal < 35 ? 'Need Mock Test' : 'Passed Mock Test';
                                @endphp
                                <tr>
                                    <td>Mock Test {{ $loop->iteration }}</td>
                                    <td>{{ $entry->speaking_capacity }}/10</td>
                                    <td>{{ $entry->presentation_skill }}/10</td>
                                    <td>{{ $entry->character }}/10</td>
                                    <td>{{ $entry->dedication }}/10</td>
                                    <td><strong>{{ $rowTotal }}/40</strong></td>
                                    <td>
                                        @if($rowStage === 'Need Mock Test')
                                            <span class="badge bg-warning text-dark">Need Mock Test</span>
                                        @else
                                            <span class="badge bg-info">Passed Mock Test</span>
                                        @endif
                                    </td>
                                    <td>{{ $entry->remark ?? '—' }}</td>
                                    <td>{{ $entry->created_at->format('d M Y h:i A') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Scheduled Interviews --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="card" id="scheduledInterviewsCard" data-status-url="{{ route('admin.placement-list.interviews.status', [$convertedLead->id, '__ID__']) }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-calendar-event text-primary"></i> Scheduled Interviews</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleInterviewModal">
                    <i class="ti ti-calendar-event"></i> Schedule Interview
                </button>
            </div>
            <div class="card-body">
                @if($convertedLead->placementScheduledInterviews->isEmpty())
                    <p class="text-muted mb-0">No scheduled interviews yet. Click "Schedule Interview" to add.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Company Name</th>
                                    <th>Place</th>
                                    <th>Interview Date</th>
                                    <th>Status</th>
                                    <th>Added On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($convertedLead->placementScheduledInterviews as $idx => $interview)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $interview->company_name }}</td>
                                    <td>{{ $interview->place }}</td>
                                    <td>{{ $interview->interview_date->format('d M Y') }}</td>
                                    <td>
                                        <select class="form-select form-select-sm interview-status-select" data-interview-id="{{ $interview->id }}" style="width: auto; min-width: 120px;">
                                            @foreach(\App\Models\PlacementScheduledInterview::statusOptions() as $value => $label)
                                                <option value="{{ $value }}" {{ $interview->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>{{ $interview->created_at->format('d M Y h:i A') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@php
    $remarksHistoryCount = $convertedLead->placementRemarkHistories->count();
@endphp
{{-- Remarks history --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="card placement-remarks-history-card" id="placementRemarksHistoryCard">
            <div class="card-header d-flex flex-column flex-sm-row flex-wrap justify-content-between align-items-start align-items-sm-center gap-2">
                <div>
                    <h5 class="mb-0"><i class="ti ti-history text-primary"></i> Remarks history</h5>
                    <small class="text-muted d-block mt-1">Each save is logged with date, time, and user.</small>
                </div>
                <span class="badge bg-light-primary text-primary rounded-pill px-3 py-2 flex-shrink-0" id="placement-remarks-history-count">{{ $remarksHistoryCount }} {{ $remarksHistoryCount === 1 ? 'entry' : 'entries' }}</span>
            </div>
            <div class="card-body placement-remarks-history-card-body">
                <div class="table-responsive placement-remarks-history-table-wrap">
                    <table class="table table-sm table-bordered table-hover mb-0 placement-remarks-history-table">
                        <colgroup>
                            <col class="placement-remarks-history-col-when">
                            <col class="placement-remarks-history-col-user">
                            <col>
                        </colgroup>
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="placement-remarks-history-th">When</th>
                                <th scope="col" class="placement-remarks-history-th">Updated by</th>
                                <th scope="col" class="placement-remarks-history-th">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="placement-remarks-history-tbody">
                            @forelse($convertedLead->placementRemarkHistories as $hist)
                                <tr data-history-id="{{ $hist->id }}" class="placement-remarks-history-row">
                                    <td class="placement-remarks-history-td align-top text-nowrap">
                                        {{ $hist->created_at->format('d-m-Y h:i A') }}
                                    </td>
                                    <td class="placement-remarks-history-td align-top">
                                        {{ $hist->user?->name ?? '—' }}
                                    </td>
                                    <td class="placement-remarks-history-td align-top placement-remarks-history-remark-cell text-break">{{ $hist->remarks !== null && $hist->remarks !== '' ? $hist->remarks : '—' }}</td>
                                </tr>
                            @empty
                                <tr class="placement-remarks-history-empty">
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="ti ti-notes-off d-block mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                                        <span class="fw-medium text-body">No history yet</span>
                                        <br>
                                        <small>Save a remark update under <strong>Placement information</strong> to add the first entry.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Schedule Interview --}}
<div class="modal fade" id="scheduleInterviewModal" tabindex="-1" aria-labelledby="scheduleInterviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleInterviewModalLabel">Schedule Interview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.placement-list.interviews.store', $convertedLead->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" required maxlength="255">
                        @error('company_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="place" class="form-label">Place <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="place" name="place" value="{{ old('place') }}" required maxlength="255">
                        @error('place')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="interview_date" class="form-label">Interview Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="interview_date" name="interview_date" value="{{ old('interview_date') }}" required>
                        @error('interview_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Add Mock Test Details --}}
<div class="modal fade" id="mockTestDetailsModal" tabindex="-1" aria-labelledby="mockTestDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mockTestDetailsModalLabel">Update Mock Test Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.placement-list.mock-test-details.store', $convertedLead->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="speaking_capacity" class="form-label">Speaking Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="speaking_capacity" name="speaking_capacity" min="1" max="10" value="{{ old('speaking_capacity', '') }}" required>
                        <small class="text-muted">Min 1, Max 10</small>
                        @error('speaking_capacity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="presentation_skill" class="form-label">Presentation Skill <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="presentation_skill" name="presentation_skill" min="1" max="10" value="{{ old('presentation_skill', '') }}" required>
                        <small class="text-muted">Min 1, Max 10</small>
                        @error('presentation_skill')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="character" class="form-label">Character <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="character" name="character" min="1" max="10" value="{{ old('character', '') }}" required>
                        <small class="text-muted">Min 1, Max 10</small>
                        @error('character')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="dedication" class="form-label">Dedication <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="dedication" name="dedication" min="1" max="10" value="{{ old('dedication', '') }}" required>
                        <small class="text-muted">Min 1, Max 10</small>
                        @error('dedication')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3" maxlength="2000">{{ old('remark') }}</textarea>
                        @error('remark')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('styles')
<style>
    .placement-remarks-history-card .placement-remarks-history-card-body {
        padding: 1.25rem 1.5rem;
    }
    @media (max-width: 575.98px) {
        .placement-remarks-history-card .placement-remarks-history-card-body {
            padding: 1rem;
        }
    }
    .placement-remarks-history-card .placement-remarks-history-table-wrap {
        margin-bottom: 0;
    }
    .placement-remarks-history-card .placement-remarks-history-table {
        table-layout: fixed;
        width: 100%;
    }
    .placement-remarks-history-card .placement-remarks-history-col-when {
        width: 11.5rem;
    }
    .placement-remarks-history-card .placement-remarks-history-col-user {
        width: 12.5rem;
    }
    @media (max-width: 767.98px) {
        .placement-remarks-history-card .placement-remarks-history-col-when {
            width: 9.5rem;
        }
        .placement-remarks-history-card .placement-remarks-history-col-user {
            width: 8.5rem;
        }
    }
    .placement-remarks-history-card .placement-remarks-history-th {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #6c757d;
        vertical-align: middle;
        white-space: nowrap;
        padding: 0.65rem 0.75rem;
    }
    .placement-remarks-history-card .placement-remarks-history-td {
        vertical-align: top;
        padding: 0.65rem 0.75rem;
    }
    .placement-remarks-history-card .placement-remarks-history-remark-cell {
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: anywhere;
        line-height: 1.5;
        min-width: 0;
    }
</style>
@endpush

@push('scripts')
<div id="placementRemarksConfig"
     data-remarks-update-url="{{ route('admin.placement-list.update-remarks', ['id' => $convertedLead->id]) }}"
     style="display: none;"></div>
@if($errors->any())
<script>
$(document).ready(function() {
    var modal = new bootstrap.Modal(document.getElementById('mockTestDetailsModal'));
    modal.show();
});
</script>
@endif
@if($errors->has('company_name') || $errors->has('place') || $errors->has('interview_date'))
<script>
$(document).ready(function() {
    var modal = new bootstrap.Modal(document.getElementById('scheduleInterviewModal'));
    modal.show();
});
</script>
@endif
<script>
$(document).ready(function() {
    var remarksConfigEl = document.getElementById('placementRemarksConfig');
    var remarksUpdateUrl = remarksConfigEl ? remarksConfigEl.getAttribute('data-remarks-update-url') : '';

    $(document).on('click', '.placement-remarks-edit-btn', function() {
        var $cell = $(this).closest('.placement-remarks-cell');
        $cell.find('.placement-remarks-display').addClass('d-none');
        $cell.find('.placement-remarks-edit-btn').addClass('d-none');
        $cell.find('.placement-remarks-edit-wrap').removeClass('d-none');
        $cell.find('.placement-remarks-input').val('').focus();
    });

    $(document).on('click', '.placement-remarks-cancel-btn', function() {
        var $cell = $(this).closest('.placement-remarks-cell');
        $cell.find('.placement-remarks-input').val('');
        $cell.find('.placement-remarks-edit-wrap').addClass('d-none');
        $cell.find('.placement-remarks-display').removeClass('d-none');
        $cell.find('.placement-remarks-edit-btn').removeClass('d-none');
    });

    $(document).on('click', '.placement-remarks-save-btn', function() {
        if (!remarksUpdateUrl) return;

        var $cell = $(this).closest('.placement-remarks-cell');
        var $saveBtn = $(this);
        var value = $cell.find('.placement-remarks-input').val().trim();

        $saveBtn.prop('disabled', true);
        $.ajax({
            url: remarksUpdateUrl,
            type: 'PATCH',
            data: { remarks: value, _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function(res) {
                var newVal = (res.remarks != null && res.remarks !== undefined) ? String(res.remarks) : '';
                $cell.find('.placement-remarks-display').text(newVal || '—');
                $cell.find('.placement-remarks-input').val('');
                $cell.find('.placement-remarks-edit-wrap').addClass('d-none');
                $cell.find('.placement-remarks-display').removeClass('d-none');
                $cell.find('.placement-remarks-edit-btn').removeClass('d-none');
                if (res.history) {
                    $('#placement-remarks-history-tbody .placement-remarks-history-empty').remove();
                    var $tr = $('<tr>').addClass('placement-remarks-history-row').attr('data-history-id', res.history.id);
                    var remarkText = res.history.remarks !== '' && res.history.remarks != null ? res.history.remarks : '—';
                    $tr.append(
                        $('<td>').addClass('placement-remarks-history-td align-top text-nowrap').text(res.history.created_at),
                        $('<td>').addClass('placement-remarks-history-td align-top').text(res.history.user_name),
                        $('<td>').addClass('placement-remarks-history-td align-top placement-remarks-history-remark-cell text-break').text(remarkText)
                    );
                    $('#placement-remarks-history-tbody').prepend($tr);
                    var n = $('#placement-remarks-history-tbody tr.placement-remarks-history-row').length;
                    $('#placement-remarks-history-count').text(n + ' ' + (n === 1 ? 'entry' : 'entries'));
                }
            },
            complete: function() {
                $saveBtn.prop('disabled', false);
            }
        });
    });

    var $card = $('#scheduledInterviewsCard');
    var statusUrlTemplate = $card.data('status-url');
    if (!statusUrlTemplate) return;
    $(document).on('change', '.interview-status-select', function() {
        var $sel = $(this);
        var interviewId = $sel.data('interview-id');
        var status = $sel.val();
        var url = statusUrlTemplate.replace('__ID__', interviewId);
        $sel.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'PATCH',
            data: { status: status, _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function() { $sel.prop('disabled', false); },
            error: function() { $sel.prop('disabled', false); }
        });
    });
});
</script>
@endpush
