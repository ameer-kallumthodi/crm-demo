@extends('layouts.mantis')

@section('title', 'Dashboard')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard</h5>
                </div>
                <ul class="breadcrumb">
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
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Leads</h6>
                <h4 class="mb-3">{{ $totalLeads ?? 0 }} <span class="badge bg-light-primary border border-primary"><i class="ti ti-trending-up"></i> 59.3%</span></h4>
                <p class="mb-0 text-muted text-sm">You made an extra <span class="text-primary">35,000</span> this year</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Total Users</h6>
                <h4 class="mb-3">{{ $totalUsers ?? 0 }} <span class="badge bg-light-success border border-success"><i class="ti ti-trending-up"></i> 70.5%</span></h4>
                <p class="mb-0 text-muted text-sm">You made an extra <span class="text-success">8,900</span> this year</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Converted</h6>
                <h4 class="mb-3">{{ $leadStatuses->where('id', 4)->first()->count ?? 0 }} <span class="badge bg-light-warning border border-warning"><i class="ti ti-trending-down"></i> 27.4%</span></h4>
                <p class="mb-0 text-muted text-sm">You made an extra <span class="text-warning">1,943</span> this year</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">Active Leads</h6>
                <h4 class="mb-3">{{ $leadStatuses->where('id', '!=', 7)->sum('count') ?? 0 }} <span class="badge bg-light-danger border border-danger"><i class="ti ti-trending-down"></i> 27.4%</span></h4>
                <p class="mb-0 text-muted text-sm">You made an extra <span class="text-danger">20,395</span> this year</p>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-8">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0">Lead Status Overview</h5>
            <ul class="nav nav-pills justify-content-end mb-0" id="chart-tab-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="chart-tab-home-tab" data-bs-toggle="pill" data-bs-target="#chart-tab-home" type="button" role="tab" aria-controls="chart-tab-home" aria-selected="true">Month</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="chart-tab-profile-tab" data-bs-toggle="pill" data-bs-target="#chart-tab-profile" type="button" role="tab" aria-controls="chart-tab-profile" aria-selected="false">Week</button>
                </li>
            </ul>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="tab-content" id="chart-tab-tabContent">
                    <div class="tab-pane" id="chart-tab-home" role="tabpanel" aria-labelledby="chart-tab-home-tab" tabindex="0">
                        <div id="visitor-chart-1"></div>
                    </div>
                    <div class="tab-pane show active" id="chart-tab-profile" role="tabpanel" aria-labelledby="chart-tab-profile-tab" tabindex="0">
                        <div id="visitor-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-xl-4">
        <h5 class="mb-3">Lead Conversion</h5>
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2 f-w-400 text-muted">This Week Statistics</h6>
                <h3 class="mb-3">{{ $leadStatuses->where('id', 4)->first()->count ?? 0 }}</h3>
                <div id="income-overview-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-8">
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
                                <td>{{ $lead->phone }}</td>
                                <td>
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="fas fa-circle text-{{ $lead->leadStatus->id == 4 ? 'success' : ($lead->leadStatus->id == 7 ? 'danger' : 'warning') }} f-10 m-r-5"></i>
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
    <div class="col-md-12 col-xl-4">
        <h5 class="mb-3">Analytics Report</h5>
        <div class="card">
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                    Lead Conversion Rate<span class="h5 mb-0">+45.14%</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                    Response Time<span class="h5 mb-0">2.5 hrs</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between">
                    Follow-up Rate<span class="h5 mb-0">High</span>
                </a>
            </div>
            <div class="card-body px-2">
                <div id="analytics-report-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xl-8">
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
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s rounded-circle text-success bg-light-success">
                                <i class="ti ti-user-plus f-18"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">New Lead Added</h6>
                            <p class="mb-0 text-muted">Today, 2:00 AM</p>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h6 class="mb-1">+ 1</h6>
                            <p class="mb-0 text-muted">100%</p>
                        </div>
                    </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s rounded-circle text-primary bg-light-primary">
                                <i class="ti ti-check f-18"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Lead Converted</h6>
                            <p class="mb-0 text-muted">5 August, 1:45 PM</p>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h6 class="mb-1">+ 1</h6>
                            <p class="mb-0 text-muted">100%</p>
                        </div>
                    </div>
                </a>
                <a href="#" class="list-group-item list-group-item-action">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s rounded-circle text-warning bg-light-warning">
                                <i class="ti ti-clock f-18"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Follow-up Scheduled</h6>
                            <p class="mb-0 text-muted">7 hours ago</p>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h6 class="mb-1">+ 3</h6>
                            <p class="mb-0 text-muted">75%</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<script src="{{ asset('assets/mantis/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/mantis/js/pages/dashboard-default.js') }}"></script>
@endpush