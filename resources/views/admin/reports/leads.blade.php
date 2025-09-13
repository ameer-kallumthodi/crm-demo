@extends('layouts.mantis')

@section('title', 'Lead Reports')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Lead Reports</h5>
                </div>
                <ul class="breadcrumb">
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
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ $fromDate }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ $toDate }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Generate Report
                                </button>
                                <a href="{{ route('admin.reports.leads') }}" class="btn btn-outline-secondary">
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

<!-- [ Reports Summary ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Report Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-primary">{{ $reports['lead_status']->sum('count') }}</h3>
                            <p class="text-muted mb-0">Total Leads</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-success">{{ $reports['lead_status']->where('title', 'Converted')->first()->count ?? 0 }}</h3>
                            <p class="text-muted mb-0">Converted</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-info">{{ $reports['lead_source']->count() }}</h3>
                            <p class="text-muted mb-0">Lead Sources</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-warning">{{ $reports['team']->count() }}</h3>
                            <p class="text-muted mb-0">Active Teams</p>
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
                        <table class="table table-hover">
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
                                            <span class="badge" style="background-color: {{ $status->color }}; color: white;">
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
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-tag me-2"></i>Lead Source Report
                </h5>
                <a href="{{ route('admin.reports.lead-source', ['date_from' => $fromDate, 'date_to' => $toDate]) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-eye"></i> Detailed Report
                </a>
            </div>
            <div class="card-body">
                @if($reports['lead_source']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
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
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-users me-2"></i>Team Report
                </h5>
                <a href="{{ route('admin.reports.team', ['date_from' => $fromDate, 'date_to' => $toDate]) }}" 
                   class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-eye"></i> Detailed Report
                </a>
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
<!-- [ Reports Content ] end -->
@endsection

@push('scripts')
<script>
function exportReport() {
    // Add export functionality here
    alert('Export functionality will be implemented');
}

function printReport() {
    window.print();
}
</script>
@endpush
