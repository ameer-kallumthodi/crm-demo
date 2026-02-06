@extends('layouts.mantis')

@section('title', 'Auditor Dashboard')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Auditor Dashboard</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Auditor Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row g-3">
    <!-- Summary Statistics Cards -->
    <div class="col-6 col-md-3 col-lg-2">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Leads</h6>
                <h4 class="mb-0">{{ $summaryStats['totalLeads'] ?? 0 }}</h4>
                <small class="text-muted d-block mt-2">All time</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Telecallers</h6>
                <h4 class="mb-0">{{ $summaryStats['totalTelecallers'] ?? 0 }}</h4>
                <small class="text-muted d-block mt-2">Active</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Teams</h6>
                <h4 class="mb-0">{{ $summaryStats['totalTeams'] ?? 0 }}</h4>
                <small class="text-muted d-block mt-2">Total teams</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Converted</h6>
                <h4 class="mb-0">{{ $summaryStats['totalConvertedLeads'] ?? 0 }}</h4>
                <small class="text-muted d-block mt-2">Total</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Today's Leads</h6>
                <h4 class="mb-0">{{ $summaryStats['todaysLeads'] ?? 0 }}</h4>
                <small class="text-muted d-block mt-2">{{ now()->format('M d, Y') }}</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Follow-ups</h6>
                <h4 class="mb-0">{{ $summaryStats['followupLeads'] ?? 0 }}</h4>
                <small class="text-muted d-block mt-2">Pending</small>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="col-12 col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Monthly Leads & Conversions Trend</h5>
            </div>
            <div class="card-body">
                <div id="monthly-trend-chart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Lead Sources Distribution</h5>
            </div>
            <div class="card-body">
                <div id="lead-sources-chart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Weekly Performance (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <div id="weekly-chart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top Telecallers by Leads</h5>
            </div>
            <div class="card-body">
                <div id="top-telecallers-chart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Telecallers Detailed Report -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Telecallers Performance Report</h5>
                <span class="badge bg-primary">{{ $telecallerStats->count() }} Telecallers</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Total Leads</th>
                                <th>Converted</th>
                                <th>Follow-ups</th>
                                <th>Today's Leads</th>
                                <th>Conversion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($telecallerStats as $telecaller)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($telecaller['profile_picture'])
                                        <img src="{{ asset('storage/' . $telecaller['profile_picture']) }}" class="rounded-circle me-2" width="32" height="32" alt="{{ $telecaller['name'] }}">
                                        @else
                                        <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                                            <span class="f-12 fw-bold text-primary">{{ strtoupper(substr($telecaller['name'], 0, 1)) }}</span>
                                        </div>
                                        @endif
                                        <strong>{{ $telecaller['name'] }}</strong>
                                    </div>
                                </td>
                                <td>{{ $telecaller['total_leads'] }}</td>
                                <td><span class="badge bg-success">{{ $telecaller['converted_leads'] }}</span></td>
                                <td><span class="badge bg-warning">{{ $telecaller['followup_leads'] }}</span></td>
                                <td><span class="badge bg-info">{{ $telecaller['todays_leads'] }}</span></td>
                                <td>
                                    <span class="badge bg-primary">{{ $telecaller['conversion_rate'] }}%</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No telecaller data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Teams Detailed Report -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Teams Performance Report</h5>
                <span class="badge bg-primary">{{ $teamStats->count() }} Teams</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Team Name</th>
                                <th>Team Lead</th>
                                <th>Members</th>
                                <th>Total Leads</th>
                                <th>Converted</th>
                                <th>Follow-ups</th>
                                <th>Today's Leads</th>
                                <th>Conversion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teamStats as $team)
                            <tr>
                                <td><strong>{{ $team['name'] }}</strong></td>
                                <td>{{ $team['team_lead'] }}</td>
                                <td><span class="badge bg-secondary">{{ $team['total_members'] }}</span></td>
                                <td>{{ $team['total_leads'] }}</td>
                                <td><span class="badge bg-success">{{ $team['converted_leads'] }}</span></td>
                                <td><span class="badge bg-warning">{{ $team['followup_leads'] }}</span></td>
                                <td><span class="badge bg-info">{{ $team['todays_leads'] }}</span></td>
                                <td><span class="badge bg-primary">{{ $team['conversion_rate'] }}%</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No team data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads by Status and Source -->
    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Leads by Status</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leadStats['by_status'] as $status)
                            <tr>
                                <td>{{ $status['title'] }}</td>
                                <td class="text-end"><strong>{{ $status['count'] }}</strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">No status data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Leads by Source</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leadStats['by_source'] as $source)
                            <tr>
                                <td>{{ $source['title'] }}</td>
                                <td class="text-end"><strong>{{ $source['count'] }}</strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">No source data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Converted Leads Report -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Converted Leads Statistics</h5>
                <div>
                    <span class="badge bg-success me-2">Total: {{ $convertedLeadsStats['total'] }}</span>
                    <span class="badge bg-info me-2">Today: {{ $convertedLeadsStats['today'] }}</span>
                    <span class="badge bg-primary">This Week: {{ $convertedLeadsStats['this_week'] }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="mb-1">{{ $convertedLeadsStats['total'] }}</h4>
                            <small class="text-muted">Total Converted</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="mb-1">{{ $convertedLeadsStats['today'] }}</h4>
                            <small class="text-muted">Today</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="mb-1">{{ $convertedLeadsStats['this_week'] }}</h4>
                            <small class="text-muted">This Week</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="mb-1">{{ $convertedLeadsStats['this_month'] }}</h4>
                            <small class="text-muted">This Month</small>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Telecaller</th>
                                <th>Converted Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($convertedLeadsStats['recent'] as $converted)
                            <tr>
                                <td>{{ $converted->name }}</td>
                                <td>{{ $converted->course->title ?? 'N/A' }}</td>
                                <td>{{ $converted->lead->telecaller->name ?? 'N/A' }}</td>
                                <td>{{ $converted->created_at->format('d-m-Y h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No recent conversions</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Follow-up Leads Report -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Follow-up Leads Report</h5>
                <div>
                    <span class="badge bg-danger me-2">Overdue: {{ $followupStats['overdue'] }}</span>
                    <span class="badge bg-warning me-2">Upcoming: {{ $followupStats['upcoming'] }}</span>
                    <span class="badge bg-primary">Total: {{ $followupStats['total'] }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="mb-1">{{ $followupStats['total'] }}</h4>
                            <small class="text-muted">Total Follow-ups</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="mb-1">{{ $followupStats['today'] }}</h4>
                            <small class="text-muted">Today</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="mb-1 text-danger">{{ $followupStats['overdue'] }}</h4>
                            <small class="text-muted">Overdue</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="mb-1 text-warning">{{ $followupStats['upcoming'] }}</h4>
                            <small class="text-muted">Upcoming (7 days)</small>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Lead Name</th>
                                <th>Telecaller</th>
                                <th>Source</th>
                                <th>Follow-up Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($followupStats['recent'] as $lead)
                            <tr>
                                <td>{{ $lead->title }}</td>
                                <td>{{ $lead->telecaller->name ?? 'N/A' }}</td>
                                <td>{{ $lead->leadSource->title ?? 'N/A' }}</td>
                                <td>
                                    @if($lead->followup_date)
                                        {{ \Carbon\Carbon::parse($lead->followup_date)->format('d-m-Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($lead->followup_date && \Carbon\Carbon::parse($lead->followup_date)->isPast())
                                        <span class="badge bg-danger">Overdue</span>
                                    @elseif($lead->followup_date)
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">No Date</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No follow-up leads</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Leads -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Today's Leads ({{ now()->format('d-m-Y') }})</h5>
                <span class="badge bg-primary">{{ $todaysLeads->count() }} Leads</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Telecaller</th>
                                <th>Team</th>
                                <th>Status</th>
                                <th>Source</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todaysLeads as $lead)
                            <tr>
                                <td><strong>{{ $lead->title }}</strong></td>
                                <td>{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</td>
                                <td>{{ $lead->telecaller->name ?? 'N/A' }}</td>
                                <td>{{ $lead->team->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ \App\Helpers\StatusHelper::getLeadStatusColor($lead->leadStatus->id) }}">
                                        {{ $lead->leadStatus->title }}
                                    </span>
                                </td>
                                <td>{{ $lead->leadSource->title ?? 'N/A' }}</td>
                                <td>{{ $lead->created_at->format('h:i A') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No leads created today</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script>
(function() {
    'use strict';
    
    // Prevent multiple initializations
    if (window.auditorChartsInitialized) {
        return;
    }
    window.auditorChartsInitialized = true;
    
    // Store chart instances
    const chartInstances = {};
    
    function initializeCharts() {
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts is not loaded');
            return;
        }
        
        // Chart data from backend
        const chartsData = @json($chartsData ?? []);
        
        // Destroy existing charts before creating new ones
        Object.keys(chartInstances).forEach(chartId => {
            if (chartInstances[chartId]) {
                chartInstances[chartId].destroy();
                delete chartInstances[chartId];
            }
        });
        
        // Monthly Trend Chart
        const monthlyChartEl = document.querySelector("#monthly-trend-chart");
        if (monthlyChartEl && !chartInstances['monthly']) {
            chartInstances['monthly'] = new ApexCharts(monthlyChartEl, {
                series: [{
                    name: 'Total Leads',
                    data: chartsData.monthly?.map(item => item.leads) || []
                }, {
                    name: 'Converted Leads',
                    data: chartsData.monthly?.map(item => item.converted) || []
                }, {
                    name: 'Follow-ups',
                    data: chartsData.monthly?.map(item => item.followups) || []
                }],
                chart: {
                    type: 'line',
                    height: 350,
                    toolbar: { show: false },
                    id: 'monthly-trend-chart'
                },
                colors: ['#7366ff', '#f73164', '#51d28c'],
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: chartsData.monthly?.map(item => item.month) || []
                },
                yaxis: {
                    title: { text: 'Number of Leads' }
                },
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: { shared: true }
            });
            chartInstances['monthly'].render();
        }

        // Lead Sources Pie Chart
        const leadSourcesChartEl = document.querySelector("#lead-sources-chart");
        if (leadSourcesChartEl && chartsData.lead_sources?.length > 0 && !chartInstances['lead-sources']) {
            chartInstances['lead-sources'] = new ApexCharts(leadSourcesChartEl, {
                series: chartsData.lead_sources.map(item => item.value),
                chart: {
                    type: 'pie',
                    height: 300,
                    toolbar: { show: false },
                    id: 'lead-sources-chart'
                },
                labels: chartsData.lead_sources.map(item => item.name),
                colors: ['#7366ff', '#f73164', '#51d28c', '#ffa726', '#ef5350', '#26a69a', '#ab47bc', '#ff7043'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: true }
            });
            chartInstances['lead-sources'].render();
        }

        // Weekly Performance Chart
        const weeklyChartEl = document.querySelector("#weekly-chart");
        if (weeklyChartEl && !chartInstances['weekly']) {
            chartInstances['weekly'] = new ApexCharts(weeklyChartEl, {
                series: [{
                    name: 'Leads',
                    data: chartsData.weekly?.map(item => item.leads) || []
                }, {
                    name: 'Converted',
                    data: chartsData.weekly?.map(item => item.converted) || []
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false },
                    id: 'weekly-chart'
                },
                colors: ['#7366ff', '#51d28c'],
                xaxis: {
                    categories: chartsData.weekly?.map(item => item.day) || []
                },
                yaxis: {
                    title: { text: 'Number of Leads' }
                },
                legend: { position: 'top' }
            });
            chartInstances['weekly'].render();
        }

        // Top Telecallers Bar Chart
        const topTelecallersChartEl = document.querySelector("#top-telecallers-chart");
        if (topTelecallersChartEl && chartsData.top_telecallers?.length > 0 && !chartInstances['top-telecallers']) {
            chartInstances['top-telecallers'] = new ApexCharts(topTelecallersChartEl, {
                series: [{
                    name: 'Leads',
                    data: chartsData.top_telecallers.map(item => item.count)
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false },
                    horizontal: true,
                    id: 'top-telecallers-chart'
                },
                colors: ['#7366ff'],
                xaxis: {
                    categories: chartsData.top_telecallers.map(item => item.name),
                    title: { text: 'Number of Leads' }
                },
                yaxis: {
                    title: { text: 'Telecallers' }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        dataLabels: { position: 'top' }
                    }
                },
                dataLabels: { enabled: true }
            });
            chartInstances['top-telecallers'].render();
        }
    }
    
    // Initialize charts when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCharts);
    } else {
        initializeCharts();
    }
})();
</script>
@endpush

