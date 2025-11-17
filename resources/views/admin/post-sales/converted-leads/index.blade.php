@extends('layouts.mantis')

@section('title', 'Post-sales Converted Students')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Post-sales Converted Students</h5>
                    <p class="m-b-0 text-muted">Review converted students with quick access to their full history.</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Post-sales Converted Students</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-filter me-2"></i>Filters
                </h5>
                <a href="{{ route('admin.post-sales.converted-leads.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-refresh"></i> Reset
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, phone, email or register no.">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Converted Students</h5>
                <span class="badge bg-light-primary text-primary">{{ $convertedLeads->count() }} records</span>
            </div>
            <div class="table-responsive">
                <table id="postSalesConvertedTable" class="table table-striped table-hover mb-0 align-middle datatable" data-order='[[4,"desc"]]' data-page-length="25">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Converted Date</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Admission Batch</th>
                            <th>Subject</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($convertedLeads as $convertedLead)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $convertedLead->name }}</div>
                                    <small class="text-muted">{{ $convertedLead->register_number ?? 'No register #' }}</small>
                                </td>
                                <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                <td>{{ $convertedLead->email ?? 'N/A' }}</td>
                                <td>{{ $convertedLead->created_at ? $convertedLead->created_at->format('d M Y h:i A') : 'N/A' }}</td>
                                <td>{{ $convertedLead->course?->title ?? 'N/A' }}</td>
                                <td>{{ $convertedLead->batch?->title ?? 'N/A' }}</td>
                                <td>{{ $convertedLead->admissionBatch?->title ?? 'N/A' }}</td>
                                <td>{{ $convertedLead->subject?->title ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.post-sales.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="ti ti-database-off f-24 d-block mb-2"></i>
                                    <span class="text-muted">No converted students found for the applied filters.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

