@extends('layouts.mantis')

@section('title', 'BDE Collected Amount Course Ways Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">BDE Collected Amount Course Ways Report</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Reports</li>
                    <li class="breadcrumb-item">Post Sales Reports</li>
                    <li class="breadcrumb-item">BDE Collected Amount</li>
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
                <form method="GET" action="{{ route('admin.reports.bde-collected-amount-course-ways') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-3">
                            <label for="post_sales_user_id" class="form-label">Select Post Sales User</label>
                            <select class="form-select" name="post_sales_user_id" id="post_sales_user_id" required>
                                <option value="">-- Select Post Sales User --</option>
                                @foreach($postSalesUsers as $user)
                                    <option value="{{ $user->id }}" {{ (string)$selectedPostSalesId === (string)$user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="from_date" class="form-label">From Date (optional)</label>
                            <input type="date" class="form-control" id="from_date" name="from_date"
                                   value="{{ $fromDate ?? '' }}">
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="to_date" class="form-label">To Date (optional)</label>
                            <input type="date" class="form-control" id="to_date" name="to_date"
                                   value="{{ $toDate ?? '' }}">
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Generate Report
                                </button>
                                <a href="{{ route('admin.reports.bde-collected-amount-course-ways') }}" class="btn btn-outline-secondary">
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
<!-- [ Filter Section ] end -->

@php
    $selectedUser = $postSalesUsers->firstWhere('id', (int) $selectedPostSalesId);
@endphp

<!-- [ Reports Content ] start -->
@if(!$selectedPostSalesId)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ti ti-filter f-48 text-muted mb-3"></i>
                    <p class="text-muted mb-0">Please select a Post Sales user to view the report.</p>
                </div>
            </div>
        </div>
    </div>
@elseif(count($reportData) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-chart-line me-2"></i>BDE Collected Amount Course Ways Report
                        @if($selectedUser)
                            <small class="text-muted">
                                ({{ $selectedUser->name }} &middot; Approved Payments
                                @if(!empty($fromDate) || !empty($toDate))
                                    &middot;
                                    @if($fromDate)
                                        From {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
                                    @endif
                                    @if($toDate)
                                        @if($fromDate) - @endif
                                        To {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                                    @endif
                                @endif
                                )
                            </small>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Course Name</th>
                                    <th class="text-end">Student Count</th>
                                    <th class="text-end">Amount Collected</th>
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
                    <p class="text-muted mb-0">No approved payments found for the selected Post Sales user{{ ($fromDate || $toDate) ? ' and selected date range' : '' }}.</p>
                </div>
            </div>
        </div>
    </div>
@endif
<!-- [ Reports Content ] end -->

@endsection

