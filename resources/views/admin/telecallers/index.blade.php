@extends('layouts.mantis')

@section('title', 'Telecaller Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Telecaller Management</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">User Management</li>
                    <li class="breadcrumb-item">Telecallers</li>
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
                <h5 class="mb-0">Telecaller List</h5>
                <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                    onclick="show_small_modal('{{ route('admin.telecallers.add') }}', 'Add Telecaller')">
                    <i class="ti ti-plus"></i> Add New
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Team</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($telecallers as $telecaller)
                            <tr>
                                <td>{{ $telecaller->id }}</td>
                                <td>{{ $telecaller->name }}</td>
                                <td>{{ $telecaller->email }}</td>
                                <td>{{ $telecaller->phone ?? '-' }}</td>
                                <td>{{ $telecaller->team ? $telecaller->team->name : '-' }}</td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.telecallers.edit', $telecaller->id) }}', 'Edit Telecaller')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-info btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.telecallers.change-password', $telecaller->id) }}', 'Change Password')
                                        title="Change Password">
                                        <i class="ti ti-key"></i> Password
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.telecallers.delete', $telecaller->id) }}')" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
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
<!-- [ Main Content ] end -->

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('.datatable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });
});
</script>
@endpush
