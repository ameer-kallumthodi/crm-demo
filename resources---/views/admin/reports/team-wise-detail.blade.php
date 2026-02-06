@extends('layouts.mantis')

@section('title', 'Team Detail Report - ' . $team->name)

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Team Detail Report - {{ $team->name }}</h5>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-3">
                    <ul class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.reports.team-wise') }}">Team Reports</a></li>
                        <li class="breadcrumb-item">Team Detail</li>
                    </ul>
                    <a href="{{ route('admin.reports.team-wise') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>Back to Teams
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Team Header ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">
                            <i class="ti ti-users me-2"></i>{{ $team->name }}
                        </h4>
                        <small class="opacity-75">
                            <i class="ti ti-user-check me-1"></i>Team Lead: {{ $team->teamLead ? $team->teamLead->name : 'N/A' }}
                        </small>
                    </div>
                    <div class="text-end">
                        <div class="d-flex flex-column align-items-end gap-2">
                            <div class="badge bg-light text-dark fs-6">
                                {{ $team->active_members }} Active Members
                            </div>
                            <div class="text-white">
                                <small>Report Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Team Header ] end -->

<!-- [ Team Summary Cards ] start -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="ti ti-phone text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="text-primary mb-1">{{ $teamData['total_leads'] }}</h3>
                <h6 class="text-muted mb-0">Total Leads</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="ti ti-check text-success" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="text-success mb-1">{{ $teamData['converted_leads'] }}</h3>
                <h6 class="text-muted mb-0">Converted Leads</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="ti ti-percentage text-info" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="text-info mb-1">{{ $teamData['conversion_rate'] }}%</h3>
                <h6 class="text-muted mb-0">Conversion Rate</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="ti ti-users text-warning" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="text-warning mb-1">{{ $team->active_members }}</h3>
                <h6 class="text-muted mb-0">Active Members</h6>
            </div>
        </div>
    </div>
</div>
<!-- [ Team Summary Cards ] end -->

<!-- [ Individual Performance ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="mb-0">
                    <i class="ti ti-user me-2"></i>Individual Team Member Performance
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">
                                    <i class="ti ti-user me-1"></i>Member Details
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
<!-- [ Individual Performance ] end -->

<!-- [ Daily Trends ] start -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-info text-white">
                <h5 class="mb-0">
                    <i class="ti ti-chart-line me-2"></i>Daily Performance Trends
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th><i class="ti ti-calendar me-1"></i>Date</th>
                                <th class="text-center"><i class="ti ti-phone me-1"></i>Total Leads</th>
                                <th class="text-center"><i class="ti ti-check me-1"></i>Conversions</th>
                                <th class="text-center"><i class="ti ti-percentage me-1"></i>Conversion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detailedMetrics['daily_trends'] as $trend)
                                <tr>
                                    <td class="fw-bold">{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $trend->total_leads }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $trend->conversions }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $rate = $trend->total_leads > 0 ? round(($trend->conversions / $trend->total_leads) * 100, 2) : 0;
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
<!-- [ Daily Trends ] end -->

<!-- [ Lead Source & Status Analysis ] start -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-warning text-white">
                <h5 class="mb-0">
                    <i class="ti ti-source me-2"></i>Lead Source Performance
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0"><i class="ti ti-tag me-1"></i>Source</th>
                                <th class="border-0 text-center"><i class="ti ti-phone me-1"></i>Leads</th>
                                <th class="border-0 text-center"><i class="ti ti-check me-1"></i>Conversions</th>
                                <th class="border-0 text-center"><i class="ti ti-percentage me-1"></i>Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detailedMetrics['lead_source_performance'] as $source)
                                <tr>
                                    <td class="border-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning rounded-circle p-2 me-2">
                                                <i class="ti ti-tag text-white"></i>
                                            </div>
                                            <span class="fw-bold">{{ $source->source_name }}</span>
                                        </div>
                                    </td>
                                    <td class="border-0 text-center">
                                        <span class="badge bg-primary">{{ $source->total_leads }}</span>
                                    </td>
                                    <td class="border-0 text-center">
                                        <span class="badge bg-success">{{ $source->conversions }}</span>
                                    </td>
                                    <td class="border-0 text-center">
                                        @php
                                            $rate = $source->total_leads > 0 ? round(($source->conversions / $source->total_leads) * 100, 2) : 0;
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
            <div class="card-header bg-gradient-danger text-white">
                <h5 class="mb-0">
                    <i class="ti ti-list me-2"></i>Lead Status Distribution
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0"><i class="ti ti-list me-1"></i>Status</th>
                                <th class="border-0 text-center"><i class="ti ti-hash me-1"></i>Count</th>
                                <th class="border-0 text-center"><i class="ti ti-percentage me-1"></i>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalLeads = $detailedMetrics['lead_status_distribution']->sum('count');
                            @endphp
                            @foreach($detailedMetrics['lead_status_distribution'] as $status)
                                <tr>
                                    <td class="border-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger rounded-circle p-2 me-2">
                                                <i class="ti ti-list text-white"></i>
                                            </div>
                                            <span class="fw-bold">{{ $status->status_name }}</span>
                                        </div>
                                    </td>
                                    <td class="border-0 text-center">
                                        <span class="badge bg-primary">{{ $status->count }}</span>
                                    </td>
                                    <td class="border-0 text-center">
                                        @php
                                            $percentage = $totalLeads > 0 ? round(($status->count / $totalLeads) * 100, 2) : 0;
                                        @endphp
                                        <span class="badge bg-info">{{ $percentage }}%</span>
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
<!-- [ Lead Source & Status Analysis ] end -->

<!-- [ Product & Region Performance ] start -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="mb-0">
                    <i class="ti ti-school me-2"></i>Product Performance
                </h5>
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
                            @foreach($teamData['product_region_analysis']['products'] as $product)
                                <tr>
                                    <td class="border-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle p-2 me-2">
                                                <i class="ti ti-book text-white"></i>
                                            </div>
                                            <span class="fw-bold">{{ $product->course_name }}</span>
                                        </div>
                                    </td>
                                    <td class="border-0 text-center">
                                        <span class="badge bg-primary">{{ $product->total_leads }}</span>
                                    </td>
                                    <td class="border-0 text-center">
                                        <span class="badge bg-success">{{ $product->conversions }}</span>
                                    </td>
                                    <td class="border-0 text-center">
                                        @php
                                            $rate = $product->total_leads > 0 ? round(($product->conversions / $product->total_leads) * 100, 2) : 0;
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
                <h5 class="mb-0">
                    <i class="ti ti-world me-2"></i>Region Performance
                </h5>
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
                            @foreach($teamData['product_region_analysis']['regions'] as $region)
                                <tr>
                                    <td class="border-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info rounded-circle p-2 me-2">
                                                <i class="ti ti-flag text-white"></i>
                                            </div>
                                            <span class="fw-bold">{{ $region->country_name }}</span>
                                        </div>
                                    </td>
                                    <td class="border-0 text-center">
                                        <span class="badge bg-primary">{{ $region->total_leads }}</span>
                                    </td>
                                    <td class="border-0 text-center">
                                        <span class="badge bg-success">{{ $region->conversions }}</span>
                                    </td>
                                    <td class="border-0 text-center">
                                        @php
                                            $rate = $region->total_leads > 0 ? round(($region->conversions / $region->total_leads) * 100, 2) : 0;
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
<!-- [ Product & Region Performance ] end -->

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
    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
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
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Add animation to cards on load
    $('.card').each(function(index) {
        $(this).css('opacity', '0').delay(index * 100).animate({
            opacity: 1
        }, 500);
    });
});
</script>
@endsection
