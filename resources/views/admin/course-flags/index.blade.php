@extends('layouts.mantis')

@section('title', 'Course Flag')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Course Flag Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Course Flag</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course Flag List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.course-flags.add') }}', 'Add Course Flag')">
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
                                <th>Color</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courseFlags as $courseFlag)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="d-inline-block rounded border" style="width: 28px; height: 28px; background-color: {{ $courseFlag->color }};" title="{{ $courseFlag->color }}"></span>
                                    <span class="ms-2 text-muted small">{{ $courseFlag->color }}</span>
                                </td>
                                <td>
                                    <i class="ti ti-flag me-2 text-primary"></i>
                                    {{ $courseFlag->title }}
                                </td>
                                <td>{{ Str::limit($courseFlag->description, 80) }}</td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm shadow-sm px-3"
                                        onclick="show_small_modal('{{ route('admin.course-flags.edit', $courseFlag->id) }}', 'Edit Course Flag')"
                                        title="Edit">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    @if(can_delete_subject_areas_mails_flags())
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm shadow-sm px-3"
                                        onclick="delete_modal('{{ route('admin.course-flags.delete', $courseFlag->id) }}')" title="Delete">
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
@endsection
