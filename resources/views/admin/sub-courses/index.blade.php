@extends('layouts.mantis')

@section('title', 'Sub Courses Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Sub Courses Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Sub Courses</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sub Courses</h5>
                <div>
                    <button type="button" onclick="show_small_modal('{{ route('admin.sub-courses.add') }}', 'Add Sub Course')" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Add New Sub Course
                    </button>
                </div>
            </div>
            <div class="card-body">
                

                @if(session('message_danger'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('message_danger') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover data_table_basic" id="subCoursesTable">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subCourses as $index => $subCourse)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $subCourse->title }}</td>
                                <td>{{ $subCourse->course->title }}</td>
                                <td>
                                    <span class="badge {{ $subCourse->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $subCourse->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $subCourse->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-warning" title="Edit"
                                                onclick="show_small_modal('{{ route('admin.sub-courses.edit', $subCourse->id) }}', 'Edit Sub Course')">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteSubCourse({{ $subCourse->id }})" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No sub courses found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function deleteSubCourse(id) {
        if (confirm('Are you sure you want to delete this sub course?')) {
            $.ajax({
                url: `/admin/sub-courses/${id}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toast_success(response.message);
                        setTimeout(() => { location.reload(); }, 800);
                    } else {
                        toast_error(response.error || 'Delete failed');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Delete failed';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    toast_error(errorMessage);
                }
            });
        }
    }
</script>
@endpush
