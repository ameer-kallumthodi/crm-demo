@extends('layouts.mantis')

@section('title', 'Team Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Team Report</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                    <li class="breadcrumb-item">Team</li>
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

<!-- [ Date Filter ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.team') }}" id="reportFilterForm">
                    <div class="row align-items-end">
                        <div class="col-md-3">
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
                            <label for="team_id" class="form-label">Team</label>
                            <select class="form-select" id="team_id" name="team_id">
                                <option value="">All Teams</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ $teamId == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Generate Report
                                </button>
                                <a href="{{ route('admin.reports.team') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportReport()">
                                    <i class="ti ti-download"></i> Export
                                </button>
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
<!-- [ Date Filter ] end -->

<!-- [ Report Summary ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Team Performance Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-primary">{{ $reports['team']->sum('count') }}</h3>
                            <p class="text-muted mb-0">Total Leads</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-info">{{ $reports['team']->count() }}</h3>
                            <p class="text-muted mb-0">Active Teams</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-success">{{ $reports['team']->max('count') }}</h3>
                            <p class="text-muted mb-0">Highest Team</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-warning">{{ round($reports['team']->avg('count'), 1) }}</h3>
                            <p class="text-muted mb-0">Average per Team</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Report Summary ] end -->

<!-- [ Team Report ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>Team Performance Report
                </h5>
            </div>
            <div class="card-body">
                @if($reports['team']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Team</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                    <th class="text-end">Actions</th>
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
                                            <div>
                                                <i class="ti ti-users me-2 text-success"></i>
                                                <strong>{{ $team->title }}</strong>
                                                @if(isset($team->telecallers) && $team->telecallers->count() > 0)
                                                    <div class="mt-2">
                                                        <small class="text-muted">Telecallers:</small>
                                                        <div class="mt-1">
                                                            @foreach($team->telecallers as $telecaller)
                                                                <span class="badge bg-light-primary me-1 mb-1">
                                                                    <i class="ti ti-phone me-1"></i>
                                                                    {{ $telecaller->name }} ({{ $telecaller->lead_count }})
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="mt-2">
                                                        <small class="text-muted">No telecallers assigned</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold">{{ $team->count }}</td>
                                        <td class="text-end">{{ $percentage }}%</td>
                                        <td class="text-end">
                                            <a href="{{ route('leads.index', ['team_id' => $team->id, 'date_from' => $fromDate, 'date_to' => $toDate]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-eye"></i> View Leads
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
</div>
<!-- [ Team Report ] end -->

<!-- [ Monthly Trend ] start -->
<div class="row mt-4">
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
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Total Leads</th>
                                    <th class="text-end">Converted</th>
                                    <th class="text-end">Conversion Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports['monthly'] as $month)
                                    <tr>
                                        <td>{{ $month->month }}</td>
                                        <td class="text-end fw-bold">{{ $month->total_leads > 0 ? $month->total_leads : '-' }}</td>
                                        <td class="text-end text-success">{{ $month->converted > 0 ? $month->converted : '-' }}</td>
                                        <td class="text-end">{{ $month->total_leads > 0 ? $month->conversion_rate . '%' : '0%' }}</td>
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
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>Leads Data
                </h5>
            </div>
            <div class="card-body">
                @if($leads->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                    <th>Team</th>
                                    <th>Telecaller</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $lead->title }}</td>
                                        <td>{{ $lead->phone }}</td>
                                        <td>{{ $lead->email ?? '-' }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $lead->leadStatus->color ?? '#6c757d' }}; color: white;">
                                                {{ $lead->leadStatus->title ?? 'Unknown' }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="ti ti-tag me-1"></i>
                                            {{ $lead->leadSource->title ?? 'Unknown' }}
                                        </td>
                                        <td>
                                            <i class="ti ti-users me-1"></i>
                                            {{ $lead->team->name ?? 'No Team' }}
                                        </td>
                                        <td>{{ $lead->telecaller->name ?? '-' }}</td>
                                        <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $leads->links() }}
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ti ti-users f-48 mb-3"></i>
                        <p>No leads found for the selected date range</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Leads Data ] end -->
@endsection

@push('scripts')
<script>
function exportReport() {
    // Export functionality
    window.print();
}

function printReport() {
    window.print();
}
</script>
@endpush
