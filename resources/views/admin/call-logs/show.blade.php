@extends('layouts.mantis')

@section('title', 'Call Log Details')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Call Log Details</h5>
                    <p class="m-b-0">Detailed information about call #{{ $callLog->id }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.call-logs.index') }}">Call Logs</a></li>
                    <li class="breadcrumb-item">Details</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- Call Status Card -->
    <div class="col-xl-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        @php
                            $status = $callLog->status ?? 'Unknown';
                            $statusUpper = strtoupper($status);
                            $statusFormatted = ucfirst(strtolower($status));
                        @endphp
                        <div class="avtar avtar-s bg-light-{{ $statusUpper == 'ANSWER' ? 'success' : ($statusUpper == 'BUSY' ? 'warning' : ($statusUpper == 'CANCEL' || $statusUpper == 'CANCELLED' ? 'danger' : 'secondary')) }}">
                            <i class="ti ti-phone-{{ $statusUpper == 'ANSWER' ? 'check' : ($statusUpper == 'BUSY' ? 'busy' : 'off') }} text-{{ $statusUpper == 'ANSWER' ? 'success' : ($statusUpper == 'BUSY' ? 'warning' : ($statusUpper == 'CANCEL' || $statusUpper == 'CANCELLED' ? 'danger' : 'secondary')) }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Call Status</h6>
                        <h4 class="mb-0">
                            @if($statusUpper == 'ANSWER')
                                <span class="text-success">{{ $statusFormatted }}</span>
                            @elseif($statusUpper == 'BUSY')
                                <span class="text-warning">{{ $statusFormatted }}</span>
                            @elseif($statusUpper == 'CANCEL' || $statusUpper == 'CANCELLED')
                                <span class="text-danger">{{ $statusFormatted }}</span>
                            @else
                                <span class="text-secondary">{{ $statusFormatted }}</span>
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call Type Card -->
    <div class="col-xl-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-{{ $callLog->type == 'incoming' ? 'info' : ($callLog->type == 'outgoing' ? 'success' : 'warning') }}">
                            <i class="ti ti-arrow-{{ $callLog->type == 'incoming' ? 'down' : 'up' }} text-{{ $callLog->type == 'incoming' ? 'info' : ($callLog->type == 'outgoing' ? 'success' : 'warning') }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Call Type</h6>
                        <h4 class="mb-0 text-capitalize">{{ $callLog->type }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Duration Card -->
    <div class="col-xl-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-primary">
                            <i class="ti ti-clock text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Duration</h6>
                        <h4 class="mb-0">{{ $callLog->formatted_duration }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Call Information -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-phone me-2"></i>Call Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label text-muted">Call ID</label>
                        <p class="fw-bold mb-0">#{{ $callLog->id }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Call UUID</label>
                        <p class="fw-bold mb-0 text-truncate" title="{{ $callLog->call_uuid }}">{{ $callLog->call_uuid }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Date</label>
                        <p class="fw-bold mb-0">{{ $callLog->date ? $callLog->date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Time</label>
                        <p class="fw-bold mb-0">{{ $callLog->start_time ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">End Time</label>
                        <p class="fw-bold mb-0">{{ $callLog->end_time ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Status</label>
                        <div class="mb-0">
                            @php
                                $status = $callLog->status ?? 'Unknown';
                                $statusUpper = strtoupper($status);
                                $statusFormatted = ucfirst(strtolower($status));
                            @endphp
                            @if($statusUpper == 'ANSWER')
                                <span class="badge bg-light-success text-success">
                                    <i class="ti ti-check"></i> {{ $statusFormatted }}
                                </span>
                            @elseif($statusUpper == 'BUSY')
                                <span class="badge bg-light-warning text-warning">
                                    <i class="ti ti-phone-busy"></i> {{ $statusFormatted }}
                                </span>
                            @elseif($statusUpper == 'CANCEL' || $statusUpper == 'CANCELLED')
                                <span class="badge bg-light-danger text-danger">
                                    <i class="ti ti-x"></i> {{ $statusFormatted }}
                                </span>
                            @elseif($statusUpper == 'NO ANSWER')
                                <span class="badge bg-light-secondary text-secondary">
                                    <i class="ti ti-phone-off"></i> {{ $statusFormatted }}
                                </span>
                            @else
                                <span class="badge bg-light-secondary text-secondary">
                                    {{ $statusFormatted }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-user me-2"></i>Contact Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label text-muted">Agent Number</label>
                        <p class="fw-bold mb-0">{{ $callLog->AgentNumber ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Telecaller</label>
                        <p class="fw-bold mb-0">{{ $callLog->telecaller_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Extension</label>
                        <p class="fw-bold mb-0">{{ $callLog->extensionNumber ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Called Number</label>
                        <p class="fw-bold mb-0">{{ $callLog->calledNumber ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Caller Number</label>
                        <p class="fw-bold mb-0">{{ $callLog->callerNumber ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Destination</label>
                        <p class="fw-bold mb-0">{{ $callLog->destinationNumber ?? 'N/A' }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted">Caller ID</label>
                        <p class="fw-bold mb-0">{{ $callLog->callerid ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recording Section -->
@if($callLog->recording_URL)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-microphone me-2"></i>Call Recording
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                    <div class="avtar avtar-s bg-light-primary me-3">
                        <i class="ti ti-volume text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Call Recording Available</h6>
                        <p class="text-muted mb-0">Listen to the recorded call below</p>
                    </div>
                </div>
                <div class="audio-player-container">
                    <audio controls class="w-100" style="max-width: 100%;">
                        <source src="{{ $callLog->recording_URL }}" type="audio/mpeg">
                        <source src="{{ $callLog->recording_URL }}" type="audio/wav">
                        <source src="{{ $callLog->recording_URL }}" type="audio/ogg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Related Lead Information -->
@if($lead)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>Related Lead Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted">Lead Name</label>
                        <p class="fw-bold mb-0">{{ $lead->title }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Phone</label>
                        <p class="fw-bold mb-0">{{ $lead->code }}{{ $lead->phone }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Email</label>
                        <p class="fw-bold mb-0">{{ $lead->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Status</label>
                        <div class="mb-0">
                            @if($lead->leadStatus)
                                <span class="badge bg-light-{{ $lead->leadStatus->id == 1 ? 'primary' : ($lead->leadStatus->id == 2 ? 'warning' : 'success') }} text-{{ $lead->leadStatus->id == 1 ? 'primary' : ($lead->leadStatus->id == 2 ? 'warning' : 'success') }}">
                                    {{ $lead->leadStatus->title }}
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Source</label>
                        <p class="fw-bold mb-0">{{ $lead->leadSource->title ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- System Information -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-settings me-2"></i>System Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted">Created At</label>
                        <p class="fw-bold mb-0">{{ $callLog->created_at ? $callLog->created_at->format('M d, Y H:i:s') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted">Updated At</label>
                        <p class="fw-bold mb-0">{{ $callLog->updated_at ? $callLog->updated_at->format('M d, Y H:i:s') : 'N/A' }}</p>
                    </div>
                    @if($callLog->createdBy)
                    <div class="col-md-3">
                        <label class="form-label text-muted">Created By</label>
                        <p class="fw-bold mb-0">{{ $callLog->createdBy->name }}</p>
                    </div>
                    @endif
                    @if($callLog->updatedBy)
                    <div class="col-md-3">
                        <label class="form-label text-muted">Updated By</label>
                        <p class="fw-bold mb-0">{{ $callLog->updatedBy->name }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.call-logs.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Call Logs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection
