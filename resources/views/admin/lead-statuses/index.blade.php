@extends('layouts.mantis')

@section('title', 'Lead Statuses')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Lead Status Management</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Lead Statuses</li>
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
                    <h5 class="mb-0">Lead Status List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.lead-statuses.add') }}', 'Add Lead Status')">
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
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leadStatuses as $index => $status)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $status->title }}</td>
                                <td>{{ $status->description ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $status->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $status->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @if(is_super_admin())
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.lead-statuses.edit', $status->id) }}', 'Edit Lead Status')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.lead-statuses.delete', $status->id) }}')" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
                                    @else
                                    <span class="badge bg-light-secondary text-secondary">Not Allowed</span>
                                    @endif
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
// DataTable is now initialized globally in footer-scripts.blade.php
// No need for duplicate initialization here
</script>
@endpush