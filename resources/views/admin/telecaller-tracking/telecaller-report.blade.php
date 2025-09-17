@extends('layouts.app')

@section('title', 'Telecaller Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Telecaller Report - {{ $telecaller->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.telecaller-tracking.reports') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Reports
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
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Sessions</span>
                                    <span class="info-box-number">{{ $stats['total_sessions'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-sign-in-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Login Hours</span>
                                    <span class="info-box-number">{{ $stats['total_login_hours'] }}h</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-pause"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Idle Hours</span>
                                    <span class="info-box-number">{{ $stats['total_idle_hours'] }}h</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-tasks"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Hours</span>
                                    <span class="info-box-number">{{ $stats['total_active_hours'] }}h</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Task Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary"><i class="fas fa-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Leads</span>
                                    <span class="info-box-number">{{ $stats['total_tasks'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Converted</span>
                                    <span class="info-box-number">{{ $stats['completed_tasks'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending</span>
                                    <span class="info-box-number">{{ $stats['pending_tasks'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Overdue</span>
                                    <span class="info-box-number">{{ $stats['overdue_tasks'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Performance Metrics</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Productivity Score:</strong></p>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" style="width: {{ $stats['productivity_score'] }}%">
                                                    {{ $stats['productivity_score'] }}%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <p><strong>Efficiency Score:</strong></p>
                                            <div class="progress">
                                                <div class="progress-bar bg-info" style="width: {{ $stats['efficiency_score'] }}%">
                                                    {{ $stats['efficiency_score'] }}%
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
                            <h3 class="card-title">Session History</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
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
                                        @forelse($sessions as $session)
                                        <tr>
                                            <td>{{ $session->login_time->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $session->logout_time ? $session->logout_time->format('Y-m-d H:i:s') : 'Active' }}</td>
                                            <td>{{ $session->total_duration_minutes ? round($session->total_duration_minutes / 60, 2) . 'h' : 'N/A' }}</td>
                                            <td>{{ $session->active_duration_minutes ? round($session->active_duration_minutes / 60, 2) . 'h' : 'N/A' }}</td>
                                            <td>{{ $session->idle_duration_minutes ? round($session->idle_duration_minutes / 60, 2) . 'h' : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $session->logout_type == 'manual' ? 'success' : ($session->logout_type == 'auto' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($session->logout_type) }}
                                                </span>
                                            </td>
                                            <td>{{ $session->ip_address ?? 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No sessions found for the selected date range.</td>
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
                            <h3 class="card-title">Assigned Leads</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
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
                                        @forelse($tasks as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>{{ $task->phone ?? 'N/A' }}</td>
                                            <td>{{ $task->email ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    {{ $task->leadStatus->title ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $task->leadSource->title ?? 'N/A' }}</td>
                                            <td>{{ $task->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $task->followup_date ? $task->followup_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $task->is_converted ? 'success' : 'warning' }}">
                                                    {{ $task->is_converted ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No leads found for the selected date range.</td>
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
</div>
@endsection
