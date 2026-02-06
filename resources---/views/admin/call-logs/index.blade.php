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
                    <p class="m-b-0">Monitor and manage all call activities</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Call Logs</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-primary">
                            <i class="ti ti-phone-call text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Total Calls</h6>
                        <h4 class="mb-0">{{ $callLogs->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-success">
                            <i class="ti ti-phone-check text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Answered</h6>
                        <h4 class="mb-0">{{ $callLogs->where('call_status', 'ANSWER')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-warning">
                            <i class="ti ti-phone-off text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Missed</h6>
                        <h4 class="mb-0">{{ $callLogs->where('call_status', 'NO ANSWER')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-info">
                            <i class="ti ti-clock text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Avg Duration</h6>
                        <h4 class="mb-0">{{ $callLogs->where('call_status', 'ANSWER')->avg('duration') ? number_format($callLogs->where('call_status', 'ANSWER')->avg('duration'), 0) . 's' : '0s' }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Call Logs</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="exportCallLogs()">
                            <i class="ti ti-download"></i> Export
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="refreshCallLogs()">
                            <i class="ti ti-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Advanced Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body p-3">
                                <form method="GET" action="{{ route('admin.call-logs.index') }}" id="filterForm">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Call Type</label>
                                            <select name="type" id="type" class="form-select">
                                                <option value="">All Types</option>
                                                <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                                <option value="outgoing" {{ request('type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                                <option value="missedcall" {{ request('type') == 'missedcall' ? 'selected' : '' }}>Missed Call</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <select name="status" id="status" class="form-select">
                                                <option value="">All Status</option>
                                                <option value="ANSWER" {{ request('status') == 'ANSWER' ? 'selected' : '' }}>Answered</option>
                                                <option value="BUSY" {{ request('status') == 'BUSY' ? 'selected' : '' }}>Busy</option>
                                                <option value="CANCEL" {{ request('status') == 'CANCEL' ? 'selected' : '' }}>Cancelled</option>
                                                <option value="NO ANSWER" {{ request('status') == 'NO ANSWER' ? 'selected' : '' }}>No Answer</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">From Date</label>
                                            <input type="date" name="date_from" id="date_from" class="form-control" 
                                                   value="{{ request('date_from') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">To Date</label>
                                            <input type="date" name="date_to" id="date_to" class="form-control" 
                                                   value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary me-2">
                                                <i class="ti ti-search"></i> Apply Filters
                                            </button>
                                            <a href="{{ route('admin.call-logs.index') }}" class="btn btn-outline-secondary">
                                                <i class="ti ti-x"></i> Clear Filters
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call Logs Table -->
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Agent</th>
                                <th>Telecaller</th>
                                <th>Destination</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Date & Time</th>
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
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-xs bg-light-primary me-2">
                                            <i class="ti ti-phone text-primary"></i>
                                        </div>
                                        <span class="fw-medium">{{ $callLog->AgentNumber ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-xs bg-light-secondary me-2">
                                            <span class="text-secondary fw-bold">{{ substr($callLog->telecaller_name ?? 'N/A', 0, 1) }}</span>
                                        </div>
                                        <span class="fw-medium">{{ $callLog->telecaller_name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $callLog->destinationNumber ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($callLog->call_status == 'ANSWER')
                                        <span class="badge bg-light-success text-success">
                                            <i class="ti ti-check"></i> Answered
                                        </span>
                                    @elseif($callLog->call_status == 'BUSY')
                                        <span class="badge bg-light-warning text-warning">
                                            <i class="ti ti-phone-busy"></i> Busy
                                        </span>
                                    @elseif($callLog->call_status == 'CANCEL')
                                        <span class="badge bg-light-danger text-danger">
                                            <i class="ti ti-x"></i> Cancelled
                                        </span>
                                    @else
                                        <span class="badge bg-light-secondary text-secondary">
                                            <i class="ti ti-phone-off"></i> No Answer
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($callLog->call_status == 'ANSWER' && $callLog->duration)
                                        <span class="fw-bold text-success">{{ $callLog->formatted_duration }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $callLog->date ? $callLog->date->format('M d, Y') : 'N/A' }}</span>
                                        <small class="text-muted">{{ $callLog->start_time ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-outline-info btn-sm" 
                                                onclick="viewCallLog({{ $callLog->id }})" 
                                                title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteCallLog({{ $callLog->id }})" 
                                                title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avtar avtar-xl bg-light-secondary mb-3">
                                            <i class="ti ti-phone-off text-secondary"></i>
                                        </div>
                                        <h5 class="text-muted">No Call Logs Found</h5>
                                        <p class="text-muted">No call logs match your current filters.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($callLogs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $callLogs->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- View Call Log Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Call Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <div class="avtar avtar-s bg-light-danger me-3">
                        <i class="ti ti-alert-triangle text-danger"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Are you sure?</h6>
                        <p class="mb-0 text-muted">This action cannot be undone. The call log will be permanently deleted.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete Call Log</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewCallLog(callLogId) {
    // Load call log details via AJAX
    $.ajax({
        url: `{{ url('admin/call-logs') }}/${callLogId}`,
        type: 'GET',
        success: function(response) {
            $('#viewModalBody').html(response);
            $('#viewModal').modal('show');
        },
        error: function(xhr) {
            toast_error('Error loading call log details');
        }
    });
}

function deleteCallLog(callLogId) {
    $('#deleteModal').modal('show');
    
    $('#confirmDelete').off('click').on('click', function() {
        $.ajax({
            url: `{{ url('admin/call-logs') }}/${callLogId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    toast_success('Call log deleted successfully');
                    location.reload();
                } else {
                    toast_error('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                toast_error('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            }
        });
    });
}

function exportCallLogs() {
    // Get current filter parameters
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData);
    
    // Create export URL with current filters
    const exportUrl = `{{ route('admin.call-logs.index') }}?export=excel&${params.toString()}`;
    
    // Trigger download
    window.open(exportUrl, '_blank');
}

function refreshCallLogs() {
    location.reload();
}

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('#filterForm select, #filterForm input[type="date"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Auto-submit after a short delay
            setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    });
});
</script>
@endpush