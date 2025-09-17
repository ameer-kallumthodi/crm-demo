@extends('layouts.mantis')

@section('title', 'Telecaller Behavior & Productivity Tracking')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Telecaller Behavior & Productivity Tracking Dashboard</h5>
                </div>
                <ul class="breadcrumb">
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
                    <h5 class="mb-0">Telecaller Performance Overview</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.telecaller-tracking.reports') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-chart-bar"></i> Detailed Reports
                        </a>
                        <a href="{{ route('admin.telecaller-tasks.index') }}" class="btn btn-outline-success btn-sm">
                            <i class="ti ti-list"></i> Task Management
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ $todayStats['total_sessions'] }}</h3>
                                        <p class="mb-0">Today's Sessions</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-users f-48"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ number_format($todayStats['total_login_hours'], 1) }}</h3>
                                        <p class="mb-0">Total Login Hours (Today)</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-clock f-48"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ number_format($todayStats['total_idle_hours'], 1) }}</h3>
                                        <p class="mb-0">Total Idle Hours (Today)</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-pause f-48"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h3 class="mb-0">{{ $overdueTasks->count() }}</h3>
                                        <p class="mb-0">Overdue Tasks</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-alert-triangle f-48"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Weekly Statistics</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Sessions:</strong> {{ $weekStats['total_sessions'] }}</p>
                                            <p><strong>Login Hours:</strong> {{ number_format($weekStats['total_login_hours'], 1) }}</p>
                                            <p><strong>Idle Hours:</strong> {{ number_format($weekStats['total_idle_hours'], 1) }}</p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Active Hours:</strong> {{ number_format($weekStats['total_active_hours'], 1) }}</p>
                                            <p><strong>Tasks:</strong> {{ $weekStats['total_tasks'] }}</p>
                                            <p><strong>Completed:</strong> {{ $weekStats['completed_tasks'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Monthly Statistics</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Sessions:</strong> {{ $monthStats['total_sessions'] }}</p>
                                            <p><strong>Login Hours:</strong> {{ number_format($monthStats['total_login_hours'], 1) }}</p>
                                            <p><strong>Idle Hours:</strong> {{ number_format($monthStats['total_idle_hours'], 1) }}</p>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Active Hours:</strong> {{ number_format($monthStats['total_active_hours'], 1) }}</p>
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
                                                        <td>{{ $activity->activity_time->format('H:i:s') }}</td>
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
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Telecallers Overview</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover datatable" id="telecallersTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Actions</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Team</th>
                                                    <th>Today's Sessions</th>
                                                    <th>Today's Hours</th>
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
                                                            $todayHours = $telecaller->telecallerSessions()
                                                                ->whereDate('login_time', today())
                                                                ->sum('total_duration_minutes') / 60;
                                                        @endphp
                                                        {{ number_format($todayHours, 1) }}h
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
    if ($('#telecallersTable').length && !$.fn.DataTable.isDataTable('#telecallersTable')) {
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
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endpush
