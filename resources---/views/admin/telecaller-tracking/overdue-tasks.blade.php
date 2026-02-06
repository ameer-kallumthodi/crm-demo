@extends('layouts.mantis')

@section('title', 'Overdue Tasks')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/telecaller-tracking.css') }}">
@endpush

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Overdue Tasks</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.telecaller-tasks.index') }}">Telecaller Tasks</a></li>
                    <li class="breadcrumb-item">Overdue Tasks</li>
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
                        <div class="avtar avtar-s rounded-circle bg-light-danger me-3 d-flex align-items-center justify-content-center">
                            <i class="ti ti-alert-triangle f-20 text-danger"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Overdue Tasks</h5>
                            <small class="text-muted">Leads that are overdue (older than 7 days and not touched)</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.telecaller-tasks.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-arrow-left"></i> Back to Tasks
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0">{{ $overdueTasks->count() }}</h4>
                                        <p class="mb-0">Total Overdue</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-alert-triangle f-24"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0">{{ $overdueTasks->filter(function($task) {
                                            $lastActivity = $task->leadActivities->sortByDesc('created_at')->first();
                                            if ($lastActivity) {
                                                $daysOverdue = $lastActivity->created_at->diffInDays(now());
                                            } else {
                                                $daysOverdue = $task->created_at->diffInDays(now());
                                            }
                                            return floor($daysOverdue) >= 14;
                                        })->count() }}</h4>
                                        <p class="mb-0">Very Old (14+ days)</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-clock f-24"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0">{{ $overdueTasks->groupBy('telecaller_id')->count() }}</h4>
                                        <p class="mb-0">Affected Telecallers</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-users f-24"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0">{{ $overdueTasks->where('followup_date', '!=', null)->count() }}</h4>
                                        <p class="mb-0">With Follow-up Date</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-calendar f-24"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover data_table_basic" id="overdueTasksTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Source</th>
                                <th>Telecaller</th>
                                <th>Days Overdue</th>
                                <th>Last Activity</th>
                                <th>Last Updated</th>
                                <th>Follow-up Date</th>
                                <th>Remarks</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overdueTasks as $index => $task)
                            <tr class="table-danger">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                            <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($task->title, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $task->title }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \App\Helpers\PhoneNumberHelper::display($task->code, $task->phone) }}</td>
                                <td>{{ $task->email ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($task->leadStatus->id ?? 1) }}">
                                        {{ $task->leadStatus->title ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td>{{ $task->leadSource->title ?? '-' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-xs rounded-circle bg-light-info me-2 d-flex align-items-center justify-content-center">
                                            <span class="f-12 fw-bold text-info">{{ strtoupper(substr($task->telecaller->name ?? 'U', 0, 1)) }}</span>
                                        </div>
                                        <span>{{ $task->telecaller->name ?? 'Unassigned' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $lastActivity = $task->leadActivities->sortByDesc('created_at')->first();
                                        if ($lastActivity) {
                                            // If there are activities, calculate days since last activity
                                            $daysOverdue = $lastActivity->created_at->diffInDays(now());
                                        } else {
                                            // If no activities, calculate days since lead creation
                                            $daysOverdue = $task->created_at->diffInDays(now());
                                        }
                                        $daysOverdue = floor($daysOverdue); // Convert to whole number
                                    @endphp
                                    <span class="badge bg-danger">
                                        {{ $daysOverdue }} days
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $lastActivity = $task->leadActivities->sortByDesc('created_at')->first();
                                    @endphp
                                    @if($lastActivity)
                                        <span class="text-info">{{ $lastActivity->created_at->format('M d, Y h:i A') }}</span>
                                    @else
                                        <span class="text-muted">No activity</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $task->updated_at->format('M d, Y h:i A') }}</span>
                                </td>
                                <td>
                                    @if($task->followup_date)
                                        <span class="badge bg-warning">{{ $task->followup_date->format('M d, Y h:i A') }}</span>
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>{{ $task->remarks ? Str::limit($task->remarks, 30) : '-' }}</td>
                                <td>
                                    <span class="text-primary">{{ $task->created_at->format('M d, Y h:i A') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-check-circle f-48 mb-3 d-block text-success"></i>
                                        <h5>No Overdue Tasks</h5>
                                        <p>Great! All tasks are up to date.</p>
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
<!-- [ Main Content ] end -->

@endsection

@push('scripts')
<script>
        $(document).ready(function() {
            // DataTable is now initialized globally via initializeTables() function
        });
</script>
@endpush
