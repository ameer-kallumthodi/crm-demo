@extends('layouts.mantis')

@section('title', 'Lead Activity Detail Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Lead Activity Detail Report</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.lead-aging') }}">Lead Idle Time</a></li>
                        <li class="breadcrumb-item active">Lead Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Lead Information Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Lead Information</h5>
                    <a href="{{ route('admin.reports.lead-aging') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Back to Lead Idle Time Report
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Lead Name:</strong></td>
                                    <td>{{ $lead->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $lead->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Source:</strong></td>
                                    <td>{{ $lead->leadSource->title ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Telecaller:</strong></td>
                                    <td>{{ $lead->telecaller->name ?? 'Unassigned' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Current Status:</strong></td>
                                    <td>
                                        @if($lead->is_converted)
                                            <span class="badge bg-success">Converted</span>
                                        @else
                                            <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->lead_status_id) }}">
                                                {{ $lead->leadStatus->title ?? 'Unknown' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created Date:</strong></td>
                                    <td>{{ $lead->created_at->format('d-m-Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Days:</strong></td>
                                    <td><span class="badge bg-info">{{ $totalDays }} days</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Average Days/Status:</strong></td>
                                    <td><span class="badge bg-secondary">{{ $averageDaysPerStatus }} days</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status History Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status History Timeline</h5>
                </div>
                <div class="card-body">
                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">{{ count($statusHistory) }}</h4>
                                <p class="text-muted mb-0">Total Status Changes</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-success mb-1">{{ $totalDays }}</h4>
                                <p class="text-muted mb-0">Total Days Active</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-info mb-1">{{ $averageDaysPerStatus }}</h4>
                                <p class="text-muted mb-0">Average Days per Status</p>
                            </div>
                        </div>
                    </div>
                    
                    @if(count($statusHistory) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped" id="statusHistoryTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Status</th>
                                        <th>Entry Date</th>
                                        <th>Exit Date</th>
                                        <th>Days in Status</th>
                                        <th>Description</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statusHistory as $index => $status)
                                    <tr class="{{ $status['exit_date'] === null ? 'table-success' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($status['status_name'] === 'Converted')
                                                <span class="badge bg-success">{{ $status['status_name'] }}</span>
                                            @else
                                                <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($status['status_id']) }}">
                                                    {{ $status['status_name'] }}
                                                </span>
                                            @endif
                                            @if($status['exit_date'] === null)
                                                <span class="badge bg-success ms-1">Current</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($status['entry_date'])->format('d-m-Y h:i A') }}</td>
                                        <td>
                                            @if($status['exit_date'])
                                                {{ \Carbon\Carbon::parse($status['exit_date'])->format('d-m-Y h:i A') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $status['days_in_status'] > 14 ? 'bg-danger' : ($status['days_in_status'] > 7 ? 'bg-warning' : 'bg-info') }}">
                                                {{ $status['days_in_status'] }} days
                                            </span>
                                        </td>
                                        <td>{{ $status['description'] ?? '-' }}</td>
                                        <td>{{ $status['remarks'] ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No status history found for this lead.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    if (!$.fn.DataTable.isDataTable('#statusHistoryTable')) {
        $('#statusHistoryTable').DataTable({
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[0, "asc"]], // Sort by sequence number
            "columnDefs": [
                {
                    "targets": [4], // Days in Status column
                    "type": "num"
                }
            ],
            "dom": 'Bfrtip',
            "buttons": [
                'excel', 'pdf', 'print'
            ],
            "responsive": true,
            "language": {
                "emptyTable": "No status history available"
            }
        });
    }
});
</script>
@endsection
