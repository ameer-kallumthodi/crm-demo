@extends('layouts.mantis')

@section('title', 'Team-Wise Detailed Report')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Team-Wise Detailed Report</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Advanced Reports</li>
                        <li class="breadcrumb-item">Team-Wise Report</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Printable Report Content ] start -->
<div class="printable-report">
    <div class="header text-center mb-4" style="display: none;">
        <h1>Team-Wise Detailed Report</h1>
        <p>Report Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</p>
        <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
    </div>
</div>
<!-- [ Printable Report Content ] end -->

<!-- [ Filter Form ] start -->
<div class="row mb-3 no-print">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-filter"></i> Report Filters
                </h5>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form method="GET" action="{{ route('admin.reports.team-wise') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input type="date" class="form-control" id="from_date" name="from_date" 
                                       value="{{ $fromDate }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="to_date">To Date</label>
                                <input type="date" class="form-control" id="to_date" name="to_date" 
                                       value="{{ $toDate }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="team_id">Team</label>
                                <select class="form-control" id="team_id" name="team_id">
                                    <option value="">All Teams</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ $teamId == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-search"></i> Generate Report
                            </button>
                            <a href="{{ route('admin.reports.team-wise.export', request()->query()) }}" 
                               class="btn btn-success">
                                <i class="ti ti-file-excel"></i> Export Excel
                            </a>
                            <a href="{{ route('admin.reports.team-wise.export-pdf', request()->query()) }}" 
                               class="btn btn-danger">
                                <i class="ti ti-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('admin.reports.team-wise') }}" 
                               class="btn btn-secondary">
                                <i class="ti ti-refresh"></i> Reset Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Form ] end -->

<!-- [ Report Data ] start -->
<div class="row">
    <div class="col-12">

                    <!-- Report Data -->
                    @if(!empty($reportData))
                        @foreach($reportData as $teamData)
                            <div class="card mb-4">
                                <div class="card-header bg-gradient-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">
                                                <i class="ti ti-users me-2"></i>{{ $teamData['team']->name }}
                                            </h4>
                                            <small class="opacity-75">
                                                <i class="ti ti-user-check me-1"></i>Team Lead: {{ $teamData['team']->teamLead ? $teamData['team']->teamLead->name : 'N/A' }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="d-flex flex-column align-items-end gap-2">
                                                <div class="badge bg-light text-dark fs-6">
                                                    {{ $teamData['team']->active_members }} Active Members
                                                </div>
                                                <a href="{{ route('admin.reports.team-wise.detail', ['team_id' => $teamData['team']->id, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" 
                                                   class="btn btn-sm btn-light text-primary border-primary">
                                                    <i class="ti ti-eye me-1"></i>View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Team Summary -->
                                    <div class="row mb-4">
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info">
                                                    <i class="ti ti-users"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Members</span>
                                                    <span class="info-box-number">{{ $teamData['team']->total_members }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success">
                                                    <i class="ti ti-user-check"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Active Members</span>
                                                    <span class="info-box-number">{{ $teamData['team']->active_members }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning">
                                                    <i class="ti ti-phone"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Total Leads</span>
                                                    <span class="info-box-number">{{ $teamData['total_leads'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-danger">
                                                    <i class="ti ti-percentage"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Conversion Rate</span>
                                                    <span class="info-box-number">{{ $teamData['conversion_rate'] }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Segment Analysis -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-gradient-success text-white">
                                                    <h5 class="mb-0">
                                                        <i class="ti ti-trophy me-2"></i>Top Performers vs Underperformers
                                                        <small class="d-block opacity-75 mt-1">
                                                            <i class="ti ti-users me-1"></i>{{ $teamData['team']->name }} - {{ $teamData['team']->teamLead ? $teamData['team']->teamLead->name : 'N/A' }}
                                                        </small>
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="text-center p-3 bg-light-success rounded">
                                                                <div class="mb-2">
                                                                    <i class="ti ti-crown text-success" style="font-size: 2rem;"></i>
                                                                </div>
                                                                @if(count($teamData['segments']['top_performers']) > 0)
                                                                    <h4 class="text-success mb-1">{{ count($teamData['segments']['top_performers']) }}</h4>
                                                                    <h6 class="text-success mb-2">Top Performers</h6>
                                                                    <small class="text-muted">
                                                                        Avg: {{ round(collect($teamData['segments']['top_performers'])->avg('conversion_rate'), 2) }}%
                                                                    </small>
                                                                    <div class="mt-2">
                                                                        <small class="text-success fw-bold">🏆 Top Performer:</small><br>
                                                                        <small class="text-dark">{{ $teamData['segments']['top_performers'][0]['user']->name }}</small><br>
                                                                        <small class="text-success">{{ $teamData['segments']['top_performers'][0]['conversion_rate'] }}% conversion</small>
                                                                    </div>
                                                                @else
                                                                    <h4 class="text-muted mb-1">-</h4>
                                                                    <h6 class="text-muted mb-2">Top Performers</h6>
                                                                    <small class="text-muted">No active performers</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="text-center p-3 bg-light-danger rounded">
                                                                <div class="mb-2">
                                                                    <i class="ti ti-trending-down text-danger" style="font-size: 2rem;"></i>
                                                                </div>
                                                                @if(count($teamData['segments']['underperformers']) > 0)
                                                                    <h4 class="text-danger mb-1">{{ count($teamData['segments']['underperformers']) }}</h4>
                                                                    <h6 class="text-danger mb-2">Underperformers</h6>
                                                                    <small class="text-muted">
                                                                        Avg: {{ round(collect($teamData['segments']['underperformers'])->avg('conversion_rate'), 2) }}%
                                                                    </small>
                                                                    <div class="mt-2">
                                                                        <small class="text-danger fw-bold">📉 Needs Support:</small><br>
                                                                        <small class="text-dark">{{ $teamData['segments']['underperformers'][0]['user']->name }}</small><br>
                                                                        <small class="text-danger">{{ $teamData['segments']['underperformers'][0]['conversion_rate'] }}% conversion</small>
                                                                    </div>
                                                                @else
                                                                    <h4 class="text-muted mb-1">-</h4>
                                                                    <h6 class="text-muted mb-2">Underperformers</h6>
                                                                    <small class="text-muted">No underperformers</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-gradient-info text-white">
                                                    <h5 class="mb-0">
                                                        <i class="ti ti-user-plus me-2"></i>New Joiners vs Experienced
                                                        <small class="d-block opacity-75 mt-1">
                                                            <i class="ti ti-users me-1"></i>{{ $teamData['team']->name }} - {{ $teamData['team']->teamLead ? $teamData['team']->teamLead->name : 'N/A' }}
                                                        </small>
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="text-center p-3 bg-light-warning rounded">
                                                                <div class="mb-2">
                                                                    <i class="ti ti-user-plus text-warning" style="font-size: 2rem;"></i>
                                                                </div>
                                                                @if(count($teamData['segments']['new_joiners']) > 0)
                                                                    <h4 class="text-warning mb-1">{{ count($teamData['segments']['new_joiners']) }}</h4>
                                                                    <h6 class="text-warning mb-2">New Joiners</h6>
                                                                    <small class="text-muted">
                                                                        Avg: {{ round(collect($teamData['segments']['new_joiners'])->avg('conversion_rate'), 2) }}%
                                                                    </small>
                                                                    <div class="mt-2">
                                                                        <small class="text-warning fw-bold">🌟 Best New Joiner:</small><br>
                                                                        <small class="text-dark">{{ collect($teamData['segments']['new_joiners'])->sortByDesc('conversion_rate')->first()['user']->name }}</small><br>
                                                                        <small class="text-warning">{{ collect($teamData['segments']['new_joiners'])->sortByDesc('conversion_rate')->first()['conversion_rate'] }}% conversion</small>
                                                                    </div>
                                                                @else
                                                                    <h4 class="text-muted mb-1">-</h4>
                                                                    <h6 class="text-muted mb-2">New Joiners</h6>
                                                                    <small class="text-muted">No active new joiners</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="text-center p-3 bg-light-info rounded">
                                                                <div class="mb-2">
                                                                    <i class="ti ti-user-check text-info" style="font-size: 2rem;"></i>
                                                                </div>
                                                                @if(count($teamData['segments']['experienced']) > 0)
                                                                    <h4 class="text-info mb-1">{{ count($teamData['segments']['experienced']) }}</h4>
                                                                    <h6 class="text-info mb-2">Experienced</h6>
                                                                    <small class="text-muted">
                                                                        Avg: {{ round(collect($teamData['segments']['experienced'])->avg('conversion_rate'), 2) }}%
                                                                    </small>
                                                                    <div class="mt-2">
                                                                        <small class="text-info fw-bold">⭐ Top Experienced:</small><br>
                                                                        <small class="text-dark">{{ collect($teamData['segments']['experienced'])->sortByDesc('conversion_rate')->first()['user']->name }}</small><br>
                                                                        <small class="text-info">{{ collect($teamData['segments']['experienced'])->sortByDesc('conversion_rate')->first()['conversion_rate'] }}% conversion</small>
                                                                    </div>
                                                                @else
                                                                    <h4 class="text-muted mb-1">-</h4>
                                                                    <h6 class="text-muted mb-2">Experienced</h6>
                                                                    <small class="text-muted">No active experienced members</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Time-Based Analysis -->
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-gradient-warning text-white">
                                                    <h5 class="mb-0"><i class="ti ti-clock"></i> Time Slot Performance Analysis</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="text-center p-4 bg-light-warning rounded">
                                                                <div class="mb-3">
                                                                    <i class="ti ti-sun text-warning" style="font-size: 3rem;"></i>
                                                                </div>
                                                                <h4 class="text-warning mb-2">Morning Shift</h4>
                                                                <p class="text-muted mb-3">6 AM - 12 PM</p>
                                                                <div class="row text-center">
                                                                    <div class="col-4">
                                                                        <div class="border-end">
                                                                            <h5 class="text-warning mb-1">{{ $teamData['time_analysis']['morning']['leads'] ?? 0 }}</h5>
                                                                            <small class="text-muted">Leads</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <div class="border-end">
                                                                            <h5 class="text-success mb-1">{{ $teamData['time_analysis']['morning']['conversions'] ?? 0 }}</h5>
                                                                            <small class="text-muted">Conversions</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <h5 class="text-info mb-1">{{ $teamData['time_analysis']['morning']['conversion_rate'] ?? 0 }}%</h5>
                                                                        <small class="text-muted">Rate</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="text-center p-4 bg-light-info rounded">
                                                                <div class="mb-3">
                                                                    <i class="ti ti-moon text-info" style="font-size: 3rem;"></i>
                                                                </div>
                                                                <h4 class="text-info mb-2">Evening Shift</h4>
                                                                <p class="text-muted mb-3">6 PM - 11 PM</p>
                                                                <div class="row text-center">
                                                                    <div class="col-4">
                                                                        <div class="border-end">
                                                                            <h5 class="text-info mb-1">{{ $teamData['time_analysis']['evening']['leads'] ?? 0 }}</h5>
                                                                            <small class="text-muted">Leads</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <div class="border-end">
                                                                            <h5 class="text-success mb-1">{{ $teamData['time_analysis']['evening']['conversions'] ?? 0 }}</h5>
                                                                            <small class="text-muted">Conversions</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <h5 class="text-info mb-1">{{ $teamData['time_analysis']['evening']['conversion_rate'] ?? 0 }}%</h5>
                                                                        <small class="text-muted">Rate</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Individual Telecaller Performance -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-gradient-primary text-white">
                                                    <h5 class="mb-0">
                                                        <i class="ti ti-user me-2"></i>Individual Telecaller Performance
                                                        <small class="d-block opacity-75 mt-1">
                                                            <i class="ti ti-users me-1"></i>{{ $teamData['team']->name }} - {{ $teamData['team']->teamLead ? $teamData['team']->teamLead->name : 'N/A' }}
                                                        </small>
                                                    </h5>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="border-0">
                                                                        <i class="ti ti-user me-1"></i>Name
                                                                    </th>
                                                                    <th class="border-0">
                                                                        <i class="ti ti-award me-1"></i>Experience
                                                                    </th>
                                                                    <th class="border-0 text-center">
                                                                        <i class="ti ti-phone me-1"></i>Total Leads
                                                                    </th>
                                                                    <th class="border-0 text-center">
                                                                        <i class="ti ti-check me-1"></i>Converted
                                                                    </th>
                                                                    <th class="border-0 text-center">
                                                                        <i class="ti ti-percentage me-1"></i>Conversion Rate
                                                                    </th>
                                                                    <th class="border-0 text-center">
                                                                        <i class="ti ti-phone-call me-1"></i>Total Calls
                                                                    </th>
                                                                    <th class="border-0 text-center">
                                                                        <i class="ti ti-clock me-1"></i>Avg Duration
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($teamData['telecaller_performance'] as $index => $perf)
                                                                    <tr class="{{ $index % 2 == 0 ? 'table-light' : '' }}">
                                                                        <td class="border-0">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="min-width: 40px; min-height: 40px;">
                                                                                    <span class="text-white fw-bold fs-6">{{ substr($perf['user']->name, 0, 1) }}</span>
                                                                                </div>
                                                                                <div class="flex-grow-1">
                                                                                    <h6 class="mb-1 fw-bold">{{ $perf['user']->name }}</h6>
                                                                                    <div class="d-flex flex-column">
                                                                                        @if($perf['user']->joining_date)
                                                                                            <small class="text-muted d-flex align-items-center mb-1">
                                                                                                <i class="ti ti-calendar me-1"></i>Joined: {{ \Carbon\Carbon::parse($perf['user']->joining_date)->format('M d, Y') }}
                                                                                            </small>
                                                                                        @endif
                                                                                        @if($perf['experience_days'] < 30)
                                                                                            <small class="text-warning d-flex align-items-center">
                                                                                                <i class="ti ti-user-plus me-1"></i>New Team Member ({{ round($perf['experience_days']) }} days)
                                                                                            </small>
                                                                                        @elseif($perf['experience_days'] < 180)
                                                                                            <small class="text-info d-flex align-items-center">
                                                                                                <i class="ti ti-trending-up me-1"></i>Growing ({{ round($perf['experience_days']) }} days)
                                                                                            </small>
                                                                                        @else
                                                                                            <small class="text-success d-flex align-items-center">
                                                                                                <i class="ti ti-star me-1"></i>Veteran ({{ round($perf['experience_days']) }} days)
                                                                                            </small>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="border-0">
                                                                            @if($perf['experience_level'] == 'New Joiner')
                                                                                <span class="badge bg-warning text-dark">
                                                                                    <i class="ti ti-user-plus me-1"></i>{{ $perf['experience_level'] }}
                                                                                </span>
                                                                            @elseif($perf['experience_level'] == 'Intermediate')
                                                                                <span class="badge bg-info">
                                                                                    <i class="ti ti-user me-1"></i>{{ $perf['experience_level'] }}
                                                                                </span>
                                                                            @else
                                                                                <span class="badge bg-success">
                                                                                    <i class="ti ti-user-check me-1"></i>{{ $perf['experience_level'] }}
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            <span class="fw-bold text-primary">{{ $perf['total_leads'] }}</span>
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            <span class="fw-bold text-success">{{ $perf['converted_leads'] }}</span>
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            @if($perf['conversion_rate'] >= 20)
                                                                                <span class="badge bg-success">
                                                                                    <i class="ti ti-trending-up me-1"></i>{{ $perf['conversion_rate'] }}%
                                                                                </span>
                                                                            @elseif($perf['conversion_rate'] >= 10)
                                                                                <span class="badge bg-warning text-dark">
                                                                                    <i class="ti ti-minus me-1"></i>{{ $perf['conversion_rate'] }}%
                                                                                </span>
                                                                            @else
                                                                                <span class="badge bg-danger">
                                                                                    <i class="ti ti-trending-down me-1"></i>{{ $perf['conversion_rate'] }}%
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            <span class="fw-bold text-info">{{ $perf['total_calls'] }}</span>
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            <span class="fw-bold text-secondary">{{ $perf['avg_call_duration'] }} min</span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Product/Region Analysis -->
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-gradient-success text-white">
                                                    <h5 class="mb-0"><i class="ti ti-school"></i> Product Performance</h5>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="border-0"><i class="ti ti-book me-1"></i>Course</th>
                                                                    <th class="border-0 text-center"><i class="ti ti-phone me-1"></i>Leads</th>
                                                                    <th class="border-0 text-center"><i class="ti ti-check me-1"></i>Conversions</th>
                                                                    <th class="border-0 text-center"><i class="ti ti-percentage me-1"></i>Rate</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($teamData['product_region_analysis']['products'] as $index => $product)
                                                                    <tr class="{{ $index % 2 == 0 ? 'table-light' : '' }}">
                                                                        <td class="border-0">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="bg-success rounded-circle p-2 me-2">
                                                                                    <i class="ti ti-book text-white"></i>
                                                                                </div>
                                                                                <span class="fw-bold">{{ $product->course_name }}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            <span class="badge bg-primary">{{ $product->total_leads ?? 0 }}</span>
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            <span class="badge bg-success">{{ $product->conversions ?? 0 }}</span>
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            @php
                                                                                $rate = ($product->total_leads ?? 0) > 0 ? round((($product->conversions ?? 0) / ($product->total_leads ?? 1)) * 100, 2) : 0;
                                                                            @endphp
                                                                            @if($rate >= 20)
                                                                                <span class="badge bg-success">{{ $rate }}%</span>
                                                                            @elseif($rate >= 10)
                                                                                <span class="badge bg-warning text-dark">{{ $rate }}%</span>
                                                                            @else
                                                                                <span class="badge bg-danger">{{ $rate }}%</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-header bg-gradient-info text-white">
                                                    <h5 class="mb-0"><i class="ti ti-world"></i> Region Performance</h5>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="border-0"><i class="ti ti-flag me-1"></i>Country</th>
                                                                    <th class="border-0 text-center"><i class="ti ti-phone me-1"></i>Leads</th>
                                                                    <th class="border-0 text-center"><i class="ti ti-check me-1"></i>Conversions</th>
                                                                    <th class="border-0 text-center"><i class="ti ti-percentage me-1"></i>Rate</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($teamData['product_region_analysis']['regions'] as $index => $region)
                                                                    <tr class="{{ $index % 2 == 0 ? 'table-light' : '' }}">
                                                                        <td class="border-0">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="bg-info rounded-circle p-2 me-2">
                                                                                    <i class="ti ti-flag text-white"></i>
                                                                                </div>
                                                                                <span class="fw-bold">{{ $region->country_name }}</span>
                                                                            </div>
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            <span class="badge bg-primary">{{ $region->total_leads ?? 0 }}</span>
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            <span class="badge bg-success">{{ $region->conversions ?? 0 }}</span>
                                                                        </td>
                                                                        <td class="border-0 text-center">
                                                                            @php
                                                                                $rate = ($region->total_leads ?? 0) > 0 ? round((($region->conversions ?? 0) / ($region->total_leads ?? 1)) * 100, 2) : 0;
                                                                            @endphp
                                                                            @if($rate >= 20)
                                                                                <span class="badge bg-success">{{ $rate }}%</span>
                                                                            @elseif($rate >= 10)
                                                                                <span class="badge bg-warning text-dark">{{ $rate }}%</span>
                                                                            @else
                                                                                <span class="badge bg-danger">{{ $rate }}%</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="card-footer bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">
                                                    <i class="ti ti-calendar me-1"></i>
                                                    Report Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}
                                                </small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.reports.team-wise.detail', ['team_id' => $teamData['team']->id, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="ti ti-eye me-1"></i>View Detailed Report
                                                </a>
                                                <a href="{{ route('admin.reports.team-wise.export', array_merge(request()->query(), ['team_id' => $teamData['team']->id])) }}" 
                                                   class="btn btn-success btn-sm">
                                                    <i class="ti ti-file-excel me-1"></i>Export Excel
                                                </a>
                                                <a href="{{ route('admin.reports.team-wise.export-pdf', array_merge(request()->query(), ['team_id' => $teamData['team']->id])) }}" 
                                                   class="btn btn-danger btn-sm">
                                                    <i class="ti ti-file-pdf me-1"></i>Export PDF
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i> No data available for the selected criteria.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Report Data ] end -->
@endsection

@section('styles')
<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
    }
    .bg-light-success {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-light-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .bg-light-warning {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-light-info {
        background-color: rgba(23, 162, 184, 0.1);
    }
    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 16px;
        flex-shrink: 0;
    }
    .avatar-sm span {
        line-height: 1;
    }
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .info-box {
        transition: all 0.3s ease;
    }
    .info-box:hover {
        transform: scale(1.05);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.5em 0.75em;
    }
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    .border-0 {
        border: 0 !important;
    }
    .performance-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    .performance-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .top-performer {
        border-left-color: #28a745;
    }
    .underperformer {
        border-left-color: #dc3545;
    }
    .new-joiner {
        border-left-color: #ffc107;
    }
    .experienced {
        border-left-color: #17a2b8;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize date picker
    $('#from_date, #to_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        
        if (fromDate && toDate && fromDate > toDate) {
            e.preventDefault();
            alert('From date cannot be greater than To date');
            return false;
        }
    });
    
    // Add animation to cards on load
    $('.card').each(function(index) {
        $(this).css('opacity', '0').delay(index * 100).animate({
            opacity: 1
        }, 500);
    });
    
    // Add hover effects to performance cards
    $('.performance-card').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );
});
</script>
@endsection
