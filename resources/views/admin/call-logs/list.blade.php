@extends('layouts.mantis')

@section('title', 'Call Logs - ' . $lead->title)

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Call Logs</h5>
                    <p class="m-b-0">Call history for {{ $lead->title }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item">Call Logs</li>
                </ul>
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
                <h5 class="mb-0">
                    <i class="ti ti-phone me-2"></i>Call Logs for Lead: {{ $lead->title }}
                    <small class="text-muted">({{ $lead->code }}{{ $lead->phone }})</small>
                </h5>
                <div class="card-header-right">
                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Leads
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Lead Information -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-light-info border border-info">
                            <div class="d-flex align-items-center">
                                <div class="avtar avtar-s bg-light-info me-3">
                                    <i class="ti ti-info-circle text-info"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">Lead Information</h6>
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Name</small>
                                            <strong>{{ $lead->title }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Phone</small>
                                            <strong>{{ $lead->code }}{{ $lead->phone }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Email</small>
                                            <strong>{{ $lead->email ?? 'N/A' }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Status</small>
                                            <strong>{{ $lead->leadStatus->title ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('leads.call-logs', $lead) }}" class="row g-3">
                            <div class="col-md-2">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select form-select-sm">
                                    <option value="">All Types</option>
                                    <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                    <option value="outgoing" {{ request('type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                    <option value="missedcall" {{ request('type') == 'missedcall' ? 'selected' : '' }}>Missed Call</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="ANSWER" {{ request('status') == 'ANSWER' ? 'selected' : '' }}>Answered</option>
                                    <option value="BUSY" {{ request('status') == 'BUSY' ? 'selected' : '' }}>Busy</option>
                                    <option value="CANCEL" {{ request('status') == 'CANCEL' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="NO ANSWER" {{ request('status') == 'NO ANSWER' ? 'selected' : '' }}>No Answer</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" 
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" 
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ti ti-search"></i> Filter
                                </button>
                                <a href="{{ route('leads.call-logs', $lead) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-x"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Call Logs Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Agent Number</th>
                                <th>Telecaller</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Recording</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($callLogs as $callLog)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary">#{{ $callLog->id }}</span>
                                </td>
                                <td>
                                    @if($callLog->type == 'incoming')
                                        <span class="badge bg-light-info text-info">
                                            <i class="ti ti-arrow-down"></i> Incoming
                                        </span>
                                    @elseif($callLog->type == 'outgoing')
                                        <span class="badge bg-light-success text-success">
                                            <i class="ti ti-arrow-up"></i> Outgoing
                                        </span>
                                    @else
                                        <span class="badge bg-light-warning text-warning">
                                            <i class="ti ti-phone-off"></i> Missed
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $callLog->AgentNumber ?? 'N/A' }}</td>
                                <td>{{ $callLog->telecaller_name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $status = $callLog->status ?? 'Unknown';
                                        $statusFormatted = ucfirst(strtolower($status));
                                        $statusUpper = strtoupper($status);
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
                                </td>
                                <td>{{ $callLog->formatted_duration }}</td>
                                <td>{{ $callLog->date ? $callLog->date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $callLog->start_time ?? 'N/A' }}</td>
                                <td>
                                    @if($callLog->recording_URL)
                                        <button type="button" class="btn btn-sm btn-primary" onclick="toggleRecording('recording-{{ $callLog->id }}')">
                                            <i class="ti ti-play"></i> Play
                                        </button>
                                        <div id="recording-{{ $callLog->id }}" class="mt-2" style="display: none;">
                                            <audio controls class="w-100" style="max-width: 300px;">
                                                <source src="{{ $callLog->recording_URL }}" type="audio/mpeg">
                                                <source src="{{ $callLog->recording_URL }}" type="audio/wav">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.call-logs.show', $callLog) }}" class="btn btn-sm btn-info">
                                        <i class="ti ti-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avtar avtar-xl bg-light-secondary mb-2">
                                            <i class="ti ti-phone-off text-secondary"></i>
                                        </div>
                                        <p class="text-muted mb-0">No call logs found for this lead</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $callLogs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<script>
function toggleRecording(recordingId) {
    const recordingDiv = document.getElementById(recordingId);
    if (recordingDiv.style.display === 'none' || recordingDiv.style.display === '') {
        recordingDiv.style.display = 'block';
        // Auto-play the audio
        const audio = recordingDiv.querySelector('audio');
        if (audio) {
            audio.play().catch(e => console.log('Auto-play prevented:', e));
        }
    } else {
        recordingDiv.style.display = 'none';
        // Pause the audio when hiding
        const audio = recordingDiv.querySelector('audio');
        if (audio) {
            audio.pause();
        }
    }
}
</script>
@endpush