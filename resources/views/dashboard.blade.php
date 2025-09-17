@extends('layouts.mantis')

@section('title', 'Dashboard')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-md-6 col-lg-4 col-xl-2">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Leads</h6>
                <h4 class="mb-3">{{ $totalLeads ?? 0 }} <span class="badge bg-light-primary border border-primary"><i class="ti ti-trending-up"></i> {{ $weeklyStats['totalLeads'] ?? 0 }}</span></h4>
                <p class="mb-0 text-muted text-sm">This week: <span class="text-primary">{{ $weeklyStats['totalLeads'] ?? 0 }}</span> leads</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Active Leads</h6>
                <h4 class="mb-3">{{ $totalLeads - ($weeklyStats['convertedLeads'] ?? 0) }} <span class="badge bg-light-success border border-success"><i class="ti ti-trending-up"></i> Active</span></h4>
                <p class="mb-0 text-muted text-sm">Active leads: <span class="text-success">{{ $totalLeads - ($weeklyStats['convertedLeads'] ?? 0) }}</span></p>
            </div>
        </div>
    </div>  
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Converted</h6>
                <h4 class="mb-3">{{ $weeklyStats['convertedLeads'] ?? 0 }} <span class="badge bg-light-warning border border-warning"><i class="ti ti-trending-up"></i> {{ $conversionRate ?? 0 }}%</span></h4>
                <p class="mb-0 text-muted text-sm">Conversion rate: <span class="text-warning">{{ $conversionRate ?? 0 }}%</span></p>
            </div>
        </div>
    </div>
    @if(\App\Helpers\RoleHelper::is_super_admin())
    <div class="col-md-6 col-lg-4 col-xl-2">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Admins</h6>
                <h4 class="mb-3">{{ $totalAdmins ?? 0 }} <span class="badge bg-light-success border border-success"><i class="ti ti-shield-check"></i> Active</span></h4>
                <p class="mb-0 text-muted text-sm">Users with <span class="text-success">full access</span></p>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-6 col-lg-4 col-xl-2">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Telecallers</h6>
                <h4 class="mb-3">{{ $totalTelecallers ?? 0 }} <span class="badge bg-light-info border border-info"><i class="ti ti-phone"></i> Active</span></h4>
                <p class="mb-0 text-muted text-sm">Users with <span class="text-info">lead access</span></p>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-8">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">Lead Analytics</h5>
        </div>
        <div class="card">
            <div class="card-body">
                <div id="visitor-chart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <h5 class="mb-3">Lead Conversion</h5>
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">This Week Statistics</h6>
                <h3 class="mb-3">{{ $weeklyStats['convertedLeads'] ?? 0 }}</h3>
                <div id="income-overview-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-12">
        <h5 class="mb-3">Recent Leads</h5>
        <div class="card tbl-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>PHONE</th>
                                <th>STATUS</th>
                                <th>SOURCE</th>
                                <th class="text-end">CREATED</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeads ?? [] as $lead)
                            <tr>
                                <td><a href="#" class="text-muted">{{ $lead->title }}</a></td>
                                <td>{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</td>
                                <td>
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="fas fa-circle text-{{ \App\Helpers\StatusHelper::getLeadStatusColor($lead->leadStatus->id) }} f-10 m-r-5"></i>
                                        {{ $lead->leadStatus->title }}
                                    </span>
                                </td>
                                <td>{{ $lead->leadSource->title ?? 'N/A' }}</td>
                                <td class="text-end">{{ $lead->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No recent leads found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-8">
        <h5 class="mb-3">Lead Status Overview</h5>
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Lead Status Distribution</h6>
                <div class="row">
                    @forelse($leadStatuses ?? [] as $status)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-s rounded-circle text-{{ \App\Helpers\StatusHelper::getLeadStatusColor($status->id) }} bg-light-{{ \App\Helpers\StatusHelper::getLeadStatusColor($status->id) }}">
                                    <i class="ti ti-circle f-18"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $status->title }}</h6>
                                <p class="mb-0 text-muted">{{ $status->leads_count ?? 0 }} leads</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">
                        <p class="mb-0">No lead status data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <h5 class="mb-3">Lead Sources</h5>
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">This Week Statistics</h6>
                <h3 class="mb-0">{{ $totalLeads ?? 0 }}</h3>
                <div id="sales-report-chart"></div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <h5 class="mb-3">Recent Activity</h5>
        <div class="card">
        <div class="list-group list-group-flush">
            @forelse($recentActivities ?? [] as $activity)
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s rounded-circle text-{{ $activity['color'] }} bg-light-{{ $activity['color'] }}">
                            <i class="{{ $activity['icon'] }} f-18"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $activity['title'] }}</h6>
                        <p class="mb-0 text-muted">{{ $activity['description'] }}</p>
                        <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                    </div>
                    <div class="flex-shrink-0 text-end">
                        <h6 class="mb-1">+ 1</h6>
                        <p class="mb-0 text-muted">{{ $activity['time']->format('M d') }}</p>
                    </div>
                </div>
            </a>
            @empty
            <div class="list-group-item text-center text-muted">
                <p class="mb-0">No recent activities</p>
            </div>
            @endforelse
        </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <h5 class="mb-3">Analytics Report</h5>
        <div class="card">
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                    Lead Conversion Rate<span class="h5 mb-0">{{ $conversionRate ?? 0 }}%</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                    This Week Leads<span class="h5 mb-0">{{ $weeklyStats['totalLeads'] ?? 0 }}</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                    This Week Converted<span class="h5 mb-0">{{ $weeklyStats['convertedLeads'] ?? 0 }}</span>
                </a>
            </div>
            <div class="card-body px-2">
                <div id="analytics-report-chart"></div>
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
// Chart data from backend
const monthlyData = @json($monthlyLeads ?? []);
const leadSourcesData = @json($leadSourcesData ?? []);
const conversionRate = {{ $conversionRate ?? 0 }};

// Wait for ApexCharts to load
document.addEventListener('DOMContentLoaded', function() {
    // Check if ApexCharts is available
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts is not loaded');
        return;
    }
    
    initializeCharts();
});

function initializeCharts() {

// Monthly leads chart
console.log('Monthly Data:', monthlyData);
const chartContainer = document.querySelector("#visitor-chart");
console.log('Chart container:', chartContainer);

if (chartContainer) {
    // Always show the chart with months, even if no data
    const monthlyChart = new ApexCharts(chartContainer, {
        series: [{
            name: 'Total Leads',
            data: monthlyData.leadCounts || []
        }, {
            name: 'Converted Leads',
            data: monthlyData.convertedCounts || []
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        colors: ['#7366ff', '#f73164'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: monthlyData.months || ['Oct 2024', 'Nov 2024', 'Dec 2024', 'Jan 2025', 'Feb 2025', 'Mar 2025', 'Apr 2025', 'May 2025', 'Jun 2025', 'Jul 2025', 'Aug 2025', 'Sep 2025'],
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Number of Leads',
                style: {
                    fontSize: '12px'
                }
            },
            min: 0,
            forceNiceScale: true
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right'
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 5
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function (val) {
                    return val + " leads"
                }
            }
        },
        noData: {
            text: 'No data available',
            align: 'center',
            verticalAlign: 'middle',
            style: {
                color: '#999',
                fontSize: '14px'
            }
        }
    });
    monthlyChart.render();
} else {
    console.error('Chart container #visitor-chart not found');
}

// Lead sources pie chart
if (leadSourcesData.length > 0) {
    const leadSourcesChart = new ApexCharts(document.querySelector("#sales-report-chart"), {
        series: leadSourcesData.map(item => item.value),
        chart: {
            type: 'pie',
            height: 300,
            toolbar: {
                show: false
            }
        },
        labels: leadSourcesData.map(item => item.name),
        colors: ['#7366ff', '#f73164', '#51d28c', '#ffa726', '#ef5350', '#26a69a', '#ab47bc', '#ff7043'],
        legend: {
            position: 'bottom',
            fontSize: '12px',
            itemMargin: {
                horizontal: 10,
                vertical: 5
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                return opts.w.config.series[opts.seriesIndex] + " (" + val.toFixed(1) + "%)"
            },
            style: {
                fontSize: '11px',
                fontWeight: 'bold'
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " leads"
                }
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '60%'
                }
            }
        }
    });
    leadSourcesChart.render();
} else {
    // Show message if no data
    document.querySelector("#sales-report-chart").innerHTML = '<div class="text-center text-muted p-4"><i class="ti ti-chart-pie f-48 mb-3 text-primary"></i><h6 class="mb-2">No Lead Sources</h6><p class="mb-0">Add leads with sources to see distribution</p></div>';
}

// Conversion rate chart
const conversionChart = new ApexCharts(document.querySelector("#income-overview-chart"), {
    series: [conversionRate],
    chart: {
        type: 'radialBar',
        height: 200,
        toolbar: {
            show: false
        }
    },
    plotOptions: {
        radialBar: {
            startAngle: -90,
            endAngle: 90,
            hollow: {
                size: '60%'
            },
            dataLabels: {
                name: {
                    show: true,
                    fontSize: '12px',
                    fontWeight: 'bold',
                    color: '#666',
                    offsetY: -10
                },
                value: {
                    fontSize: '20px',
                    fontWeight: 'bold',
                    show: true,
                    color: '#333',
                    formatter: function (val) {
                        return val + '%';
                    }
                }
            }
        }
    },
    colors: ['#51d28c'],
    labels: ['Conversion Rate'],
    tooltip: {
        y: {
            formatter: function (val) {
                return val + '% conversion rate';
            }
        }
    }
});
conversionChart.render();

// Analytics report chart
const analyticsChart = new ApexCharts(document.querySelector("#analytics-report-chart"), {
    series: [{
        name: 'Conversion Rate',
        data: [conversionRate]
    }],
    chart: {
        type: 'bar',
        height: 200,
        toolbar: {
            show: false
        }
    },
    colors: ['#7366ff'],
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return val + '%';
        },
        style: {
            fontSize: '12px',
            fontWeight: 'bold',
            colors: ['#fff']
        }
    },
    xaxis: {
        categories: ['Conversion Rate'],
        labels: {
            style: {
                fontSize: '12px'
            }
        }
    },
    yaxis: {
        max: 100,
        min: 0,
        labels: {
            formatter: function (val) {
                return val + '%';
            },
            style: {
                fontSize: '11px'
            }
        }
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + '% conversion rate';
            }
        }
    },
    plotOptions: {
        bar: {
            borderRadius: 4,
            columnWidth: '60%'
        }
    }
});
analyticsChart.render();

} // End of initializeCharts function
</script>
@endpush
