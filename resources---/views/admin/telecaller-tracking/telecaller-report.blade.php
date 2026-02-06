@extends('layouts.mantis')

@section('title', 'Telecaller Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/telecaller-tracking.css') }}">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
    /* Simple Stats Cards */
    .stats-card {
        transition: all 0.2s ease;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .stats-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .stats-card-primary {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
    }
    
    .stats-card-success {
        background: #f8f9fa;
        border-left: 4px solid #28a745;
    }
    
    .stats-card-warning {
        background: #f8f9fa;
        border-left: 4px solid #ffc107;
    }
    
    .stats-card-info {
        background: #f8f9fa;
        border-left: 4px solid #17a2b8;
    }
    
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    
    .stats-card-primary .stats-icon {
        background: #e3f2fd;
        color: #007bff;
    }
    
    .stats-card-success .stats-icon {
        background: #e8f5e8;
        color: #28a745;
    }
    
    .stats-card-warning .stats-icon {
        background: #fff3cd;
        color: #ffc107;
    }
    
    .stats-card-info .stats-icon {
        background: #d1ecf1;
        color: #17a2b8;
    }
    
    .stats-card h2 {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 6px;
        color: #2c3e50;
    }
    
    .stats-card p {
        font-size: 0.95rem;
        font-weight: 500;
        margin-bottom: 4px;
        color: #6c757d;
    }
    
    .stats-card small {
        font-size: 0.8rem;
        color: #adb5bd;
        display: flex;
        align-items: center;
    }
    
    /* Task Statistics Cards */
    .metric-mini-card {
        transition: all 0.2s ease;
        border-radius: 8px;
        background: white;
        border: 1px solid #e9ecef;
    }
    
    .metric-mini-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .metric-mini-card h4 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .metric-mini-card h6 {
        font-size: 0.85rem;
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 8px;
    }
    
    .metric-mini-card small {
        font-size: 0.75rem;
        color: #adb5bd;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stats-card h2 {
            font-size: 1.5rem;
        }
        
        .stats-icon {
            width: 40px;
            height: 40px;
        }
        
        .metric-mini-card h4 {
            font-size: 1.3rem;
        }
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
                    <h5 class="m-b-10">Telecaller Report - {{ $telecaller->name }}</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Telecaller Tracking</li>
                    <li class="breadcrumb-item">Reports</li>
                    <li class="breadcrumb-item">{{ $telecaller->name }}</li>
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
                        <i class="ti ti-user"></i> Telecaller Report - {{ $telecaller->name }}
                    </h5>
                    <a href="{{ route('admin.telecaller-tracking.reports') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Reports
                    </a>
                </div>
            </div>
                <div class="card-body">
                    <!-- Date Range Filter -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.telecaller-tracking.telecaller-report', $telecaller->id) }}" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="start_date" class="mr-2">Start Date:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                                </div>
                                <div class="form-group mr-3">
                                    <label for="end_date" class="mr-2">End Date:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                    <div class="col-lg-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-clock f-24 text-primary"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Total Sessions</h6>
                                <h3 class="mb-0 text-primary fw-bold">{{ $stats['total_sessions'] }}</h3>
                                <small class="text-muted">All time sessions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-login f-24 text-success"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Login Time</h6>
                                <h3 class="mb-0 text-success fw-bold">
                                    @php
                                        // Convert hours to seconds for more accurate calculation
                                        $totalSeconds = $stats['total_login_hours'] * 3600;
                                        $hours = floor($totalSeconds / 3600);
                                        $minutes = floor(($totalSeconds % 3600) / 60);
                                        $seconds = $totalSeconds % 60;
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </h3>
                                <small class="text-muted">Total logged time</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-clock f-24 text-warning"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Idle Time</h6>
                                <h3 class="mb-0 text-warning fw-bold">
                                    @php
                                        $totalSeconds = $stats['total_idle_seconds'];
                                        $hours = floor($totalSeconds / 3600);
                                        $minutes = floor(($totalSeconds % 3600) / 60);
                                        $seconds = $totalSeconds % 60;
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </h3>
                                <small class="text-muted">Non-productive time</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-activity f-24 text-info"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Active Time</h6>
                                <h3 class="mb-0 text-info fw-bold">
                                    @php
                                        // Convert hours to seconds for more accurate calculation
                                        $totalSeconds = $stats['total_active_hours'] * 3600;
                                        $hours = floor($totalSeconds / 3600);
                                        $minutes = floor(($totalSeconds % 3600) / 60);
                                        $seconds = $totalSeconds % 60;
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </h3>
                                <small class="text-muted">Productive time</small>
                            </div>
                        </div>
                    </div>

                    <!-- Task Statistics -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center p-4">
                                    <div class="metric-icon mb-3">
                                        <i class="ti ti-list f-24 text-secondary"></i>
                                    </div>
                                    <h6 class="mb-2 text-muted">Total Leads</h6>
                                    <h3 class="mb-0 text-secondary fw-bold">{{ $stats['total_tasks'] }}</h3>
                                    <small class="text-muted">Assigned leads</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center p-4">
                                    <div class="metric-icon mb-3">
                                        <i class="ti ti-check f-24 text-success"></i>
                                    </div>
                                    <h6 class="mb-2 text-muted">Converted</h6>
                                    <h3 class="mb-0 text-success fw-bold">{{ $stats['completed_tasks'] }}</h3>
                                    <small class="text-muted">Successfully converted</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center p-4">
                                    <div class="metric-icon mb-3">
                                        <i class="ti ti-clock f-24 text-warning"></i>
                                    </div>
                                    <h6 class="mb-2 text-muted">Pending</h6>
                                    <h3 class="mb-0 text-warning fw-bold">{{ $stats['pending_tasks'] }}</h3>
                                    <small class="text-muted">In progress</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center p-4">
                                    <div class="metric-icon mb-3">
                                        <i class="ti ti-alert-triangle f-24 text-danger"></i>
                                    </div>
                                    <h6 class="mb-2 text-muted">Overdue</h6>
                                    <h3 class="mb-0 text-danger fw-bold">{{ $stats['overdue_tasks'] }}</h3>
                                    <small class="text-muted">Past due date</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-gradient-primary text-white">
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-s rounded-circle bg-light-white me-3 d-flex align-items-center justify-content-center">
                                            <i class="ti ti-chart-line f-20 text-warning"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0 text-white">Performance Analytics</h5>
                                            <small class="text-white-50">Detailed performance metrics and insights</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <!-- Productivity Score -->
                                        <div class="col-lg-12 mb-4">
                                            <div class="performance-metric-card">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h6 class="mb-1 text-primary fw-semibold">
                                                            <i class="ti ti-trending-up me-2"></i>Productivity Score
                                                        </h6>
                                                        <p class="text-muted mb-0 small">Based on task completion and activity</p>
                                                    </div>
                                                    <div class="text-end">
                                                        <h3 class="mb-0 text-primary fw-bold">{{ $stats['productivity_score'] }}%</h3>
                                                        <small class="text-muted">Score</small>
                                                    </div>
                                                </div>
                                                <div class="progress progress-lg position-relative" style="height: 20px;">
                                                    <div class="progress-bar bg-gradient-success d-flex align-items-center justify-content-center" 
                                                         style="width: {{ $stats['productivity_score'] }}%; border-radius: 10px;"
                                                         role="progressbar" 
                                                         aria-valuenow="{{ $stats['productivity_score'] }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <span class="text-white fw-bold">{{ $stats['productivity_score'] }}%</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2">
                                                    <small class="text-muted">0%</small>
                                                    <small class="text-muted">100%</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Metrics -->
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <div class="metric-mini-card text-center p-3 border rounded">
                                                        <div class="metric-icon mb-2">
                                                            <i class="ti ti-target f-24 text-success"></i>
                                                        </div>
                                                        <h6 class="mb-1">Conversion Rate</h6>
                                                        <h4 class="mb-0 text-success">
                                                            {{ $stats['total_tasks'] > 0 ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1) : 0 }}%
                                                        </h4>
                                                        <small class="text-muted">{{ $stats['completed_tasks'] }} of {{ $stats['total_tasks'] }} leads</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="metric-mini-card text-center p-3 border rounded">
                                                        <div class="metric-icon mb-2">
                                                            <i class="ti ti-clock f-24 text-warning"></i>
                                                        </div>
                                                        <h6 class="mb-1">Avg. Session Time</h6>
                                                        <h4 class="mb-0 text-warning">
                                                        @php
                                                            $avgHours = $stats['total_sessions'] > 0 ? $stats['total_login_hours'] / $stats['total_sessions'] : 0;
                                                            $totalSeconds = $avgHours * 3600;
                                                            $hours = floor($totalSeconds / 3600);
                                                            $minutes = floor(($totalSeconds % 3600) / 60);
                                                            $seconds = $totalSeconds % 60;
                                                        @endphp
                                                        {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                                        </h4>
                                                        <small class="text-muted">Per session</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="metric-mini-card text-center p-3 border rounded">
                                                        <div class="metric-icon mb-2">
                                                            <i class="ti ti-activity f-24 text-info"></i>
                                                        </div>
                                                        <h6 class="mb-1">Activity Ratio</h6>
                                                        <h4 class="mb-0 text-info">
                                                            {{ $stats['total_login_hours'] > 0 ? round(($stats['total_active_hours'] / $stats['total_login_hours']) * 100, 1) : 0 }}%
                                                        </h4>
                                                        <small class="text-muted">Active vs Total time</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sessions Table -->
                    <div class="card">
                        <div class="card-header">
                         <h3 class="card-title">Session History ({{ $sessions->count() }} Sessions)</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="telecallerSessionsTable">
                                    <thead>
                                        <tr>
                                        <th>#</th>
                                            <th>Login Time</th>
                                            <th>Logout Time</th>
                                            <th>Duration</th>
                                            <th>Active Time</th>
                                            <th>Idle Time</th>
                                            <th>Logout Type</th>
                                            <th>IP Address</th>
                                        <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     @forelse($sessions as $index => $session)
                                        <tr>
                                        <td>{{ $index + 1 }}</td>
                                            <td>
                                                @php
                                                    $loginTime = $session->login_time;
                                                    if ($loginTime) {
                                                        $formattedTime = $loginTime->setTimezone('Asia/Kolkata')->format('M d, Y g:i:s A');
                                                    } else {
                                                        $formattedTime = 'N/A';
                                                    }
                                                @endphp
                                                {{ $formattedTime }}
                                            </td>
                                        <td>
                                            @if($session->logout_time)
                                                @php
                                                    $logoutTime = $session->logout_time;
                                                    $formattedLogoutTime = $logoutTime->setTimezone('Asia/Kolkata')->format('M d, Y g:i:s A');
                                                @endphp
                                                {{ $formattedLogoutTime }}
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                // Calculate total duration more accurately
                                                if ($session->logout_time) {
                                                    $totalSeconds = $session->login_time->diffInSeconds($session->logout_time);
                                                } else {
                                                    $totalSeconds = $session->login_time->diffInSeconds(now());
                                                }
                                                $hours = floor($totalSeconds / 3600);
                                                $minutes = floor(($totalSeconds % 3600) / 60);
                                                $seconds = $totalSeconds % 60;
                                            @endphp
                                            {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                        </td>
                                        <td>
                                            @php
                                                // Calculate active duration (total duration - idle time)
                                                $totalDurationSeconds = $session->logout_time ? 
                                                    $session->login_time->diffInSeconds($session->logout_time) : 
                                                    $session->login_time->diffInSeconds(now());
                                                
                                                $idleSeconds = $session->idleTimes()->sum('idle_duration_seconds');
                                                $activeSeconds = max(0, $totalDurationSeconds - $idleSeconds);
                                                
                                                $hours = floor($activeSeconds / 3600);
                                                $minutes = floor(($activeSeconds % 3600) / 60);
                                                $seconds = $activeSeconds % 60;
                                            @endphp
                                            {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                        </td>
                                        <td>
                                            @php
                                                // Calculate idle time more accurately
                                                $idleSeconds = $session->idleTimes()->sum('idle_duration_seconds');
                                                
                                                // If no idle time recorded, try to calculate from session data
                                                if ($idleSeconds == 0 && $session->idle_duration_minutes) {
                                                    $idleSeconds = $session->idle_duration_minutes * 60;
                                                }
                                                
                                                $hours = floor($idleSeconds / 3600);
                                                $minutes = floor(($idleSeconds % 3600) / 60);
                                                $seconds = $idleSeconds % 60;
                                            @endphp
                                            {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                        </td>
                                        <td>
                                            <span class="badge {{ $session->logout_type == 'manual' ? 'bg-success' : ($session->logout_type == 'auto' ? 'bg-warning' : 'bg-info') }}">
                                                    {{ ucfirst($session->logout_type) }}
                                                </span>
                                            </td>
                                            <td>{{ $session->ip_address ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.telecaller-tracking.session-details', $session->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye"></i> Details
                                            </a>
                                        </td>
                                        </tr>
                                        @empty
                                        <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                                                <h5>No sessions found</h5>
                                                <p>No sessions found for the selected date range.</p>
                                            </div>
                                        </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <!-- Leads Table -->
                    <div class="card mt-4">
                        <div class="card-header">
                        <h3 class="card-title">Assigned Leads ({{ $tasks->count() }} Leads)</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="tasksTable">
                                    <thead>
                                        <tr>
                                        <th>#</th>
                                            <th>Title</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Source</th>
                                            <th>Created Date</th>
                                            <th>Follow-up Date</th>
                                            <th>Converted</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($tasks as $index => $task)
                                        <tr>
                                        <td>{{ $index + 1 }}</td>
                                            <td>{{ $task->title }}</td>
                                            <td>{{ $task->phone ?? 'N/A' }}</td>
                                            <td>{{ $task->email ?? 'N/A' }}</td>
                                            <td>
                                            <span class="badge bg-secondary">
                                                    {{ $task->leadStatus->title ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $task->leadSource->title ?? 'N/A' }}</td>
                                        <td>{{ $task->created_at->format('M d, Y') }}</td>
                                        <td>{{ $task->followup_date ? $task->followup_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                            <span class="badge {{ $task->is_converted ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $task->is_converted ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                        <td colspan="9" class="text-center">No leads assigned to this telecaller for the selected date range.</td>
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
<!-- <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script> -->

<script>
$(document).ready(function() {
    // Initialize DataTable for sessions only if there are actual sessions
    if ($('#telecallerSessionsTable').length) {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#telecallerSessionsTable')) {
            $('#telecallerSessionsTable').DataTable().destroy();
        }
        
        // Check if table has actual session data (not empty message)
        var table = $('#telecallerSessionsTable');
        var rows = table.find('tbody tr');
        var hasSessions = false;
        
        // Check each row - if any row has 9 individual cells (not colspan), we have sessions
        rows.each(function() {
            var cells = $(this).find('td');
            if (cells.length === 9 && !cells.filter('[colspan]').length) {
                hasSessions = true;
                return false; // break
            }
        });
        
        if (hasSessions) {
            $('#telecallerSessionsTable').DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "columnDefs": [
                    { "orderable": false, "targets": [0, 8] }, // Disable sorting on serial number and actions columns
                    { "searchable": false, "targets": [0, 8] } // Disable searching on serial number and actions columns
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
                }
        });
        } else {
            // Remove the table from DataTables initialization to prevent the error
            $('#telecallerSessionsTable').removeClass('datatable');
        }
    }
    

    // Initialize DataTable for tasks
    if ($('#tasksTable').length) {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#tasksTable')) {
            $('#tasksTable').DataTable().destroy();
        }
        
        try {
        $('#tasksTable').DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "columnDefs": [
                    { "orderable": false, "targets": [0] }, // Disable sorting on serial number column
                    { "searchable": false, "targets": [0] } // Disable searching on serial number column
            ],
            "language": {
                    "processing": "Loading leads...",
                    "emptyTable": "No leads found",
                    "zeroRecords": "No matching leads found",
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
        } catch (error) {
            console.error('Error initializing tasks DataTable:', error);
        }
    }
});

</script>
@endpush