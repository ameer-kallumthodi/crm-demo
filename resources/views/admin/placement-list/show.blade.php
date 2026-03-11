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
                <div class="d-flex justify-content-end mt-2">
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

@push('scripts')
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
