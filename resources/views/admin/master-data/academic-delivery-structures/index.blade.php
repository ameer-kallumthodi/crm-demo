@extends('layouts.mantis')

@section('title', 'Academic Delivery Structure Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Academic Delivery Structure</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Master Data</li>
                    <li class="breadcrumb-item">Academic Delivery Structure</li>
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
                    <h5 class="mb-0">Academic Delivery Structure List</h5>
                    @if(has_permission('admin/academic-delivery-structures/index') || \App\Helpers\RoleHelper::is_academic_counselor())
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.academic-delivery-structures.add') }}', 'Add Academic Delivery Structure')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($academicDeliveryStructures as $structure)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $structure->title }}</td>
                                <td>{{ $structure->course ? $structure->course->title : '-' }}</td>
                                <td>
                                    @if(has_permission('admin/academic-delivery-structures/index') || \App\Helpers\RoleHelper::is_academic_counselor())
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3 me-1"
                                        onclick="show_small_modal('{{ route('admin.academic-delivery-structures.edit', $structure->id) }}', 'Edit Academic Delivery Structure')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.academic-delivery-structures.delete', $structure->id) }}')" title="Delete">
                                        <i class="ti ti-trash"></i> Delete
                                    </a>
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
