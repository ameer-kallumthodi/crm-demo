@extends('layouts.mantis')

@section('title', 'Marketing Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Marketing Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">User Management</li>
                    <li class="breadcrumb-item">Marketing</li>
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
                    <h5 class="mb-0">Marketing List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.marketing.add') }}', 'Add Marketing User')">
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
                                <th>Team</th>
                                <th>Joining Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($marketings as $index => $marketing)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span>{{ $marketing->name }}</span>
                                        @if($marketing->is_team_lead)
                                            <span class="badge bg-warning ms-2">
                                                <i class="ti ti-crown me-1"></i>Team Lead
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $marketing->email }}</td>
                                <td>{{ $marketing->phone ?? '-' }}</td>
                                <td>{{ $marketing->team ? $marketing->team->name : '-' }}</td>
                                <td>
                                    @if($marketing->joining_date)
                                        {{ $marketing->joining_date->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.marketing.edit', $marketing->id) }}', 'Edit Marketing User')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-info btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.marketing.change-password', $marketing->id) }}', 'Change Password')" title="Change Password">
                                        <i class="ti ti-key"></i> Password
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.marketing.delete', $marketing->id) }}')" title="Delete">
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

