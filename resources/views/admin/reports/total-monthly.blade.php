@extends('layouts.mantis')

@section('title', 'Total Monthly Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Total Monthly Report</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Reports</li>
                    <li class="breadcrumb-item">Post Sales Reports</li>
                    <li class="breadcrumb-item">Total Monthly</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Month Filter ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.total-monthly') }}" id="reportFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-4">
                            <label for="month" class="form-label">Select Month</label>
                            <input type="month" class="form-control" id="month" name="month" 
                                   value="{{ $month }}" required>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Generate Report
                                </button>
                                <a href="{{ route('admin.reports.total-monthly') }}" class="btn btn-outline-secondary">
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
<!-- [ Month Filter ] end -->

<!-- [ Reports Content ] start -->
@if(count($reportData) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-chart-bar me-2"></i>Total Monthly Report - {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Course Name</th>
                                    <th class="text-end">Student Count</th>
                                    <th class="text-end">Collected Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData as $row)
                                    <tr>
                                        <td>{{ $row['course_name'] }}</td>
                                        <td class="text-end">{{ number_format($row['student_count']) }}</td>
                                        <td class="text-end">₹{{ number_format($row['total_amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>Grand Total</th>
                                    <th class="text-end">{{ number_format($grandTotalStudents) }}</th>
                                    <th class="text-end">₹{{ number_format($grandTotal, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ti ti-inbox f-48 text-muted mb-3"></i>
                    <p class="text-muted mb-0">No data found for the selected month.</p>
                </div>
            </div>
        </div>
    </div>
@endif
<!-- [ Reports Content ] end -->

@endsection

