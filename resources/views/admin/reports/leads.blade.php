@extends('layouts.mantis')

@section('title', 'Lead Reports')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Lead Reports</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Reports</li>
                    <li class="breadcrumb-item">Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Date Filter ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.leads') }}" id="reportFilterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ $fromDate }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ $toDate }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> <span class="d-none d-sm-inline">Generate Report</span>
                                </button>
                                <a href="{{ route('admin.reports.leads') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i> <span class="d-none d-sm-inline">Reset</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.reports.main.excel', request()->query()) }}" class="btn btn-success btn-sm">
                                        <i class="ti ti-file-excel"></i> <span class="d-none d-sm-inline">Excel</span>
                                    </a>
                                    <a href="{{ route('admin.reports.main.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                                        <i class="ti ti-file-pdf"></i> <span class="d-none d-sm-inline">PDF</span>
                                    </a>
                                </div>
                                <button type="button" class="btn btn-info btn-sm" onclick="printReport()">
                                    <i class="ti ti-printer"></i> <span class="d-none d-sm-inline">Print</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Date Filter ] end -->

<!-- [ Printable Report Content ] start -->
<div class="printable-report">
    <div class="header text-center mb-4" style="display: none;">
        <h1>Main Reports</h1>
        <p>Report Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</p>
        <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
    </div>
</div>
<!-- [ Printable Report Content ] end -->

<!-- [ Reports Summary ] start -->
<div class="printable-report">
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Report Summary</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-primary mb-2 f-24">{{ $reports['lead_status']->sum('count') }}</h3>
                            <p class="text-muted mb-0 fw-medium f-14">Total Leads</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-success mb-2 f-24">{{ $reports['lead_status']->where('title', 'Converted')->first()->count ?? 0 }}</h3>
                            <p class="text-muted mb-0 fw-medium f-14">Converted</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-info mb-2 f-24">{{ $reports['lead_source']->count() }}</h3>
                            <p class="text-muted mb-0 fw-medium f-14">Lead Sources</p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="text-center p-3 border rounded">
                            <h3 class="text-warning mb-2 f-24">{{ $reports['team']->count() }}</h3>
                            <p class="text-muted mb-0 fw-medium f-14">Active Teams</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Reports Summary ] end -->

<!-- [ Reports Content ] start -->
<div class="row">
    <!-- Lead Status Report -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-chart-pie me-2"></i>Lead Status Report
                </h5>
                <a href="{{ route('admin.reports.lead-status', ['date_from' => $fromDate, 'date_to' => $toDate]) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-eye"></i> Detailed Report
                </a>
            </div>
            <div class="card-body">
                @if($reports['lead_status']->count() > 0)
                    <div class="table-responsive">
                        <table id="leadStatusTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports['lead_status'] as $status)
                                    @php
                                        $total = $reports['lead_status']->sum('count');
                                        $percentage = $total > 0 ? round(($status->count / $total) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($status->id) }}">
                                                {{ $status->title }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ $status->count }}</td>
                                        <td class="text-end">{{ $percentage }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-chart-pie f-48 mb-3"></i>
                        <p>No data available for the selected date range</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Lead Source Report -->
    <div class="col-12 col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-tag me-2"></i>Lead Source Report
                </h5>
                <a href="{{ route('admin.reports.lead-source', ['date_from' => $fromDate, 'date_to' => $toDate]) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-eye"></i> <span class="d-none d-sm-inline">Detailed Report</span>
                </a>
            </div>
            <div class="card-body">
                @if($reports['lead_source']->count() > 0)
                    <!-- Desktop Table View -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table id="leadSourceTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Source</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports['lead_source'] as $source)
                                        @php
                                            $total = $reports['lead_source']->sum('count');
                                            $percentage = $total > 0 ? round(($source->count / $total) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <i class="ti ti-source me-2 text-primary"></i>
                                                {{ $source->title }}
                                            </td>
                                            <td class="text-end fw-bold">{{ $source->count }}</td>
                                            <td class="text-end">{{ $percentage }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-md-none">
                        @foreach($reports['lead_source'] as $source)
                            @php
                                $total = $reports['lead_source']->sum('count');
                                $percentage = $total > 0 ? round(($source->count / $total) * 100, 1) : 0;
                            @endphp
                            <div class="card mb-2">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-source me-2 text-primary"></i>
                                                <h6 class="mb-0 f-14">{{ $source->title }}</h6>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold f-16">{{ $source->count }}</div>
                                            <small class="text-muted f-12">{{ $percentage }}%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-source f-48 mb-3"></i>
                        <p>No data available for the selected date range</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Team Report -->
    <div class="col-12 col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>Team Report
                </h5>
                @if($isTeamLead || !$isTelecaller)
                    <a href="{{ route('admin.reports.team', ['date_from' => $fromDate, 'date_to' => $toDate]) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye"></i> <span class="d-none d-sm-inline">Detailed Report</span>
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($reports['team']->count() > 0)
                    <!-- Desktop Table View -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table id="teamTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Team</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports['team'] as $team)
                                        @php
                                            $total = $reports['team']->sum('count');
                                            $percentage = $total > 0 ? round(($team->count / $total) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <i class="ti ti-users me-2 text-success"></i>
                                                {{ $team->title }}
                                            </td>
                                            <td class="text-end fw-bold">{{ $team->count }}</td>
                                            <td class="text-end">{{ $percentage }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-md-none">
                        @foreach($reports['team'] as $team)
                            @php
                                $total = $reports['team']->sum('count');
                                $percentage = $total > 0 ? round(($team->count / $total) * 100, 1) : 0;
                            @endphp
                            <div class="card mb-2">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-users me-2 text-success"></i>
                                                <h6 class="mb-0 f-14">{{ $team->title }}</h6>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold f-16">{{ $team->count }}</div>
                                            <small class="text-muted f-12">{{ $percentage }}%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-users f-48 mb-3"></i>
                        <p>No data available for the selected date range</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Telecaller Report -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-phone me-2"></i>Telecaller Report
                </h5>
                @if($isTeamLead || !$isTelecaller)
                    <a href="{{ route('admin.reports.telecaller', ['date_from' => $fromDate, 'date_to' => $toDate]) }}" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye"></i> Detailed Report
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($reports['telecaller']->count() > 0)
                    <div class="table-responsive">
                        <table id="telecallerTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Telecaller</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports['telecaller'] as $telecaller)
                                    @php
                                        $total = $reports['telecaller']->sum('count');
                                        $percentage = $total > 0 ? round(($telecaller->count / $total) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                    <i class="ti ti-phone"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $telecaller->name }}</h6>
                                                    <small class="text-muted">{{ $telecaller->team_name ?? 'No Team' }}</small>
                                                </div>
                                            </div>
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

    <!-- B2B Report -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-chart-pie me-2"></i>B2B Report
                </h5>
                <a href="{{ route('admin.reports.b2b', ['date_from' => $fromDate, 'date_to' => $toDate]) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-eye"></i> Detailed Report
                </a>
            </div>
            <div class="card-body">
                @if($reports['b2b']->count() > 0)
                    <div class="table-responsive">
                        <table id="b2bTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports['b2b'] as $item)
                                    @php
                                        $total = $reports['b2b']->sum('count');
                                        $percentage = $total > 0 ? round(($item->count / $total) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($item->title == 'B2B')
                                                <span class="badge bg-info text-dark">B2B</span>
                                            @else
                                                <span class="badge bg-secondary text-white">In House</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">{{ $item->count }}</td>
                                        <td class="text-end">{{ $percentage }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-chart-pie f-48 mb-3"></i>
                        <p>No data available for the selected date range</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
<!-- [ Reports Content ] end -->
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
    // Initialize DataTable for Lead Status
    if ($.fn.DataTable.isDataTable('#leadStatusTable')) {
        $('#leadStatusTable').DataTable().destroy();
    }
    $('#leadStatusTable').DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
        order: [[1, 'desc']], // Sort by count descending
        language: {
            search: "Search statuses:",
            lengthMenu: "Show _MENU_ statuses per page",
            info: "Showing _START_ to _END_ of _TOTAL_ statuses",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Initialize DataTable for Lead Source
    if ($.fn.DataTable.isDataTable('#leadSourceTable')) {
        $('#leadSourceTable').DataTable().destroy();
    }
    $('#leadSourceTable').DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
        order: [[1, 'desc']], // Sort by count descending
        language: {
            search: "Search sources:",
            lengthMenu: "Show _MENU_ sources per page",
            info: "Showing _START_ to _END_ of _TOTAL_ sources",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Initialize DataTable for Team
    if ($.fn.DataTable.isDataTable('#teamTable')) {
        $('#teamTable').DataTable().destroy();
    }
    $('#teamTable').DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
        order: [[1, 'desc']], // Sort by count descending
        language: {
            search: "Search teams:",
            lengthMenu: "Show _MENU_ teams per page",
            info: "Showing _START_ to _END_ of _TOTAL_ teams",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Initialize DataTable for Telecaller
    if ($.fn.DataTable.isDataTable('#telecallerTable')) {
        $('#telecallerTable').DataTable().destroy();
    }
    $('#telecallerTable').DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
        order: [[1, 'desc']], // Sort by count descending
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

    // Initialize DataTable for B2B
    if ($.fn.DataTable.isDataTable('#b2bTable')) {
        $('#b2bTable').DataTable().destroy();
    }
    $('#b2bTable').DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]],
        order: [[1, 'desc']], // Sort by count descending
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ items per page",
            info: "Showing _START_ to _END_ of _TOTAL_ items",
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
                <title>Main Reports - Print</title>
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
