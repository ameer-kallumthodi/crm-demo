@extends('layouts.mantis')

@section('title', 'Lead Stage Movement Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Lead Stage Movement Report</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Lead Stage Movement</li>
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
                    <form method="GET" action="{{ route('admin.reports.lead-stage-movement') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $toDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="lead_source_id" class="form-label">Lead Source</label>
                            <select class="form-select" id="lead_source_id" name="lead_source_id">
                                <option value="">All Sources</option>
                                @foreach($leadSources as $source)
                                    <option value="{{ $source->id }}" {{ $leadSourceId == $source->id ? 'selected' : '' }}>
                                        {{ $source->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Apply Filter</button>
                                <a href="{{ route('admin.reports.lead-stage-movement') }}" class="btn btn-secondary">Reset</a>
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
                        <a href="{{ route('admin.reports.lead-stage-movement.export.excel', request()->query()) }}" class="btn btn-success">
                            <i class="ti ti-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('admin.reports.lead-stage-movement.export.pdf', request()->query()) }}" class="btn btn-danger">
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
                                    <i class="ti ti-users text-primary"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Total Leads</p>
                            <h4 class="mb-0">{{ $stageData['summary']['total_leads'] }}</h4>
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
                            <div class="avatar-sm rounded bg-danger-subtle">
                                <span class="avatar-title rounded">
                                    <i class="ti ti-alert-triangle text-danger"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Stuck Leads </p>
                            <h4 class="mb-0">{{ $stageData['summary']['total_stuck_leads'] }}</h4>
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
                                    <i class="ti ti-arrow-right text-info"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Follow-Up Leads</p>
                            <h4 class="mb-0">{{ $stageData['summary']['total_follow_up_leads'] }}</h4>
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
                                    <i class="ti ti-check text-success"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Converted Leads</p>
                            <h4 class="mb-0">{{ $stageData['summary']['total_converted_leads'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stuck Leads Percentage -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Stuck Leads Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-danger">{{ $stageData['summary']['stuck_percentage'] }}%</h3>
                                <p class="text-muted mb-0">Leads Stuck (5+ days)</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-info">{{ $stageData['summary']['follow_up_percentage'] }}%</h3>
                                <p class="text-muted mb-0">Follow-Up Rate</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-success">{{ $stageData['summary']['conversion_percentage'] }}%</h3>
                                <p class="text-muted mb-0">Conversion Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stage Movement Report -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lead Stage Movement by Status</h5>
                    <p class="text-muted mb-0">Showing data from {{ date('d M Y', strtotime($fromDate)) }} to {{ date('d M Y', strtotime($toDate)) }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="stageMovementTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Lead Name</th>
                                    <th>Phone</th>
                                    <th>Source</th>
                                    <th>Telecaller</th>
                                    <th>Current Status</th>
                                    <th>Created Date</th>
                                    <th>Last Activity</th>
                                    <th>Days Since Update</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $serialNumber = 1; @endphp
                                @foreach($stageData['status_groups'] as $statusGroup)
                                    @foreach($statusGroup['leads'] as $lead)
                                    <tr class="{{ $lead->is_stuck ? 'table-danger' : '' }}">
                                        <td>{{ $serialNumber++ }}</td>
                                        <td>{{ $lead->title }}</td>
                                        <td>{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $lead->source_name ?: 'Unknown' }}</span>
                                        </td>
                                        <td>
                                            @if($lead->telecaller_name)
                                                <span class="badge bg-info">{{ $lead->telecaller_name }}</span>
                                            @else
                                                <span class="badge bg-secondary">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->lead_status_id) }}">
                                                {{ $statusGroup['status_name'] }}
                                            </span>
                                        </td>
                                        <td>{{ $lead->created_at->format('d-m-Y h:i A') }}</td>
                                        <td>{{ $lead->last_activity_date->format('d-m-Y h:i A') }}</td>
                                        <td>
                                            <span class="badge {{ $lead->is_stuck ? 'bg-danger' : 'bg-warning' }}">
                                                {{ $lead->days_since_last_update }} days
                                            </span>
                                        </td>
                                        <td>
                                            @if($lead->is_stuck)
                                                <span class="badge bg-danger">Stuck</span>
                                            @else
                                                <span class="badge bg-warning">Active</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
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
    if (!$.fn.DataTable.isDataTable('#stageMovementTable')) {
        // Initialize DataTable
        $('#stageMovementTable').DataTable({
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[9, "desc"]], // Sort by Days Since Update descending
            "columnDefs": [
                {
                    "targets": [9], // Days Since Update column
                    "type": "num"
                }
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'excel', 'pdf', 'print'
            ],
            "responsive": true,
            "language": {
                "emptyTable": "No leads found for the selected date range.",
                "zeroRecords": "No matching records found"
            }
        });
    }
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
