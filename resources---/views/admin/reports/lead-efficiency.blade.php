@extends('layouts.mantis')

@section('title', 'Lead Source Efficiency Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Lead Source Efficiency Report</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Lead Source Efficiency</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filter Options</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.lead-efficiency') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $toDate }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Apply Filter</button>
                                <a href="{{ route('admin.reports.lead-efficiency') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.reports.lead-efficiency.export.excel', request()->query()) }}" class="btn btn-success">
                            <i class="ti ti-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('admin.reports.lead-efficiency.export.pdf', request()->query()) }}" class="btn btn-danger">
                            <i class="ti ti-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-primary-subtle">
                                <span class="avatar-title rounded">
                                    <i class="ti ti-chart-line text-primary"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Sources</p>
                            <h4 class="mb-0">{{ $efficiencyData->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-success-subtle">
                                <span class="avatar-title rounded">
                                    <i class="ti ti-target text-success"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Leads</p>
                            <h4 class="mb-0">{{ $efficiencyData->sum('total_leads') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-info-subtle">
                                <span class="avatar-title rounded">
                                    <i class="ti ti-check text-info"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Converted Leads</p>
                            <h4 class="mb-0">{{ $efficiencyData->sum('converted_leads') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded bg-warning-subtle">
                                <span class="avatar-title rounded">
                                    <i class="ti ti-percentage text-warning"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Avg Conversion Rate</p>
                            <h4 class="mb-0">{{ $efficiencyData->count() > 0 ? round($efficiencyData->avg('conversion_rate'), 1) : 0 }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Efficiency Report Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lead Source Efficiency Report</h5>
                    <p class="text-muted mb-0">Showing data from {{ date('d M Y', strtotime($fromDate)) }} to {{ date('d M Y', strtotime($toDate)) }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="efficiencyTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Lead Source</th>
                                    <th>Total Leads</th>
                                    <th>Converted Leads</th>
                                    <th>Follow-Up Leads</th>
                                    <th>Interested Leads</th>
                                    <th>Conversion Rate</th>
                                    <th>Follow-Up Rate</th>
                                    <th>Interested Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($efficiencyData as $index => $source)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $source->source_name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $source->total_leads }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $source->converted_leads }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $source->follow_up_leads }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $source->interested_leads }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ min($source->conversion_rate, 100) }}%"></div>
                                            </div>
                                            <span class="fw-medium">{{ $source->conversion_rate }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-info" role="progressbar" 
                                                     style="width: {{ min($source->follow_up_rate, 100) }}%"></div>
                                            </div>
                                            <span class="fw-medium">{{ $source->follow_up_rate }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" role="progressbar" 
                                                     style="width: {{ min($source->interested_rate, 100) }}%"></div>
                                            </div>
                                            <span class="fw-medium">{{ $source->interested_rate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No data available for the selected date range.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable('#efficiencyTable')) {
        // Initialize DataTable
        $('#efficiencyTable').DataTable({
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[6, "desc"]], // Sort by Conversion Rate descending
            "columnDefs": [
                {
                    "targets": [2, 3, 4, 5], // Numeric columns
                    "type": "num"
                },
                {
                    "targets": [6, 7, 8], // Percentage columns
                    "type": "num"
                }
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'excel', 'pdf', 'print'
            ],
            "responsive": true,
            "language": {
                "emptyTable": "No data available for the selected date range.",
                "zeroRecords": "No matching records found"
            }
        });
    }
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
