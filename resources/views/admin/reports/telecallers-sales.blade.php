@extends('layouts.mantis')

@section('title', 'Telecallers Sales Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Telecallers Sales Report</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Reports</li>
                    <li class="breadcrumb-item">Finance Reports</li>
                    <li class="breadcrumb-item">Telecallers Sales Report</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Filters ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.telecallers-sales') }}" id="reportFilterForm">
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
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="telecaller_id" class="form-label">Telecaller</label>
                            <select class="form-select" id="telecaller_id" name="telecaller_id">
                                <option value="">All Telecallers</option>
                                @foreach($telecallers as $telecaller)
                                    <option value="{{ $telecaller->id }}" {{ $selectedTelecallerId == $telecaller->id ? 'selected' : '' }}>
                                        {{ $telecaller->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 mt-3 mt-md-0">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Generate Report
                                </button>
                                <a href="{{ route('admin.reports.telecallers-sales') }}" class="btn btn-outline-secondary">
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
<!-- [ Filters ] end -->

<!-- [ Summary Cards ] start -->
@if(count($reports) > 0)
    @php
        $totalSalesCount = collect($reports)->sum('sales_count');
        $totalSaleAmount = collect($reports)->sum('total_sale_amount');
        $totalReceivedAtSale = collect($reports)->sum('received_at_sale');
    @endphp
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-primary">
                                <i class="ti ti-users text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Total Sales Count</h6>
                            <h4 class="mb-0">{{ number_format($totalSalesCount) }}</h4>
                            <small class="text-muted">Converted Leads</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-success">
                                <i class="ti ti-currency-rupee text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Total Sale Amount</h6>
                            <h4 class="mb-0">₹{{ number_format(round($totalSaleAmount)) }}</h4>
                            <small class="text-muted">Invoice Total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-info">
                                <i class="ti ti-cash text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Received at Sale (DP)</h6>
                            <h4 class="mb-0">₹{{ number_format(round($totalReceivedAtSale)) }}</h4>
                            <small class="text-muted">Payments Collected & Approved</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
<!-- [ Summary Cards ] end -->

<!-- [ Reports Content ] start -->
@if(count($reports) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-chart-line me-2"></i>Telecallers Sales Report
                        @if($fromDate && $toDate)
                            <small class="text-muted">({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }})</small>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Sl No</th>
                                    <th>Telecaller Name</th>
                                    <th class="text-end">Sales Count<br><small class="text-muted">(Converted Leads)</small></th>
                                    <th class="text-end">Total Sale Amount<br><small class="text-muted">(Invoice Total)</small></th>
                                    <th class="text-end">Received at Sale (DP)<br><small class="text-muted">(Payments Collected & Approved)</small></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports as $index => $report)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $report['telecaller']->name }}</strong>
                                            @if($report['telecaller']->email)
                                                <br><small class="text-muted">{{ $report['telecaller']->email }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-info">{{ number_format($report['sales_count']) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>₹{{ number_format(round($report['total_sale_amount'])) }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">₹{{ number_format(round($report['received_at_sale'])) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th class="text-center">-</th>
                                    <th>Total</th>
                                    <th class="text-end">
                                        <span class="badge bg-primary">{{ number_format(collect($reports)->sum('sales_count')) }}</span>
                                    </th>
                                    <th class="text-end">
                                        <strong>₹{{ number_format(round(collect($reports)->sum('total_sale_amount'))) }}</strong>
                                    </th>
                                    <th class="text-end">
                                        <strong class="text-success">₹{{ number_format(round(collect($reports)->sum('received_at_sale'))) }}</strong>
                                    </th>
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
                    <p class="text-muted mb-0">No data found for the selected filters.</p>
                </div>
            </div>
        </div>
    </div>
@endif
<!-- [ Reports Content ] end -->

@endsection

