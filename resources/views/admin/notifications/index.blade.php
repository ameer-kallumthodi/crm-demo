@extends('layouts.mantis')

@section('title', 'Manage Notifications')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Manage Notifications</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Notifications</li>
                    <li class="breadcrumb-item">Manage</li>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-bell me-2"></i>Notifications Management
                </h5>
                <button type="button" class="btn btn-primary" onclick="show_ajax_modal('{{ route('admin.notifications.create') }}', 'Create Notification')">
                    <i class="ti ti-plus"></i> Create Notification
                </button>
            </div>
            <div class="card-body">
                @if($notifications->count() > 0)
                    <div class="table-responsive">
                        <table id="notificationsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Target</th>
                                    <th>Role</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $index => $notification)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $notifications->firstItem() + $index }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if($notification->type === 'success')
                                                        <i class="ti ti-check-circle text-success"></i>
                                                    @elseif($notification->type === 'error')
                                                        <i class="ti ti-x-circle text-danger"></i>
                                                    @elseif($notification->type === 'warning')
                                                        <i class="ti ti-alert-triangle text-warning"></i>
                                                    @else
                                                        <i class="ti ti-info-circle text-info"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $notification->title }}</h6>
                                                    <small class="text-muted">{{ Str::limit($notification->message, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $notification->type === 'success' ? 'success' : ($notification->type === 'error' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info')) }}">
                                                {{ ucfirst($notification->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($notification->target_type) }}
                                            </span>
                                        </td>
                                        <td>{{ $notification->role->title ?? 'N/A' }}</td>
                                        <td>{{ $notification->user->name ?? 'All Users' }}</td>
                                        <td>
                                            @if($notification->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->creator->name ?? 'System' }}</td>
                                        <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="show_large_modal('{{ route('admin.notifications.show', $notification->id) }}', 'View Notification')"
                                                        title="View Details">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="show_ajax_modal('{{ route('admin.notifications.edit', $notification->id) }}', 'Edit Notification')"
                                                        title="Edit Notification">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="delete_modal('{{ route('admin.notifications.destroy', $notification->id) }}', 'Delete Notification', 'Are you sure you want to delete this notification?')"
                                                        title="Delete Notification">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-bell f-48 mb-3"></i>
                        <h5>No Notifications Found</h5>
                        <p>Create your first notification to get started.</p>
                        <button type="button" class="btn btn-primary" onclick="show_ajax_modal('{{ route('admin.notifications.create') }}', 'Create Notification')">
                            <i class="ti ti-plus"></i> Create Notification
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection

@section('page-scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    if ($.fn.DataTable.isDataTable('#notificationsTable')) {
        $('#notificationsTable').DataTable().destroy();
    }
    
    $('#notificationsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[8, 'desc']], // Sort by created_at desc (updated index)
        columnDefs: [
            { orderable: false, targets: [0, 9] } // Disable sorting on Serial # and Actions columns
        ],
        language: {
            search: "Search notifications:",
            lengthMenu: "Show _MENU_ notifications per page",
            info: "Showing _START_ to _END_ of _TOTAL_ notifications",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});
</script>
@endsection
