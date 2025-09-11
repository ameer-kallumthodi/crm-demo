@extends('layouts.mantis')

@section('title', 'Course Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Course Management</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">Courses</li>
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
                <h5 class="mb-0">Course List</h5>
                <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                    onclick="show_small_modal('{{ route('admin.courses.add') }}', 'Add Course')">
                    <i class="ti ti-plus"></i> Add New
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Duration</th>
                                <th>Fees</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr>
                                <td>{{ $course->id }}</td>
                                <td>{{ $course->title }}</td>
                                <td>{{ $course->description ?? '-' }}</td>
                                <td>{{ $course->duration ?? '-' }}</td>
                                <td>{{ $course->fees ? '$' . number_format($course->fees, 2) : '-' }}</td>
                                <td>
                                    <span class="badge {{ $course->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $course->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.courses.edit', $course->id) }}', 'Edit Course')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.courses.delete', $course->id) }}')" title="Delete">
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
