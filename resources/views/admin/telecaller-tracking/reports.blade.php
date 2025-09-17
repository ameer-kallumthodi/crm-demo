@extends('layouts.mantis')

@section('title', 'Telecaller Tracking Reports')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Detailed Telecaller Reports</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Telecaller Tracking</li>
                    <li class="breadcrumb-item">Reports</li>
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
                    <h5 class="mb-0">Session Reports</h5>
                    <a href="{{ route('admin.telecaller-tracking.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- [ Date Filter ] start -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.telecaller-tracking.reports') }}" id="dateFilterForm">
                                    <div class="row align-items-end">
                                        <div class="col-md-2">
                                            <label for="start_date" class="form-label">From Date</label>
                                            <input type="date" class="form-control" name="start_date" 
                                                   value="{{ $startDate }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="end_date" class="form-label">To Date</label>
                                            <input type="date" class="form-control" name="end_date" 
                                                   value="{{ $endDate }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="telecaller_id" class="form-label">Telecaller</label>
                                            <select class="form-select" name="telecaller_id" id="telecaller_id">
                                                <option value="">All Telecallers</option>
                                                @foreach($telecallers as $telecaller)
                                                    <option value="{{ $telecaller->id }}" {{ $telecallerId == $telecaller->id ? 'selected' : '' }}>
                                                        {{ $telecaller->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ti ti-filter"></i> Filter
                                                </button>
                                                <a href="{{ route('admin.telecaller-tracking.reports') }}" class="btn btn-outline-secondary">
                                                    <i class="ti ti-x"></i> Clear
                                                </a>
                                                <a href="{{ route('admin.telecaller-tracking.export.excel', request()->query()) }}" 
                                                   class="btn btn-outline-success">
                                                    <i class="ti ti-file-excel"></i> Export Excel
                                                </a>
                                                <a href="{{ route('admin.telecaller-tracking.export.pdf', request()->query()) }}" 
                                                   class="btn btn-outline-danger">
                                                    <i class="ti ti-file-pdf"></i> Export PDF
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ Date Filter ] end -->

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover datatable" id="sessionsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Actions</th>
                                <th>Telecaller</th>
                                <th>Login Time</th>
                                <th>Logout Time</th>
                                <th>Duration</th>
                                <th>Active Time</th>
                                <th>Idle Time</th>
                                <th>Logout Type</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $index => $session)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.telecaller-tracking.telecaller-report', $session->user_id) }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                            <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($session->user->name ?? 'U', 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $session->user->name ?? 'Unknown User' }}</h6>
                                            <small class="text-muted">{{ $session->user->email ?? 'No email' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $session->login_time->format('M d, Y H:i:s') }}</td>
                                <td>
                                    @if($session->logout_time)
                                        {{ $session->logout_time->format('M d, Y H:i:s') }}
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @if($session->total_duration_minutes)
                                        {{ number_format($session->total_duration_minutes / 60, 1) }}h
                                    @else
                                        {{ number_format($session->calculateTotalDuration() / 60, 1) }}h
                                    @endif
                                </td>
                                <td>
                                    @if($session->active_duration_minutes)
                                        {{ number_format($session->active_duration_minutes / 60, 1) }}h
                                    @else
                                        {{ number_format($session->calculateActiveDuration() / 60, 1) }}h
                                    @endif
                                </td>
                                <td>
                                    @if($session->idle_duration_minutes)
                                        {{ number_format($session->idle_duration_minutes / 60, 1) }}h
                                    @else
                                        {{ number_format($session->idleTimes()->sum('idle_duration_seconds') / 3600, 1) }}h
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $session->logout_type == 'manual' ? 'bg-primary' : ($session->logout_type == 'auto' ? 'bg-warning' : 'bg-secondary') }}">
                                        {{ ucfirst($session->logout_type) }}
                                    </span>
                                </td>
                                <td>{{ $session->ip_address ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                                        No sessions found
                                    </div>
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
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable for telecaller reports
    if ($('#sessionsTable').length && !$.fn.DataTable.isDataTable('#sessionsTable')) {
        $('#sessionsTable').DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "columnDefs": [
                { "orderable": false, "targets": [0, 1] }, // Disable sorting on serial number and actions columns
                { "searchable": false, "targets": [0, 1] } // Disable searching on serial number and actions columns
            ],
            "language": {
                "processing": "Loading sessions...",
                "emptyTable": "No sessions found",
                "zeroRecords": "No matching sessions found",
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
        });
    }
});
</script>
@endpush
