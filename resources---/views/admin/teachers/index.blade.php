@extends('layouts.mantis')

@section('title', 'Teacher Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Teacher Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">User Management</li>
                    <li class="breadcrumb-item">Teachers</li>
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
                    <h5 class="mb-0">Teacher List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.teachers.add') }}', 'Add Teacher')">
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
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $index => $teacher)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $teacher->name }}</td>
                                <td>{{ $teacher->phone ?? 'N/A' }}</td>
                                <td>{{ $teacher->role->title ?? 'N/A' }}</td>
                                <td>{{ $teacher->created_at->format('d-m-Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary"
                                            onclick="show_small_modal('{{ route('admin.teachers.edit', $teacher->id) }}', 'Edit Teacher')" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTeacher({{ $teacher->id }})" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No teachers found</td>
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
function deleteTeacher(id) {
    if (confirm('Are you sure you want to delete this teacher?')) {
        $.ajax({
            url: `/admin/teachers/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toast_success(response.message);
                    location.reload();
                }
            },
            error: function() {
                toast_error('An error occurred while deleting the teacher.');
            }
        });
    }
}
</script>
@endpush
