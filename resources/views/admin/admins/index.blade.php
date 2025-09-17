@extends('layouts.mantis')

@section('title', 'Admins')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Admin Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Admins</li>
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
                    <h5 class="mb-0">Admin Users</h5>
                    <button type="button" class="btn btn-primary" onclick="show_ajax_modal('{{ route('admin.admins.add') }}', 'Add Admin')">
                        <i class="ti ti-plus"></i> Add Admin
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="adminsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $index => $admin)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                            <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $admin->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $admin->phone }}</td>
                                <td>
                                    <span class="badge bg-light-info text-info">
                                        {{ $admin->role->title ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($admin->is_active)
                                        <span class="badge bg-light-success text-success">Active</span>
                                    @else
                                        <span class="badge bg-light-danger text-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary"
                                            onclick="show_ajax_modal('{{ route('admin.admins.edit', $admin->id) }}', 'Edit Admin')">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-warning"
                                            onclick="show_ajax_modal('{{ route('admin.admins.change-password', $admin->id) }}', 'Change Password')">
                                            <i class="ti ti-key"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger"
                                            onclick="delete_modal('{{ route('admin.admins.destroy', $admin->id) }}', 'Delete Admin', 'Are you sure you want to delete this admin? This action cannot be undone.')">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-users f-48 mb-3 d-block"></i>
                                        <h5>No Admins Found</h5>
                                        <p>Start by adding your first admin user.</p>
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
    if (!$.fn.DataTable.isDataTable('#adminsTable')) {
        $('#adminsTable').DataTable({
            "processing": true,
            "serverSide": false,
            "responsive": true,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[6, "desc"]], // Sort by created date descending
            "columnDefs": [
                { "orderable": false, "targets": [0, 7] }, // Disable sorting on serial number and actions columns
                { "searchable": false, "targets": [0, 7] } // Disable searching on serial number and actions columns
            ],
            "language": {
                "processing": "Loading admins...",
                "emptyTable": "No admins found",
                "zeroRecords": "No matching admins found"
            }
        });
    }
});
</script>
@endpush
