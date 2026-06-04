@extends('layouts.mantis')

@section('title', 'Batch Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Batch Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">Batches</li>
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
                    <h5 class="mb-0">Batch List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3"
                        onclick="show_small_modal('{{ route('admin.batches.add') }}', 'Add Batch')">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.batches.index') }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4 col-lg-3">
                            <label for="filter_course_id" class="form-label">Course</label>
                            <select class="form-select form-select-sm" name="course_id" id="filter_course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ (int) ($selectedCourseId ?? 0) === (int) $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-filter me-1"></i> Filter
                            </button>
                        </div>
                        @if($selectedCourseId)
                        <div class="col-md-auto">
                            <a href="{{ route('admin.batches.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-x me-1"></i> Clear
                            </a>
                        </div>
                        @endif
                    </div>
                </form>

                @if($selectedCourseId)
                    @php
                        $filteredCourse = $courses->firstWhere('id', $selectedCourseId);
                    @endphp
                    <p class="text-muted small mb-3">
                        Showing batches for <strong>{{ $filteredCourse?->title ?? 'selected course' }}</strong>
                        ({{ $batches->count() }} {{ $batches->count() === 1 ? 'batch' : 'batches' }})
                    </p>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Amount</th>
                                <th>B2B Amount</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Postponed To Batch</th>
                                <th>Postponed Start Date</th>
                                <th>Postponed End Date</th>
                                <th>Postponed Amount</th>
                                <th>Postponed Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batches as $batch)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $batch->title }}</td>
                                <td>{{ $batch->course->title ?? 'N/A' }}</td>
                                <td>
                                    @if($batch->course_id == 16)
                                        <div><strong>SSLC:</strong>
                                            @if(!is_null($batch->sslc_amount))
                                                ₹ {{ number_format($batch->sslc_amount, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                        <div><strong>Plus Two:</strong>
                                            @if(!is_null($batch->plustwo_amount))
                                                ₹ {{ number_format($batch->plustwo_amount, 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    @elseif(!is_null($batch->amount))
                                        ₹ {{ number_format($batch->amount, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if(!is_null($batch->b2b_amount))
                                        ₹ {{ number_format($batch->b2b_amount, 2) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $batch->description ?? 'N/A' }}</td>
                                <td>
                                    @if($batch->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($batch->postponeBatch)
                                        <span class="badge bg-info">{{ $batch->postponeBatch->title }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($batch->postpone_start_date)
                                        {{ \Carbon\Carbon::parse($batch->postpone_start_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($batch->postpone_end_date)
                                        {{ \Carbon\Carbon::parse($batch->postpone_end_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!is_null($batch->batch_postpone_amount))
                                        ₹ {{ number_format($batch->batch_postpone_amount, 2) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($batch->is_postpone_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $batch->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info"
                                            onclick="show_small_modal('{{ route('admin.batches.edit', $batch->id) }}', 'Edit Batch')">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-warning"
                                            onclick="show_ajax_modal('{{ route('admin.batches.postpone', $batch->id) }}', 'Postponed Batch')" title="Postponed">
                                            <i class="ti ti-calendar-time"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-danger"
                                            onclick="delete_modal('{{ route('admin.batches.destroy', $batch->id) }}')" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="14" class="text-center text-muted py-4">
                                    @if($selectedCourseId)
                                        No batches found for this course.
                                    @else
                                        No batches found.
                                    @endif
                                </td>
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
