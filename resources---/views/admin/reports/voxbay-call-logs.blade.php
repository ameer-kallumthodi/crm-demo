@extends('layouts.mantis')

@section('title', 'Voxbay Call Logs Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Voxbay Call Logs Report</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('reports.voxbay-call-logs') }}" class="form-inline">
                                <div class="form-group mr-2">
                                    <label for="date_from" class="mr-2">From Date:</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" 
                                           value="{{ $fromDate }}">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="date_to" class="mr-2">To Date:</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" 
                                           value="{{ $toDate }}">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="team_id" class="mr-2">Team:</label>
                                    <select name="team_id" id="team_id" class="form-control form-control-sm">
                                        <option value="">All Teams</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ $teamId == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <label for="telecaller_id" class="mr-2">Telecaller:</label>
                                    <select name="telecaller_id" id="telecaller_id" class="form-control form-control-sm">
                                        <option value="">All Telecallers</option>
                                        @foreach($telecallers as $telecaller)
                                            <option value="{{ $telecaller->id }}" {{ $telecallerId == $telecaller->id ? 'selected' : '' }}>
                                                {{ $telecaller->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mr-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('reports.voxbay-call-logs') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                                <div class="btn-group ml-2">
                                    <a href="{{ route('reports.voxbay-call-logs.export.excel', request()->query()) }}" 
                                       class="btn btn-success btn-sm">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </a>
                                    <a href="{{ route('reports.voxbay-call-logs.export.pdf', request()->query()) }}" 
                                       class="btn btn-danger btn-sm">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-phone"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Calls</span>
                                    <span class="info-box-number">{{ $reportData['stats']['total_calls'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-phone-volume"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Answered Calls</span>
                                    <span class="info-box-number">{{ $reportData['stats']['answered_calls'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Duration</span>
                                    <span class="info-box-number">{{ gmdate('H:i:s', $reportData['stats']['total_duration']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Answer Rate</span>
                                    <span class="info-box-number">
                                        {{ $reportData['stats']['total_calls'] > 0 ? 
                                            round(($reportData['stats']['answered_calls'] / $reportData['stats']['total_calls']) * 100, 2) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Call Type Breakdown -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Call Types</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-info">
                                                    {{ $reportData['stats']['total_calls'] > 0 ? 
                                                        round(($reportData['stats']['incoming_calls'] / $reportData['stats']['total_calls']) * 100, 1) : 0 }}%
                                                </span>
                                                <h5 class="description-header">{{ $reportData['stats']['incoming_calls'] }}</h5>
                                                <span class="description-text">INCOMING</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="description-block">
                                                <span class="description-percentage text-success">
                                                    {{ $reportData['stats']['total_calls'] > 0 ? 
                                                        round(($reportData['stats']['outgoing_calls'] / $reportData['stats']['total_calls']) * 100, 1) : 0 }}%
                                                </span>
                                                <h5 class="description-header">{{ $reportData['stats']['outgoing_calls'] }}</h5>
                                                <span class="description-text">OUTGOING</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Call Status</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-success">
                                                    {{ $reportData['stats']['total_calls'] > 0 ? 
                                                        round(($reportData['stats']['answered_calls'] / $reportData['stats']['total_calls']) * 100, 1) : 0 }}%
                                                </span>
                                                <h5 class="description-header">{{ $reportData['stats']['answered_calls'] }}</h5>
                                                <span class="description-text">ANSWERED</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="description-block">
                                                <span class="description-percentage text-danger">
                                                    {{ $reportData['stats']['total_calls'] > 0 ? 
                                                        round(($reportData['stats']['busy_calls'] / $reportData['stats']['total_calls']) * 100, 1) : 0 }}%
                                                </span>
                                                <h5 class="description-header">{{ $reportData['stats']['busy_calls'] }}</h5>
                                                <span class="description-text">BUSY</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Performance</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-primary">
                                                    {{ $reportData['stats']['total_calls'] > 0 ? 
                                                        round(($reportData['stats']['answered_calls'] / $reportData['stats']['total_calls']) * 100, 1) : 0 }}%
                                                </span>
                                                <h5 class="description-header">Answer Rate</h5>
                                                <span class="description-text">EFFICIENCY</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="description-block">
                                                <span class="description-percentage text-info">
                                                    {{ gmdate('H:i:s', $reportData['stats']['average_duration']) }}
                                                </span>
                                                <h5 class="description-header">Avg Duration</h5>
                                                <span class="description-text">PER CALL</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Telecaller Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Telecaller Performance</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Telecaller</th>
                                                    <th>Team</th>
                                                    <th>Total Calls</th>
                                                    <th>Incoming</th>
                                                    <th>Outgoing</th>
                                                    <th>Answered</th>
                                                    <th>Answer Rate</th>
                                                    <th>Total Duration</th>
                                                    <th>Avg Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($reportData['telecaller_stats'] as $stat)
                                                <tr>
                                                    <td>{{ $stat['telecaller_name'] }}</td>
                                                    <td>{{ $stat['team_name'] }}</td>
                                                    <td>{{ $stat['total_calls'] }}</td>
                                                    <td>{{ $stat['incoming_calls'] }}</td>
                                                    <td>{{ $stat['outgoing_calls'] }}</td>
                                                    <td>{{ $stat['answered_calls'] }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $stat['answer_rate'] >= 70 ? 'success' : ($stat['answer_rate'] >= 50 ? 'warning' : 'danger') }}">
                                                            {{ $stat['answer_rate'] }}%
                                                        </span>
                                                    </td>
                                                    <td>{{ gmdate('H:i:s', $stat['total_duration']) }}</td>
                                                    <td>{{ gmdate('H:i:s', $stat['avg_duration']) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">No data available</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Team Performance</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Team</th>
                                                    <th>Members</th>
                                                    <th>Total Calls</th>
                                                    <th>Incoming</th>
                                                    <th>Outgoing</th>
                                                    <th>Answered</th>
                                                    <th>Answer Rate</th>
                                                    <th>Total Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($reportData['team_stats'] as $stat)
                                                <tr>
                                                    <td>{{ $stat['team_name'] }}</td>
                                                    <td>{{ $stat['total_members'] }}</td>
                                                    <td>{{ $stat['total_calls'] }}</td>
                                                    <td>{{ $stat['incoming_calls'] }}</td>
                                                    <td>{{ $stat['outgoing_calls'] }}</td>
                                                    <td>{{ $stat['answered_calls'] }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $stat['answer_rate'] >= 70 ? 'success' : ($stat['answer_rate'] >= 50 ? 'warning' : 'danger') }}">
                                                            {{ $stat['answer_rate'] }}%
                                                        </span>
                                                    </td>
                                                    <td>{{ gmdate('H:i:s', $stat['total_duration']) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No data available</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Calls -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Calls</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Type</th>
                                                    <th>Telecaller</th>
                                                    <th>Destination</th>
                                                    <th>Status</th>
                                                    <th>Duration</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($reportData['recent_calls'] as $callLog)
                                                <tr>
                                                    <td>{{ $callLog->id }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $callLog->type == 'incoming' ? 'info' : ($callLog->type == 'outgoing' ? 'success' : 'warning') }}">
                                                            {{ ucfirst($callLog->type) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $callLog->telecaller_name }}</td>
                                                    <td>{{ $callLog->destinationNumber ?? 'N/A' }}</td>
                                                    <td>{!! $callLog->call_status_badge !!}</td>
                                                    <td>{{ $callLog->formatted_duration }}</td>
                                                    <td>{{ $callLog->date ? $callLog->date->format('Y-m-d') : 'N/A' }}</td>
                                                    <td>{{ $callLog->start_time ?? 'N/A' }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No recent calls found</td>
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
            </div>
        </div>
    </div>
</div>
@endsection
