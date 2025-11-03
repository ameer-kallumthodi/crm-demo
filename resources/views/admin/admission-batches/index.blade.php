@extends('layouts.mantis')

@section('title', 'Admission Batch Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Admission Batch Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">Admission Batches</li>
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
                    <h5 class="mb-0">Admission Batch List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.admission-batches.add') }}', 'Add Admission Batch')">
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
                                <th>Course - Batch</th>
                                <th>Mentor</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admissionBatches as $index => $admissionBatch)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $admissionBatch->title }}</td>
                                <td>
                                    @php($batch = $admissionBatch->batch)
                                    {{ $batch && $batch->course ? ($batch->course->title . ' - ') : '' }}{{ $batch->title ?? 'N/A' }}
                                </td>
                                <td>{{ $admissionBatch->mentor->name ?? 'No Mentor Assigned' }}</td>
                                <td>{{ $admissionBatch->description ?? 'N/A' }}</td>
                                <td>
                                    @if($admissionBatch->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $admissionBatch->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info"
                                            onclick="show_small_modal('{{ route('admin.admission-batches.edit', $admissionBatch->id) }}', 'Edit Admission Batch')">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-danger"
                                            onclick="delete_modal('{{ route('admin.admission-batches.destroy', $admissionBatch->id) }}')" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
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
