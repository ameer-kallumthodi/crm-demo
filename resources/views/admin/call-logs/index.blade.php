@extends('layouts.mantis')

@section('title', 'Call Logs')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Call Logs Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Call Management</li>
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
                    <h5 class="mb-0">Call Logs</h5>
                    <button type="button" class="btn btn-primary btn-sm" onclick="refreshCallLogs()">
                        <i class="ti ti-refresh"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters Form -->
                <form method="GET" action="{{ route('admin.call-logs.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Call Type</label>
                            <select class="form-select" name="type">
                                <option value="">All Types</option>
                                <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                <option value="outgoing" {{ request('type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                <option value="missedcall" {{ request('type') == 'missedcall' ? 'selected' : '' }}>Missed Call</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="ANSWER" {{ request('status') == 'ANSWER' ? 'selected' : '' }}>Answered</option>
                                <option value="CANCEL" {{ request('status') == 'CANCEL' ? 'selected' : '' }}>Cancelled</option>
                                <option value="BUSY" {{ request('status') == 'BUSY' ? 'selected' : '' }}>Busy</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Agent Number</label>
                            <input type="text" class="form-control" name="agent_number" value="{{ request('agent_number') }}" placeholder="Agent Number">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Destination</label>
                            <input type="text" class="form-control" name="destination_number" value="{{ request('destination_number') }}" placeholder="Destination">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.call-logs.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $callLogs->total() }}</h4>
                                        <p class="mb-0">Total Calls</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="ti ti-phone fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $callLogs->where('status', 'ANSWER')->count() }}</h4>
                                        <p class="mb-0">Answered</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="ti ti-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $callLogs->whereIn('status', ['CANCEL', 'cancelled'])->count() }}</h4>
                                        <p class="mb-0">Cancelled</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="ti ti-x-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0">{{ $callLogs->where('status', 'BUSY')->count() }}</h4>
                                        <p class="mb-0">Busy</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="ti ti-phone-off fa-2x"></i>
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
                                <th>Destination</th>
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
                                <td>{{ $callLog->destinationNumber ?? $callLog->calledNumber }}</td>
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
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.call-logs.show', $callLog) }}" class="btn btn-sm btn-primary" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteCallLog({{ $callLog->id }})" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-phone-off fa-3x mb-3"></i>
                                        <p class="mb-0">No call logs found</p>
                                        <small>Try adjusting your filters or check back later</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($callLogs->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $callLogs->appends(request()->query())->links() }}
                </div>
                @endif
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

function deleteCallLog(callLogId) {
    if (confirm('Are you sure you want to delete this call log?')) {
        $.ajax({
            url: `/admin/call-logs/${callLogId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the call log');
            }
        });
    }
}
</script>
@endsection
