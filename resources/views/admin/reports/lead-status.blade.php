@extends('layouts.mantis')

@section('title', 'Lead Status Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Lead Status Report</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reports.leads') }}">Reports</a></li>
                    <li class="breadcrumb-item">Lead Status</li>
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
                <form method="GET" action="{{ route('admin.reports.lead-status') }}" id="reportFilterForm">
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
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-filter"></i> Generate Report
                                </button>
                                <a href="{{ route('admin.reports.lead-status') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <div class="dropdown">
                                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-download"></i> Export
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.lead-status.excel', request()->query()) }}">
                                            <i class="ti ti-file-excel me-2"></i>Export to Excel
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.lead-status.pdf', request()->query()) }}">
                                            <i class="ti ti-file-pdf me-2"></i>Export to PDF
                                        </a></li>
                                    </ul>
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
<!-- [ Date Filter ] end -->

<!-- [ Report Summary ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lead Status Summary</h5>
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
                            <h3 class="text-info">{{ $reports['lead_status']->count() }}</h3>
                            <p class="text-muted mb-0">Status Types</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h3 class="text-warning">{{ $reports['conversion']['conversion_rate'] ?? 0 }}%</h3>
                            <p class="text-muted mb-0">Conversion Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Report Summary ] end -->

<!-- [ Lead Status Report ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-chart-pie me-2"></i>Lead Status Report
                </h5>
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
                                    <th class="text-end">Actions</th>
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
                                        <td class="text-end">
                                            <a href="{{ route('leads.index', ['lead_status_id' => $status->id, 'date_from' => $fromDate, 'date_to' => $toDate]) }}" 
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
                        <i class="ti ti-chart-pie f-48 mb-3"></i>
                        <p>No data available for the selected date range</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Lead Status Report ] end -->

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
function printReport() {
    window.print();
}
</script>
@endpush
