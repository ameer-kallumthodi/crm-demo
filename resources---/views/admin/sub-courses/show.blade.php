@extends('layouts.mantis')

@section('title', 'View Sub Course')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">View Sub Course</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sub-courses.index') }}">Sub Courses</a></li>
                    <li class="breadcrumb-item">View</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Sub Course Details</h5>
                <div class="card-header-right">
                    <a href="{{ route('admin.sub-courses.edit', $subCourse->id) }}" class="btn btn-warning">
                        <i class="ti ti-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('admin.sub-courses.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">ID:</th>
                                <td>{{ $subCourse->id }}</td>
                            </tr>
                            <tr>
                                <th>Title:</th>
                                <td>{{ $subCourse->title }}</td>
                            </tr>
                            <tr>
                                <th>Course:</th>
                                <td>{{ $subCourse->course->title }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge {{ $subCourse->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $subCourse->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Created At:</th>
                                <td>{{ $subCourse->created_at->format('d-m-Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At:</th>
                                <td>{{ $subCourse->updated_at->format('d-m-Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
