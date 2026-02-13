@extends('layouts.mantis')

@section('title', 'B2B Service Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">B2B Service Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Master Data</li>
                    <li class="breadcrumb-item">B2B Services</li>
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
                    <h5 class="mb-0">B2B Service List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.b2b-services.add') }}', 'Add B2B Service')">
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
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($b2bServices as $index => $service)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                            <i class="ti ti-briefcase"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $service->title }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($service->status === 'active')
                                        <span class="badge bg-success text-white">
                                            <i class="ti ti-check me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger text-white">
                                            <i class="ti ti-x me-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($service->createdBy)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                <i class="ti ti-user"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $service->createdBy->name }}</h6>
                                                <small class="text-muted">{{ $service->createdBy->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted">
                                            <i class="ti ti-user-x me-1"></i>Unknown
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">{{ $service->created_at->format('d M Y, h:i A') }}</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3 me-1"
                                        onclick="show_small_modal('{{ route('admin.b2b-services.edit', $service->id) }}', 'Edit B2B Service')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.b2b-services.delete', $service->id) }}')" title="Delete">
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
