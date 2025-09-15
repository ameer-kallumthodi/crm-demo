@extends('layouts.mantis')

@section('title', 'Call Logs for ' . $lead->title)

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Call Logs for Lead: {{ $lead->title }}</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item">{{ $lead->title }}</li>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Call Logs for Lead: {{ $lead->title }}
                        <small class="text-muted">({{ $lead->code }}{{ $lead->phone }})</small>
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('leads.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ti ti-arrow-left"></i> Back to Leads
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" onclick="refreshCallLogs()">
                            <i class="ti ti-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
                <div class="card-body">
                    <!-- Lead Information -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Name:</strong> {{ $lead->title }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Phone:</strong> {{ $lead->code }}{{ $lead->phone }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Email:</strong> {{ $lead->email ?? 'N/A' }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Status:</strong> 
                                            <span class="badge badge-{{ $lead->leadStatus->color ?? 'secondary' }}">
                                                {{ $lead->leadStatus->title ?? 'Unknown' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Call Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Telecaller</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Recording</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($callLogs as $callLog)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-{{ $callLog->type == 'incoming' ? 'success' : ($callLog->type == 'outgoing' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($callLog->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $callLog->telecaller_name }}</td>
                                    <td>{{ $callLog->formatted_duration }}</td>
                                    <td>
                                        @php
                                            $status = strtoupper($callLog->status ?? 'UNKNOWN');
                                            $badgeClass = match($status) {
                                                'ANSWER' => 'bg-success',
                                                'CANCEL', 'cancelled' => 'bg-warning',
                                                'BUSY' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td>{{ $callLog->date ? $callLog->date->format('Y-m-d') : 'N/A' }}</td>
                                    <td>{{ $callLog->start_time ? $callLog->start_time->format('H:i:s') : 'N/A' }}</td>
                                    <td>
                                        @if($callLog->recording_URL)
                                            <a href="{{ $callLog->recording_URL }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="ti ti-play"></i> Play
                                            </a>
                                        @else
                                            <span class="text-muted">No Recording</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.call-logs.show', $callLog) }}" class="btn btn-sm btn-primary" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-phone-off fa-3x mb-3"></i>
                                            <p class="mb-0">No call logs found for this lead</p>
                                            <small>Call logs will appear here when calls are made to this lead</small>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Call Summary -->
                    @if($callLogs->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Call Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-primary mb-1">{{ $callLogs->where('type', 'outgoing')->count() }}</h4>
                                                <p class="mb-0 text-muted">Outgoing Calls</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-success mb-1">{{ $callLogs->where('type', 'incoming')->count() }}</h4>
                                                <p class="mb-0 text-muted">Incoming Calls</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-success mb-1">{{ $callLogs->where('status', 'ANSWER')->count() }}</h4>
                                                <p class="mb-0 text-muted">Answered Calls</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-warning mb-1">{{ $callLogs->where('status', 'CANCEL')->count() + $callLogs->where('status', 'cancelled')->count() }}</h4>
                                                <p class="mb-0 text-muted">Cancelled Calls</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('scripts')
<script>
function refreshCallLogs() {
    location.reload();
}
</script>
@endsection
