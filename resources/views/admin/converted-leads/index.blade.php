@extends('layouts.mantis')

@section('title', 'Converted Leads')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Converted Leads Management</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Converted Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.converted-leads.index') }}" id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Name, Phone, Email">
                        </div>
                        <div class="col-md-2">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.converted-leads.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Converted Leads List</h5>
            </div>
            <div class="card-body">
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover datatable" id="convertedLeadsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Academic Assistant</th>
                                <th>Converted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeads as $index => $convertedLead)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-s rounded-circle bg-light-success me-2 d-flex align-items-center justify-content-center">
                                            <span class="f-16 fw-bold text-success">{{ strtoupper(substr($convertedLead->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $convertedLead->name }}</h6>
                                            <small class="text-muted">ID: {{ $convertedLead->lead_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</td>
                                <td>{{ $convertedLead->email ?? 'N/A' }}</td>
                                <td>{{ $convertedLead->course ? $convertedLead->course->title : 'N/A' }}</td>
                                <td>{{ $convertedLead->academicAssistant ? $convertedLead->academicAssistant->name : 'N/A' }}</td>
                                <td>{{ $convertedLead->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No converted leads found</td>
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
$(document).ready(function() {
    // Handle filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const params = new URLSearchParams();
        
        // Add form data to params
        for (let [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                params.append(key, value);
            }
        }
        
        // Redirect with filter parameters
        const url = new URL(window.location.href);
        url.search = params.toString();
        window.location.href = url.toString();
    });

    // Handle clear button
    $('a[href="{{ route("admin.converted-leads.index") }}"]').on('click', function(e) {
        e.preventDefault();
        window.location.href = '{{ route("admin.converted-leads.index") }}';
    });
});
</script>
@endpush
