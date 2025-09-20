@extends('layouts.mantis')

@section('title', 'View Converted Lead')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Converted Lead Details</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.converted-leads.index') }}">Converted Leads</a></li>
                        <li class="breadcrumb-item">View</li>
                    </ul>
                    <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Back to List
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
                <h5 class="mb-0">Converted Lead Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Personal Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avtar avtar-s rounded-circle bg-light-success me-2" style="width: 60px; height: 60px;">
                                        <span class="text-white fw-bold" style="font-size: 1.5rem;">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">{{ $convertedLead->name }}</h4>
                                        <p class="text-muted mb-0">Converted Lead</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">Phone</label>
                                <p class="fw-bold">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">Email</label>
                                <p class="fw-bold">{{ $convertedLead->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">Remarks</label>
                                <p class="fw-bold">{{ $convertedLead->remarks ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Academic Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted">Course</label>
                                <p class="fw-bold">{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Academic Assistant</label>
                                <p class="fw-bold">{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Lead Information -->
                    @if($convertedLead->lead)
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3">Original Lead Information</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead ID</label>
                                <p class="fw-bold">#{{ $convertedLead->lead->id }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead Name</label>
                                <p class="fw-bold">{{ $convertedLead->lead->title }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead Source</label>
                                <p class="fw-bold">{{ $convertedLead->lead->leadSource ? $convertedLead->lead->leadSource->title : 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Lead Status</label>
                                <p class="fw-bold">{{ $convertedLead->lead->leadStatus ? $convertedLead->lead->leadStatus->title : 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Interest Status</label>
                                <p class="fw-bold">
                                    @if($convertedLead->lead->interest_status)
                                        <span class="badge bg-{{ $convertedLead->lead->interest_status_color }}">{{ $convertedLead->lead->interest_status_label }}</span>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Conversion Information -->
                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary mb-3">Conversion Information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Converted By</label>
                                <p class="fw-bold">{{ $convertedLead->createdBy ? $convertedLead->createdBy->name : 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Converted Date</label>
                                <p class="fw-bold">{{ $convertedLead->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Last Updated</label>
                                <p class="fw-bold">{{ $convertedLead->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Lead Activities History -->
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lead Activities History</h5>
            </div>
            <div class="card-body">
                @if($leadActivities->count() > 0)
                    <div class="timeline">
                        @foreach($leadActivities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <div class="avtar avtar-s rounded-circle bg-light-{{ $activity->activity_type === 'converted' ? 'success' : 'primary' }}">
                                    <i class="ti ti-{{ $activity->activity_type === 'converted' ? 'check' : 'activity' }} f-16"></i>
                                </div>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</h6>
                                        <p class="mb-1 text-muted">{{ $activity->description }}</p>
                                        @if($activity->reason)
                                            <p class="mb-1"><strong>Reason:</strong> <span class="badge bg-info">{{ $activity->formatted_reason }}</span></p>
                                        @endif
                                        @if($activity->rating)
                                            <p class="mb-1"><strong>Rating:</strong> <span class="badge bg-success">{{ $activity->rating }}/10</span></p>
                                        @endif
                                        @if($activity->lead_status_id == 2 && $activity->followup_date)
                                            <p class="mb-1"><strong>Followup Date:</strong> <span class="badge bg-warning">{{ $activity->followup_date->format('d M Y') }}</span></p>
                                        @endif
                                        @if($activity->remarks)
                                            <p class="mb-1"><small class="text-info">{{ $activity->remarks }}</small></p>
                                        @endif
                                        @if($activity->leadStatus)
                                            <span class="badge bg-light-{{ \App\Helpers\StatusHelper::getLeadStatusColor($activity->leadStatus->id) }}">
                                                {{ $activity->leadStatus->title }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $activity->created_at->format('M d, Y h:i A') }}</small>
                                        @if($activity->createdBy)
                                            <p class="mb-0"><small class="text-muted">by {{ $activity->createdBy->name }}</small></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-activity f-48 mb-3"></i>
                        <h6>No Activities Found</h6>
                        <p class="mb-0">No activities have been recorded for this lead.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    z-index: 1;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #7366ff;
    margin-left: 10px;
}

.timeline-item:last-child .timeline::before {
    display: none;
}
</style>
@endpush
