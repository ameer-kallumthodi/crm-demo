@extends('layouts.mantis')

@section('title', 'Course-wise Summary Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Course-wise Summary Report</h5>
                    <p class="m-b-0">Comprehensive analysis of leads and conversions by course</p>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Reports</li>
                    <li class="breadcrumb-item">Course Summary</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Filter Section ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.course-summary') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" name="course_id" id="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" name="date_from" id="date_from" 
                                   value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" name="date_to" id="date_to" 
                                   value="{{ $toDate }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.reports.course-summary') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-x"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Summary Cards ] start -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-primary">
                            <i class="ti ti-book text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Total Courses</h6>
                        <h4 class="mb-0">{{ $courseSummary->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-success">
                            <i class="ti ti-users text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Total Leads</h6>
                        <h4 class="mb-0">{{ $courseSummary->sum('total_leads') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-warning">
                            <i class="ti ti-user-check text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Converted Leads</h6>
                        <h4 class="mb-0">{{ $courseSummary->sum('converted_leads') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s bg-light-info">
                            <i class="ti ti-clock text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Follow-up Leads</h6>
                        <h4 class="mb-0">{{ $courseSummary->sum('followup_leads') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Summary Cards ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course-wise Summary Report</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reports.course-summary.excel', request()->query()) }}" 
                           class="btn btn-outline-success btn-sm">
                            <i class="ti ti-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('admin.reports.course-summary.pdf', request()->query()) }}" 
                           class="btn btn-outline-danger btn-sm">
                            <i class="ti ti-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover data_table_basic">
                        <thead class="table-light">
                            <tr>
                                <th class="no-sort">#</th>
                                <th>Course Name</th>
                                <th class="text-center">Total Leads</th>
                                <th class="text-center">Converted Leads</th>
                                <th class="text-center">Follow-up Leads</th>
                                <th class="text-center">Other Status</th>
                                <th class="text-center">Conversion Rate</th>
                                <th class="no-sort">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courseSummary as $index => $course)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avtar avtar-s bg-light-primary me-2">
                                            <i class="ti ti-book text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $course['course_name'] }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $course['total_leads'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $course['converted_leads'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $course['followup_leads'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $course['other_leads'] }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                            <div class="progress-bar 
                                                @if($course['conversion_rate'] >= 20) bg-success
                                                @elseif($course['conversion_rate'] >= 10) bg-warning
                                                @else bg-danger
                                                @endif"
                                                role="progressbar" 
                                                style="width: {{ min($course['conversion_rate'], 100) }}%"
                                                aria-valuenow="{{ $course['conversion_rate'] }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="fw-bold 
                                            @if($course['conversion_rate'] >= 20) text-success
                                            @elseif($course['conversion_rate'] >= 10) text-warning
                                            @else text-danger
                                            @endif">
                                            {{ $course['conversion_rate'] }}%
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.reports.course-leads', $course['course_id']) }}?date_from={{ $fromDate }}&date_to={{ $toDate }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Leads">
                                            <i class="ti ti-users"></i>
                                        </a>
                                        <a href="{{ route('admin.reports.course-converted-leads', $course['course_id']) }}?date_from={{ $fromDate }}&date_to={{ $toDate }}" 
                                           class="btn btn-sm btn-outline-success" title="View Converted Leads">
                                            <i class="ti ti-user-check"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avtar avtar-xl bg-light-secondary mb-3">
                                            <i class="ti ti-book-x text-secondary"></i>
                                        </div>
                                        <h5 class="text-muted">No Course Data Found</h5>
                                        <p class="text-muted">No course data available for the selected date range.</p>
                                    </div>
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

<!-- [ Chart Section ] start -->
@if($courseSummary->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Conversion Rate by Course</h5>
            </div>
            <div class="card-body">
                <canvas id="conversionChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Chart Section ] end -->

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize chart if data exists
    @if($courseSummary->count() > 0)
    const ctx = document.getElementById('conversionChart').getContext('2d');
    const courseData = @json($courseSummary);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: courseData.map(course => course.course_name),
            datasets: [{
                label: 'Conversion Rate (%)',
                data: courseData.map(course => course.conversion_rate),
                backgroundColor: courseData.map(course => 
                    course.conversion_rate >= 20 ? '#28a745' :
                    course.conversion_rate >= 10 ? '#ffc107' : '#dc3545'
                ),
                borderColor: courseData.map(course => 
                    course.conversion_rate >= 20 ? '#1e7e34' :
                    course.conversion_rate >= 10 ? '#e0a800' : '#c82333'
                ),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Conversion Rate: ' + context.parsed.y + '%';
                        }
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endpush
