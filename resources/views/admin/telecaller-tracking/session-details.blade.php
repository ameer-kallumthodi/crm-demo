@extends('layouts.mantis')

@section('title', 'Session Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/telecaller-tracking.css') }}">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    .dropdown-menu {
        z-index: 1050 !important;
    }
    @media print {
        .no-print {
            display: none !important;
        }
    }
    
    /* Table Styling */
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 12px 8px;
    }
    
    .table tbody td {
        padding: 10px 8px;
        vertical-align: middle;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
    
    .card {
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
    }
    
    .card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    /* DataTable Styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #007bff !important;
        color: white !important;
        border-color: #007bff !important;
    }
</style>
@endpush

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Session Details</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Telecaller Tracking</li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.telecaller-tracking.reports') }}">Reports</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.telecaller-tracking.telecaller-report', $session->user_id) }}">Telecaller Report</a></li>
                    <li class="breadcrumb-item">Session Details</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card telecaller-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="avtar avtar-s rounded-circle bg-light-primary me-3 d-flex align-items-center justify-content-center">
                            <i class="ti ti-clock f-20 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Session Details</h5>
                            <small class="text-muted">Session ID: {{ $session->session_id }}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.telecaller-tracking.telecaller-report', $session->user_id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-arrow-left me-1"></i> Back to Report
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Session Information -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-user f-24 text-primary"></i>
                                </div>
                                <h6 class="mb-2 text-muted">User</h6>
                                <h5 class="mb-0 text-primary fw-bold">{{ $session->user->name ?? 'N/A' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-login f-24 text-success"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Login Time</h6>
                                <h6 class="mb-0 text-success fw-bold">{{ $session->login_time->format('M d, Y g:i A') }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-clock f-24 text-primary"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Total Duration</h6>
                                <h5 class="mb-0 text-primary fw-bold">
                                    @php
                                        $totalMinutes = $sessionStats['total_duration_minutes'];
                                        $hours = floor($totalMinutes / 60);
                                        $minutes = floor($totalMinutes % 60);
                                        $seconds = floor(($totalMinutes % 1) * 60);
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-logout f-24 text-warning"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Logout Time</h6>
                                <h6 class="mb-0 text-warning fw-bold">
                                    @if($session->logout_time)
                                        {{ $session->logout_time->format('M d, Y g:i A') }}
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-clock f-24 text-info"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Duration</h6>
                                <h5 class="mb-0 text-info fw-bold">
                                    @php
                                        $totalMinutes = $session->total_duration_minutes ?: $session->calculateTotalDuration();
                                        $hours = floor($totalMinutes / 60);
                                        $minutes = floor($totalMinutes % 60);
                                        $seconds = floor(($totalMinutes % 1) * 60);
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Session Details -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-activity f-24 text-success"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Active Time</h6>
                                <h5 class="mb-0 text-success fw-bold">
                                    @php
                                        $activeMinutes = $sessionStats['active_duration_minutes'];
                                        $hours = floor($activeMinutes / 60);
                                        $minutes = floor($activeMinutes % 60);
                                        $seconds = floor(($activeMinutes % 1) * 60);
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-clock f-24 text-warning"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Idle Time</h6>
                                <h5 class="mb-0 text-warning fw-bold">
                                    @php
                                        $idleSeconds = $session->idleTimes()->sum('idle_duration_seconds');
                                        $hours = floor($idleSeconds / 3600);
                                        $minutes = floor(($idleSeconds % 3600) / 60);
                                        $seconds = $idleSeconds % 60;
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-shield f-24 text-info"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Logout Type</h6>
                                <h6 class="mb-0">
                                    <span class="badge {{ $session->logout_type == 'manual' ? 'bg-success' : ($session->logout_type == 'auto' ? 'bg-warning' : 'bg-info') }}">
                                        {{ ucfirst($session->logout_type) }}
                                    </span>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-world f-24 text-secondary"></i>
                                </div>
                                <h6 class="mb-2 text-muted">IP Address</h6>
                                <h6 class="mb-0 text-secondary fw-bold">{{ $session->ip_address ?? 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Logs Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Activity Logs ({{ $activityLogs->count() }})</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable" id="activityLogsTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Time</th>
                                        <th>Activity</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Page URL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activityLogs as $index => $log)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $log->activity_time->format('M d, Y g:i:s A') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $log->activity_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $log->activity_type }}</span>
                                        </td>
                                        <td>{{ $log->description ?? 'N/A' }}</td>
                                        <td>
                                            @if($log->page_url)
                                                <span class="text-muted">{{ Str::limit($log->page_url, 40) }}</span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                                                <h5>No activity logs found</h5>
                                                <p>No activity logs found for this session.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Idle Times Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Idle Times ({{ $idleTimes->count() }})</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable" id="idleTimesTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Duration</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($idleTimes as $index => $idle)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $idle->idle_start_time->format('M d, Y g:i:s A') }}</td>
                                        <td>
                                            @if($idle->idle_end_time)
                                                {{ $idle->idle_end_time->format('M d, Y g:i:s A') }}
                                            @else
                                                <span class="badge bg-warning">Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $idleSeconds = $idle->idle_duration_seconds;
                                                $hours = floor($idleSeconds / 3600);
                                                $minutes = floor(($idleSeconds % 3600) / 60);
                                                $seconds = $idleSeconds % 60;
                                            @endphp
                                            {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $idle->idle_type ?? 'general' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $idle->is_active ? 'bg-warning' : 'bg-success' }}">
                                                {{ $idle->is_active ? 'Active' : 'Ended' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                                                <h5>No idle times found</h5>
                                                <p>No idle times found for this session.</p>
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
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable for activity logs
    if ($('#activityLogsTable').length && !$.fn.DataTable.isDataTable('#activityLogsTable')) {
        // Check if table has actual data rows (not empty message with colspan)
        const hasDataRows = $('#activityLogsTable tbody tr').length > 0 && 
                           !$('#activityLogsTable tbody tr td[colspan]').length;
        
        if (hasDataRows) {
            try {
                $('#activityLogsTable').DataTable({
                    "processing": true,
                    "serverSide": false,
                    "responsive": true,
                    "pageLength": 10,
                    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    "columnDefs": [
                        { "orderable": false, "targets": [0] },
                        { "className": "text-center", "targets": [0, 2, 3] }
                    ],
                    "language": {
                        "processing": "Loading activity logs...",
                        "emptyTable": "No activity logs found",
                        "zeroRecords": "No matching activity logs found",
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing activity logs DataTable:', error);
            }
        } else {
            console.log('No data rows found in activity logs table, skipping DataTable initialization');
        }
    }

    // Initialize DataTable for idle times
    if ($('#idleTimesTable').length && !$.fn.DataTable.isDataTable('#idleTimesTable')) {
        // Check if table has actual data rows (not empty message with colspan)
        const hasDataRows = $('#idleTimesTable tbody tr').length > 0 && 
                           !$('#idleTimesTable tbody tr td[colspan]').length;
        
        if (hasDataRows) {
            try {
                $('#idleTimesTable').DataTable({
                    "processing": true,
                    "serverSide": false,
                    "responsive": true,
                    "pageLength": 10,
                    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    "columnDefs": [
                        { "orderable": false, "targets": [0] },
                        { "className": "text-center", "targets": [0, 4, 5] }
                    ],
                    "language": {
                        "processing": "Loading idle times...",
                        "emptyTable": "No idle times found",
                        "zeroRecords": "No matching idle times found",
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing idle times DataTable:', error);
            }
        } else {
            console.log('No data rows found in idle times table, skipping DataTable initialization');
        }
    }
});
</script>
@endpush
