@extends('layouts.mantis')

@section('title', 'Telecaller Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Telecaller Report</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                    <li class="breadcrumb-item">Telecaller Report</li>
                </ul>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.reports.leads') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-arrow-left me-1"></i>Back to Reports
                </a>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Printable Report Content ] start -->
<div class="printable-report">
    <div class="header text-center mb-4" style="display: none;">
        <h1>Telecaller Report</h1>
        <p>Report Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</p>
        <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
    </div>
</div>
<!-- [ Printable Report Content ] end -->

<!-- [ Filter Form ] start -->
<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.telecaller') }}" id="reportFilterForm">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ $toDate }}">
                        </div>
                        <div class="col-md-2">
                            <label for="telecaller_id" class="form-label">Select Telecaller</label>
                            <select class="form-select" id="telecaller_id" name="telecaller_id">
                                <option value="">All Telecallers</option>
                                @foreach($telecallers as $telecaller)
                                    <option value="{{ $telecaller->id }}" {{ $telecallerId == $telecaller->id ? 'selected' : '' }}>
                                        {{ $telecaller->name }} {{ $telecaller->phone ? '(' . $telecaller->phone . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 flex-wrap align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Generate Report
                                </button>
                                <a href="{{ route('admin.reports.telecaller') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.reports.telecaller.excel', request()->query()) }}" class="btn btn-success">
                                        <i class="ti ti-file-excel"></i> Excel
                                    </a>
                                    <a href="{{ route('admin.reports.telecaller.pdf', request()->query()) }}" class="btn btn-danger">
                                        <i class="ti ti-file-pdf"></i> PDF
                                    </a>
                                </div>
                                <button type="button" class="btn btn-info" onclick="printReport()">
                                    <i class="ti ti-printer"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Form ] end -->

<!-- [ Report Summary ] start -->
<div class="printable-report">
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Report Summary</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-primary mb-2">{{ $reports['telecaller']->sum('count') ?? 0 }}</h3>
                            <p class="text-muted mb-0 fw-medium">Total Leads</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-success mb-2">{{ $reports['telecaller']->count() ?? 0 }}</h3>
                            <p class="text-muted mb-0 fw-medium">Active Telecallers</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-info mb-2">{{ $reports['telecaller']->avg('count') ? round($reports['telecaller']->avg('count'), 1) : 0 }}</h3>
                            <p class="text-muted mb-0 fw-medium">Avg Leads per Telecaller</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-warning mb-2">{{ $reports['telecaller']->max('count') ?? 0 }}</h3>
                            <p class="text-muted mb-0 fw-medium">Highest Leads</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Report Summary ] end -->

<!-- [ Telecaller Performance Report ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-phone me-2"></i>Telecaller Performance Report
                </h5>
            </div>
            <div class="card-body">
                @if($reports['telecaller']->count() > 0)
                    <div class="table-responsive">
                        <table id="telecallerReportTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Telecaller</th>
                                    <th>Team</th>
                                    <th class="text-end">Leads</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports['telecaller'] as $index => $telecaller)
                                    @php
                                        $total = $reports['telecaller']->sum('count');
                                        $percentage = $total > 0 ? round(($telecaller->count / $total) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                    <i class="ti ti-phone"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $telecaller->name }}</h6>
                                                    <small class="text-muted">Phone: {{ $telecaller->phone ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $telecaller->team_name ?? 'No Team' }}</span>
                                        </td>
                                        <td class="text-end fw-bold">{{ $telecaller->count }}</td>
                                        <td class="text-end">{{ $percentage }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-phone f-48 mb-3"></i>
                        <p>No data available for the selected date range</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Telecaller Performance Report ] end -->

<!-- [ Monthly Trend ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-chart-line me-2"></i>Monthly Trend
                </h5>
            </div>
            <div class="card-body">
                @if($reports['monthly']->count() > 0)
                    <div class="table-responsive">
                        <table id="monthlyTrendTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Leads</th>
                                    <th class="text-end">Growth</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports['monthly'] as $index => $month)
                                    @php
                                        $previousMonth = $reports['monthly']->get($index - 1);
                                        $growth = $previousMonth && isset($previousMonth->count) && $previousMonth->count > 0 
                                            ? round((($month->count - $previousMonth->count) / $previousMonth->count) * 100, 1)
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $month->month }}</td>
                                        <td class="text-end fw-bold">{{ $month->count }}</td>
                                        <td class="text-end">
                                            @if($growth > 0)
                                                <span class="text-success">+{{ $growth }}%</span>
                                            @elseif($growth < 0)
                                                <span class="text-danger">{{ $growth }}%</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-chart-line f-48 mb-3"></i>
                        <p>No monthly data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Monthly Trend ] end -->

<!-- [ Leads Data ] start -->
@if($telecallerId)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-list me-2"></i>Leads Data - {{ $telecallers->where('id', $telecallerId)->first()->name ?? 'Selected Telecaller' }}
                </h5>
            </div>
            <div class="card-body">
                @if($leads->count() > 0)
                    <div class="table-responsive">
                        <table id="leadsDataTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $index => $lead)
                                    <tr>
                                        <td>{{ $leads->firstItem() + $index }}</td>
                                        <td>{{ $lead->title }}</td>
                                        <td>{{ $lead->phone }}</td>
                                        <td>{{ $lead->email }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $lead->leadStatus->color ?? '#6c757d' }}; color: white;">
                                                {{ $lead->leadStatus->title ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $lead->leadSource->title ?? 'N/A' }}</td>
                                        <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-list f-48 mb-3"></i>
                        <p>No leads found for the selected telecaller</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ti ti-user-search f-48 text-muted mb-3"></i>
                <h5 class="text-muted">Select a Telecaller</h5>
                <p class="text-muted">Please select a telecaller from the dropdown above to view detailed leads data.</p>
            </div>
        </div>
    </div>
</div>
@endif
<!-- [ Leads Data ] end -->
</div>
<!-- [ Printable Report Content ] end -->

@endsection

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .printable-report, .printable-report * {
        visibility: visible;
    }
    .printable-report {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .btn {
        display: none !important;
    }
    .breadcrumb {
        display: none !important;
    }
    .page-header {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable for Telecaller Report
    if ($.fn.DataTable.isDataTable('#telecallerReportTable')) {
        $('#telecallerReportTable').DataTable().destroy();
    }
    $('#telecallerReportTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[3, 'desc']], // Sort by leads count descending
        columnDefs: [
            { orderable: false, targets: [0] } // Disable sorting on # column
        ],
        language: {
            search: "Search telecallers:",
            lengthMenu: "Show _MENU_ telecallers per page",
            info: "Showing _START_ to _END_ of _TOTAL_ telecallers",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Initialize DataTable for Monthly Trend
    if ($.fn.DataTable.isDataTable('#monthlyTrendTable')) {
        $('#monthlyTrendTable').DataTable().destroy();
    }
    $('#monthlyTrendTable').DataTable({
        responsive: true,
        pageLength: 12,
        lengthMenu: [[6, 12, 24, -1], [6, 12, 24, "All"]],
        order: [[0, 'asc']], // Sort by month ascending
        language: {
            search: "Search months:",
            lengthMenu: "Show _MENU_ months per page",
            info: "Showing _START_ to _END_ of _TOTAL_ months",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Initialize DataTable for Leads Data
    if ($.fn.DataTable.isDataTable('#leadsDataTable')) {
        $('#leadsDataTable').DataTable().destroy();
    }
    $('#leadsDataTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[6, 'desc']], // Sort by date descending
        columnDefs: [
            { orderable: false, targets: [0] } // Disable sorting on # column
        ],
        language: {
            search: "Search leads:",
            lengthMenu: "Show _MENU_ leads per page",
            info: "Showing _START_ to _END_ of _TOTAL_ leads",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});

function printReport() {
    // Create a printable version of the report
    const printContent = document.querySelector('.printable-report');
    if (printContent) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Telecaller Report - Print</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                    .header h1 { margin: 0; color: #333; font-size: 24px; }
                    .header p { margin: 5px 0; color: #666; }
                    .summary { margin-bottom: 30px; }
                    .summary h3 { color: #333; margin-bottom: 15px; }
                    .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px; }
                    .summary-item { text-align: center; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
                    .summary-item h4 { margin: 0; font-size: 18px; color: #333; }
                    .summary-item p { margin: 5px 0 0 0; color: #666; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                    .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
                </style>
            </head>
            <body>
                ${printContent.innerHTML}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    } else {
        window.print();
    }
}
</script>
@endpush
