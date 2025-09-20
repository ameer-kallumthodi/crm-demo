@extends('layouts.mantis')

@section('title', 'Telecaller Task Management')

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
                    <h5 class="m-b-10">Telecaller Task Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Telecaller Tracking</li>
                    <li class="breadcrumb-item">Task Management</li>
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
                        <div class="avtar avtar-s rounded-circle bg-light-success me-3 d-flex align-items-center justify-content-center">
                            <i class="ti ti-list-check f-20 text-success"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Lead Assignments (Tasks)</h5>
                            <small class="text-muted">Manage and track telecaller lead assignments</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- [ Date Filter ] start -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.telecaller-tasks.index') }}" id="dateFilterForm">
                                    <div class="row align-items-end">
                                        <div class="col-md-2">
                                            <label for="telecaller_id" class="form-label">Telecaller</label>
                                            <select class="form-select" name="telecaller_id" id="telecaller_id">
                                                <option value="">All Telecallers</option>
                                                @foreach($telecallers as $telecaller)
                                                    <option value="{{ $telecaller->id }}" {{ request('telecaller_id') == $telecaller->id ? 'selected' : '' }}>
                                                        {{ $telecaller->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="is_converted" class="form-label">Conversion Status</label>
                                            <select class="form-select" name="is_converted" id="is_converted">
                                                <option value="">All Status</option>
                                                <option value="0" {{ request('is_converted') == '0' ? 'selected' : '' }}>Not Converted</option>
                                                <option value="1" {{ request('is_converted') == '1' ? 'selected' : '' }}>Converted</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="start_date" class="form-label">From Date</label>
                                            <input type="date" class="form-control" name="start_date" 
                                                   value="{{ request('start_date', \Carbon\Carbon::now()->subDays(7)->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="end_date" class="form-label">To Date</label>
                                            <input type="date" class="form-control" name="end_date" 
                                                   value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="ti ti-filter"></i> Filter
                                                </button>
                                                <a href="{{ route('admin.telecaller-tasks.index') }}" class="btn btn-outline-secondary">
                                                    <i class="ti ti-x"></i> Clear
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
                    <table class="table table-hover data_table_basic" id="leadsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Actions</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Source</th>
                                <th>Telecaller</th>
                                <th>Place</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $index => $task)
                            <tr class="{{ $task->isOverdue() ? 'table-danger' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary"
                                            onclick="show_large_modal('{{ route('admin.telecaller-tasks.show', $task) }}', 'View Lead')"
                                            title="View Lead">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary"
                                            onclick="show_ajax_modal('{{ route('admin.telecaller-tasks.edit', $task) }}', 'Edit Lead')"
                                            title="Edit Lead">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        @if(!$task->is_converted)
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning"
                                            onclick="completeTask({{ $task->id }})"
                                            title="Convert Lead">
                                            <i class="ti ti-refresh"></i>
                                        </a>
                                        @endif
                                        @if($task->phone && is_telecaller())
                                        @php
                                            $currentUserId = session('user_id') ?? (\App\Helpers\AuthHelper::getCurrentUserId() ?? 0);
                                        @endphp
                                        @if($currentUserId > 0)
                                        <button class="btn btn-sm btn-outline-success voxbay-call-btn" 
                                                data-lead-id="{{ $task->id }}" 
                                                data-telecaller-id="{{ $currentUserId }}"
                                                title="Call Lead">
                                            <i class="ti ti-phone"></i>
                                        </button>
                                        @endif
                                        @endif
                                        <a href="{{ route('leads.call-logs', $task) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="View Call Logs">
                                            <i class="ti ti-phone-call"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteTask({{ $task->id }})"
                                            title="Delete Lead">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
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
                                <td>{{ $task->telecaller->name ?? 'Unassigned' }}</td>
                                <td>{{ $task->place ?? '-' }}</td>
                                <td>{{ $task->remarks ? Str::limit($task->remarks, 30) : '-' }}</td>
                                <td>{{ $task->created_at->format('M d, Y') }}</td>
                                <td>{{ $task->created_at->format('g:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-inbox f-48 mb-3 d-block"></i>
                                        No leads found
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

<!-- Complete Task Modal -->
<div class="modal fade" id="completeTaskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Task</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="completeTaskForm" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="remarks">Conversion Notes:</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="3" 
                                  placeholder="Add any notes about lead conversion..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // DataTable is now initialized globally via initializeTables() function
});

function completeTask(taskId) {
    $('#completeTaskForm').attr('action', '{{ route("admin.telecaller-tasks.complete", ":id") }}'.replace(':id', taskId));
    $('#completeTaskModal').modal('show');
}

function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this lead?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.telecaller-tasks.destroy", ":id") }}'.replace(':id', taskId);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
