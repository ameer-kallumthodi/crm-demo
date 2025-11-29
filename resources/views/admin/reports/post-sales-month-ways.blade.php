@extends('layouts.mantis')

@section('title', 'Post Sales Month Ways Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Post Sales Month Ways Report</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Reports</li>
                    <li class="breadcrumb-item">Post Sales Reports</li>
                    <li class="breadcrumb-item">Month Ways Report</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Date Range Filter ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.post-sales-month-ways') }}" id="reportFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="from_date" name="from_date"
                                   value="{{ $fromDate ?? '' }}" required>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="to_date" name="to_date"
                                   value="{{ $toDate ?? '' }}" required>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 mt-3 mt-md-0">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Generate Report
                                </button>
                                <a href="{{ route('admin.reports.post-sales-month-ways') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Date Range Filter ] end -->

<!-- [ Reports Content ] start -->
@if(count($reports) > 0)
    <div class="row">
        @foreach($reports as $report)
            <div class="col-12 col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ti ti-user me-2"></i>{{ $report['user']->name }}
                            @if($report['user']->is_head == 1)
                                <span class="badge bg-primary ms-2">Head</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($report['data']) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Course Name</th>
                                            <th class="text-end">Student Count</th>
                                            <th class="text-end">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($report['data'] as $row)
                                            <tr>
                                                <td>{{ $row['course_name'] }}</td>
                                                <td class="text-end">{{ number_format($row['student_count']) }}</td>
                                                <td class="text-end">₹{{ number_format($row['total_amount'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-end">{{ number_format(collect($report['data'])->sum('student_count')) }}</th>
                                            <th class="text-end">₹{{ number_format(collect($report['data'])->sum('total_amount'), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">No data found for this user in the selected month.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ti ti-inbox f-48 text-muted mb-3"></i>
                    <p class="text-muted mb-0">No post sales users found.</p>
                </div>
            </div>
        </div>
    </div>
@endif
<!-- [ Reports Content ] end -->

@endsection

