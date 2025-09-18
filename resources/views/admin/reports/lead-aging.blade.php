@extends('layouts.mantis')

@section('title', 'Lead Aging Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Lead Aging Report</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Lead Aging</li>
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
                    <form method="GET" action="{{ route('admin.reports.lead-aging') }}" class="row g-3">
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
                            <label for="lead_status_id" class="form-label">Lead Status</label>
                            <select class="form-select" id="lead_status_id" name="lead_status_id">
                                <option value="">All Statuses</option>
                                @foreach($leadStatuses as $status)
                                    <option value="{{ $status->id }}" {{ $leadStatusId == $status->id ? 'selected' : '' }}>
                                        {{ $status->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Apply Filter</button>
                                <a href="{{ route('admin.reports.lead-aging') }}" class="btn btn-secondary">Reset</a>
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
                        <a href="{{ route('admin.reports.lead-aging.export.excel', request()->query()) }}" class="btn btn-success">
                            <i class="ti ti-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('admin.reports.lead-aging.export.pdf', request()->query()) }}" class="btn btn-danger">
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
                            <h4 class="mb-0">{{ $agingData['summary']['total_leads'] }}</h4>
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
                                    <i class="ti ti-clock text-info"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Avg Days in Status</p>
                            <h4 class="mb-0">{{ $agingData['summary']['avg_days_in_status'] }}</h4>
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
                                    <i class="ti ti-alert-triangle text-warning"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Over 7 Days</p>
                            <h4 class="mb-0">{{ $agingData['summary']['leads_over_7_days'] }}</h4>
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
                                    <i class="ti ti-alert-circle text-danger"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted mb-0">Over 14 Days</p>
                            <h4 class="mb-0">{{ $agingData['summary']['leads_over_14_days'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aging Analysis -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aging Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $agingData['summary']['over_7_days_percentage'] }}%</h3>
                                <p class="text-muted mb-0">Leads Over 7 Days</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-danger">{{ $agingData['summary']['over_14_days_percentage'] }}%</h3>
                                <p class="text-muted mb-0">Leads Over 14 Days</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-info">{{ $agingData['summary']['max_days_in_status'] }}</h3>
                                <p class="text-muted mb-0">Max Days in Status</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Non-Converted Leads Aging Report Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Non-Converted Leads Aging Report</h5>
                    <p class="text-muted mb-0">Showing data from {{ date('d M Y', strtotime($fromDate)) }} to {{ date('d M Y', strtotime($toDate)) }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="agingTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Lead Name</th>
                                    <th>Phone</th>
                                    <th>Source</th>
                                    <th>Telecaller</th>
                                    <th>Current Status</th>
                                    <th>Created Date</th>
                                    <th>Days</th>
                                    <th>Last Activity</th>
                                    <th>Aging Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $serialNumber = 1; @endphp
                                @forelse($agingData['status_groups'] as $statusGroup)
                                    @foreach($statusGroup['leads'] as $lead)
                                    <tr class="{{ $lead->days_in_current_status > 14 ? 'table-danger' : ($lead->days_in_current_status > 7 ? 'table-warning' : '') }}">
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
                                                {{ $lead->status_name }}
                                            </span>
                                        </td>
                                        <td>{{ $lead->created_at->format('d-m-Y h:i A') }}</td>
                                        <td>
                                            <span class="badge {{ $lead->days_in_current_status > 14 ? 'bg-danger' : ($lead->days_in_current_status > 7 ? 'bg-warning' : 'bg-info') }}">
                                                {{ $lead->days_in_current_status }} days
                                            </span>
                                        </td>
                                        <td>{{ $lead->last_activity_date->format('d-m-Y h:i A') }}</td>
                                        <td>
                                            @if($lead->aging_category == 'Fresh (0-1 days)')
                                                <span class="badge bg-success">Fresh</span>
                                            @elseif($lead->aging_category == 'Recent (2-3 days)')
                                                <span class="badge bg-info">Recent</span>
                                            @elseif($lead->aging_category == 'Moderate (4-7 days)')
                                                <span class="badge bg-warning">Moderate</span>
                                            @elseif($lead->aging_category == 'Old (8-14 days)')
                                                <span class="badge bg-orange">Old</span>
                                            @else
                                                <span class="badge bg-danger">Very Old</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No non-converted leads found for the selected date range.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Converted Leads Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Converted Leads Report</h5>
                    <p class="text-muted mb-0">Showing converted leads from {{ date('d M Y', strtotime($fromDate)) }} to {{ date('d M Y', strtotime($toDate)) }}</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="convertedTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Lead Name</th>
                                    <th>Phone</th>
                                    <th>Source</th>
                                    <th>Telecaller</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Conversion Date</th>
                                    <th>Days</th>
                                    <th>Days to Conversion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $convertedSerialNumber = 1; @endphp
                                @forelse($agingData['converted_leads'] as $lead)
                                <tr class="table-success">
                                    <td>{{ $convertedSerialNumber++ }}</td>
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
                                            {{ $lead->status_name }}
                                        </span>
                                    </td>
                                    <td>{{ $lead->created_at->format('d-m-Y h:i A') }}</td>
                                    <td>{{ $lead->conversion_date->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $lead->days_since_creation }} days</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $lead->days_to_conversion }} days</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No converted leads found for the selected date range.</td>
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

@push('styles')
<style>
.bg-orange {
    background-color: #fd7e14 !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Non-Converted Leads DataTable
    if (!$.fn.DataTable.isDataTable('#agingTable')) {
        $('#agingTable').DataTable({
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[7, "desc"]], // Sort by Days descending
            "columnDefs": [
                {
                    "targets": [7], // Days column
                    "type": "num"
                }
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'excel', 'pdf', 'print'
            ],
            "responsive": true,
            "language": {
                "emptyTable": "No non-converted leads found for the selected date range.",
                "zeroRecords": "No matching records found"
            }
        });
    }
    
    // Initialize Converted Leads DataTable
    if (!$.fn.DataTable.isDataTable('#convertedTable')) {
        $('#convertedTable').DataTable({
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[9, "desc"]], // Sort by Days to Conversion descending
            "columnDefs": [
                {
                    "targets": [8, 9], // Days and Days to Conversion columns
                    "type": "num"
                }
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'excel', 'pdf', 'print'
            ],
            "responsive": true,
            "language": {
                "emptyTable": "No converted leads found for the selected date range.",
                "zeroRecords": "No matching records found"
            }
        });
    }
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
