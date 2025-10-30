@extends('layouts.mantis')

@section('title', 'General Manager Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">General Manager Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">User Management</li>
                    <li class="breadcrumb-item">General Managers</li>
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
                    <h5 class="mb-0">General Manager List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3 js-open-modal"
                        data-url="{{ route('admin.general-managers.add') }}" data-title="Add General Manager">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($generalManagers as $index => $generalManager)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $generalManager->name }}</td>
                                <td>{{ $generalManager->email }}</td>
                                <td>{{ $generalManager->phone ?? '-' }}</td>
                                <td>
                                    @if($generalManager->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3 js-open-modal"
                                        data-url="{{ route('admin.general-managers.edit', $generalManager->id) }}" data-title="Edit General Manager" title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-info btn-sm shadow-sm px-3 js-open-modal"
                                        data-url="{{ route('admin.general-managers.change-password', $generalManager->id) }}" data-title="Change Password" title="Change Password">
                                        <i class="ti ti-key"></i> Password
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3 js-delete-modal"
                                        data-url="{{ route('admin.general-managers.delete', $generalManager->id) }}" title="Delete">
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
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-open-modal').forEach(function (el) {
        el.addEventListener('click', function () {
            var url = this.getAttribute('data-url');
            var title = this.getAttribute('data-title');
            if (typeof show_small_modal === 'function') {
                show_small_modal(url, title);
            }
        });
    });
    document.querySelectorAll('.js-delete-modal').forEach(function (el) {
        el.addEventListener('click', function () {
            var url = this.getAttribute('data-url');
            if (typeof delete_modal === 'function') {
                delete_modal(url);
            }
        });
    });
});
</script>
@endpush


