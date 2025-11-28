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


<!-- [ Reports Content ] start -->
@if(count($reportData) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-chart-line me-2"></i>BDE Collected Amount Course Ways Report
                        <small class="text-muted">(All Fully Paid Invoices)</small>
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
                    <p class="text-muted mb-0">No fully paid invoices found.</p>
                </div>
            </div>
        </div>
    </div>
@endif
<!-- [ Reports Content ] end -->

@endsection

