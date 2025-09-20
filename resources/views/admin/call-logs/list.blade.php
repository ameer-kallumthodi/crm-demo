@extends('layouts.mantis')

@section('title', 'Call Logs - ' . $lead->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Call Logs for Lead: {{ $lead->title }}
                        <small class="text-muted">({{ $lead->code }}{{ $lead->phone }})</small>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('leads.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Leads
                        </a>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Lead Information -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Lead Information</h5>
                                <strong>Name:</strong> {{ $lead->title }}<br>
                                <strong>Phone:</strong> {{ $lead->code }}{{ $lead->phone }}<br>
                                <strong>Email:</strong> {{ $lead->email ?? 'N/A' }}<br>
                                <strong>Status:</strong> {{ $lead->leadStatus->title ?? 'N/A' }}<br>
                                <strong>Source:</strong> {{ $lead->leadSource->title ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('leads.call-logs', $lead) }}" class="form-inline">
                                <div class="form-group mr-2">
                                    <label for="type" class="mr-2">Type:</label>
                                    <select name="type" id="type" class="form-control form-control-sm">
                                        <option value="">All Types</option>
                                        <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                        <option value="outgoing" {{ request('type') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                        <option value="missedcall" {{ request('type') == 'missedcall' ? 'selected' : '' }}>Missed Call</option>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <label for="status" class="mr-2">Status:</label>
                                    <select name="status" id="status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <option value="ANSWER" {{ request('status') == 'ANSWER' ? 'selected' : '' }}>Answered</option>
                                        <option value="BUSY" {{ request('status') == 'BUSY' ? 'selected' : '' }}>Busy</option>
                                        <option value="CANCEL" {{ request('status') == 'CANCEL' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="NO ANSWER" {{ request('status') == 'NO ANSWER' ? 'selected' : '' }}>No Answer</option>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <label for="date_from" class="mr-2">From:</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" 
                                           value="{{ request('date_from') }}">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="date_to" class="mr-2">To:</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" 
                                           value="{{ request('date_to') }}">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mr-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('leads.call-logs', $lead) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- Call Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Agent Number</th>
                                    <th>Telecaller</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Recording</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($callLogs as $callLog)
                                <tr>
                                    <td>{{ $callLog->id }}</td>
                                    <td>
                                        <span class="badge badge-{{ $callLog->type == 'incoming' ? 'info' : ($callLog->type == 'outgoing' ? 'success' : 'warning') }}">
                                            {{ ucfirst($callLog->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $callLog->AgentNumber ?? 'N/A' }}</td>
                                    <td>{{ $callLog->telecaller_name }}</td>
                                    <td>{!! $callLog->call_status_badge !!}</td>
                                    <td>{{ $callLog->formatted_duration }}</td>
                                    <td>{{ $callLog->date ? $callLog->date->format('Y-m-d') : 'N/A' }}</td>
                                    <td>{{ $callLog->start_time ?? 'N/A' }}</td>
                                    <td>
                                        @if($callLog->recording_URL)
                                            <a href="{{ $callLog->recording_URL }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-play"></i> Play
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.call-logs.show', $callLog) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No call logs found for this lead</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $callLogs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection