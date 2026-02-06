@extends('layouts.mantis')

@section('title', 'Telecaller Behavior & Productivity Tracking')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/telecaller-tracking.css') }}">
<style>
    /* Simple Dashboard Cards */
    .dashboard-card {
        transition: all 0.2s ease;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .dashboard-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .dashboard-card .card-body {
        padding: 1.5rem;
    }
    
    .dashboard-card h3 {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .dashboard-card p {
        font-size: 1rem;
        font-weight: 500;
        margin-bottom: 0;
    }
    
    .dashboard-card .flex-shrink-0 i {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    
    /* Statistics Cards */
    .stats-card {
        transition: all 0.2s ease;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .stats-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .stats-card .card-body {
        padding: 1.25rem;
    }
    
    .stats-card h3 {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }
    
    .stats-card p {
        font-size: 0.9rem;
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .stats-card .flex-shrink-0 i {
        font-size: 2rem;
        opacity: 0.7;
    }
    
    /* Chart Cards */
    .chart-card {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }
    
    .chart-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .chart-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 8px 8px 0 0;
        padding: 1rem 1.5rem;
    }
    
    .chart-card .card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .chart-card .card-body {
        padding: 1.5rem;
    }
    
    /* Table Cards */
    .table-card {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }
    
    .table-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .table-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 8px 8px 0 0;
        padding: 1rem 1.5rem;
    }
    
    .table-card .card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-card h3 {
            font-size: 1.5rem;
        }
        
        .dashboard-card .flex-shrink-0 i {
            font-size: 2rem;
        }
        
        .stats-card h3 {
            font-size: 1.4rem;
        }
        
        .stats-card .flex-shrink-0 i {
            font-size: 1.5rem;
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
                    <h5 class="m-b-10">Telecaller Behavior & Productivity Tracking Dashboard</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Telecaller Tracking</li>
                    <li class="breadcrumb-item">Dashboard</li>
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
                    <div class="d-flex align-items-center">
                        <div class="avtar avtar-s rounded-circle bg-light-primary me-3 d-flex align-items-center justify-content-center">
                            <i class="ti ti-dashboard f-20 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Telecaller Performance Overview</h5>
                            <small class="text-muted">Real-time tracking and productivity analytics</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.telecaller-tracking.reports') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-chart-bar me-1"></i> Detailed Reports
                        </a>
                        <a href="{{ route('admin.telecaller-tasks.index') }}" class="btn btn-outline-success btn-sm">
                            <i class="ti ti-list me-1"></i> Task Management
                        </a>
                        <button class="btn btn-outline-info btn-sm" onclick="location.reload()">
                            <i class="ti ti-refresh me-1"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-users f-24 text-primary"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Today's Sessions</h6>
                                <h3 class="mb-0 text-primary fw-bold">{{ $todayStats['total_sessions'] }}</h3>
                                <small class="text-muted">Active sessions today</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-clock f-24 text-success"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Login Time (Today)</h6>
                                <h3 class="mb-0 text-success fw-bold">
                                    @php
                                        $totalMinutes = $todayStats['total_login_hours'] * 60;
                                        $hours = floor($totalMinutes / 60);
                                        $minutes = floor($totalMinutes % 60);
                                        $seconds = floor(($totalMinutes % 1) * 60);
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </h3>
                                <small class="text-muted">Total logged time today</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-clock f-24 text-warning"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Idle Time (Today)</h6>
                                <h3 class="mb-0 text-warning fw-bold">
                                    @php
                                        $totalSeconds = $todayStats['total_idle_seconds'];
                                        $hours = floor($totalSeconds / 3600);
                                        $minutes = floor(($totalSeconds % 3600) / 60);
                                        $seconds = $totalSeconds % 60;
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                </h3>
                                <small class="text-muted">Non-productive time today</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="ti ti-alert-triangle f-24 text-danger"></i>
                                </div>
                                <h6 class="mb-2 text-muted">Overdue Tasks</h6>
                                <h3 class="mb-0 text-danger fw-bold">{{ $overdueTasks->count() }}</h3>
                                <small class="text-muted">Tasks past due date</small>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card chart-card">
                                <div class="card-header">
                                    <h3 class="card-title">Weekly Statistics</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Sessions:</strong> {{ $weekStats['total_sessions'] }}</p>
                                            <p><strong>Login Time:</strong> 
                                                @php
                                                    $totalMinutes = $weekStats['total_login_hours'] * 60;
                                                    $hours = floor($totalMinutes / 60);
                                                    $minutes = floor($totalMinutes % 60);
                                                    $seconds = floor(($totalMinutes % 1) * 60);
                                                @endphp
                                                {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                            </p>
                                            <p><strong>Idle Time:</strong> 
                                                @php
                                                    $totalSeconds = $weekStats['total_idle_seconds'];
                                                    $hours = floor($totalSeconds / 3600);
                                                    $minutes = floor(($totalSeconds % 3600) / 60);
                                                    $seconds = $totalSeconds % 60;
                                                @endphp
                                                {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                            </p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Active Time:</strong> 
                                                @php
                                                    $totalMinutes = $weekStats['total_active_hours'] * 60;
                                                    $hours = floor($totalMinutes / 60);
                                                    $minutes = floor($totalMinutes % 60);
                                                    $seconds = floor(($totalMinutes % 1) * 60);
                                                @endphp
                                                {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                            </p>
                                            <p><strong>Tasks:</strong> {{ $weekStats['total_tasks'] }}</p>
                                            <p><strong>Completed:</strong> {{ $weekStats['completed_tasks'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card chart-card">
                                <div class="card-header">
                                    <h3 class="card-title">Monthly Statistics</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Sessions:</strong> {{ $monthStats['total_sessions'] }}</p>
                                            <p><strong>Login Time:</strong> 
                                                @php
                                                    $totalMinutes = $monthStats['total_login_hours'] * 60;
                                                    $hours = floor($totalMinutes / 60);
                                                    $minutes = floor($totalMinutes % 60);
                                                    $seconds = floor(($totalMinutes % 1) * 60);
                                                @endphp
                                                {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                            </p>
                                            <p><strong>Idle Time:</strong> 
                                                @php
                                                    $totalSeconds = $monthStats['total_idle_seconds'];
                                                    $hours = floor($totalSeconds / 3600);
                                                    $minutes = floor(($totalSeconds % 3600) / 60);
                                                    $seconds = $totalSeconds % 60;
                                                @endphp
                                                {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                            </p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Active Time:</strong> 
                                                @php
                                                    $totalMinutes = $monthStats['total_active_hours'] * 60;
                                                    $hours = floor($totalMinutes / 60);
                                                    $minutes = floor($totalMinutes % 60);
                                                    $seconds = floor(($totalMinutes % 1) * 60);
                                                @endphp
                                                {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                            </p>
                                            <p><strong>Tasks:</strong> {{ $monthStats['total_tasks'] }}</p>
                                            <p><strong>Completed:</strong> {{ $monthStats['completed_tasks'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities and Overdue Tasks -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Activities</h3>
                                </div>
                                <div class="card-body">
                                    @if($recentActivities->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Time</th>
                                                        <th>User</th>
                                                        <th>Activity</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentActivities as $activity)
                                                    <tr>
                                                        <td>{{ $activity->activity_time->format('g:i:s A') }}</td>
                                                        <td>{{ $activity->user->name ?? 'Unknown User' }}</td>
                                                        <td>
                                                            <span class="badge {{ $activity->activity_type == 'login' ? 'bg-success' : ($activity->activity_type == 'logout' ? 'bg-danger' : 'bg-info') }}">
                                                                {{ ucfirst($activity->activity_name) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $activity->description ?? 'No description' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                                                No recent activities found.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Overdue Tasks</h3>
                                </div>
                                <div class="card-body">
                                    @if($overdueTasks->count() > 0)
                                        @foreach($overdueTasks->take(5) as $lead)
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $lead->title }}</strong>
                                                <span class="badge bg-danger">Overdue</span>
                                            </div>
                                            <small class="text-muted">{{ $lead->telecaller->name ?? 'Unassigned' }} - {{ $lead->created_at->format('M d, Y') }}</small>
                                        </div>
                                        @endforeach
                                        @if($overdueTasks->count() > 5)
                                            <a href="{{ route('admin.telecaller-tasks.overdue') }}" class="btn btn-sm btn-outline-danger">
                                                View All ({{ $overdueTasks->count() }})
                                            </a>
                                        @endif
                                    @else
                                        <p class="text-success">No overdue tasks!</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Telecaller List -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card table-card">
                                <div class="card-header">
                                    <h3 class="card-title">Telecallers Overview</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="telecallersTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Actions</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Team</th>
                                                    <th>Today's Sessions</th>
                                                    <th>Today's Hours</th>
                                                    <th>Idle Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($telecallers as $index => $telecaller)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('admin.telecaller-tracking.telecaller-report', $telecaller->id) }}" 
                                                               class="btn btn-sm btn-outline-primary" title="View Report">
                                                                <i class="ti ti-chart-line"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                                                <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($telecaller->name, 0, 1)) }}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $telecaller->name }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $telecaller->email }}</td>
                                                    <td>{{ $telecaller->team->name ?? 'No Team' }}</td>
                                                    <td>
                                                        @php
                                                            $todaySessions = $telecaller->telecallerSessions()
                                                                ->whereDate('login_time', today())
                                                                ->count();
                                                        @endphp
                                                        {{ $todaySessions }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            // Get today's sessions for this telecaller
                                                            $todaySessions = $telecaller->telecallerSessions()
                                                                ->whereDate('login_time', today())
                                                                ->get();
                                                            
                                                            // Calculate total duration in seconds
                                                            $totalDurationSeconds = 0;
                                                            foreach ($todaySessions as $session) {
                                                                if ($session->total_duration_minutes && $session->total_duration_minutes > 0) {
                                                                    $totalDurationSeconds += $session->total_duration_minutes * 60;
                                                                } else {
                                                                    $calculatedDuration = $session->calculateTotalDuration();
                                                                    if ($calculatedDuration > 0) {
                                                                        $totalDurationSeconds += $calculatedDuration;
                                                                    }
                                                                }
                                                            }
                                                            
                                                            // Convert to hours, minutes, seconds
                                                            $hours = floor($totalDurationSeconds / 3600);
                                                            $minutes = floor(($totalDurationSeconds % 3600) / 60);
                                                            $seconds = $totalDurationSeconds % 60;
                                                        @endphp
                                                        {{ $hours }}h {{ $minutes }}m {{ $seconds }}s
                                                    </td>
                                                    <td>
                                                        @php
                                                            // Calculate idle time for today
                                                            $todayIdleSeconds = \App\Models\TelecallerIdleTime::where('user_id', $telecaller->id)
                                                                ->whereDate('idle_start_time', today())
                                                                ->sum('idle_duration_seconds');
                                                            
                                                            // Convert to hours, minutes, seconds
                                                            $idleHours = floor($todayIdleSeconds / 3600);
                                                            $idleMinutes = floor(($todayIdleSeconds % 3600) / 60);
                                                            $idleSeconds = $todayIdleSeconds % 60;
                                                        @endphp
                                                        {{ $idleHours }}h {{ $idleMinutes }}m {{ $idleSeconds }}s
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable for telecallers overview
    if ($('#telecallersTable').length) {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#telecallersTable')) {
            $('#telecallersTable').DataTable().destroy();
        }
        
        $('#telecallersTable').DataTable({
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
                "processing": "Loading telecallers...",
                "emptyTable": "No telecallers found",
                "zeroRecords": "No matching telecallers found",
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
    }
});

// Auto-refresh dashboard every 30 seconds
// setInterval(function() {
//     location.reload();
// }, 30000);
</script>
@endpush
