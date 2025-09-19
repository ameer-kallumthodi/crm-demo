@extends('layouts.mantis')

@section('title', 'Call Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Call Logs</h3>
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
                            <form method="GET" action="{{ route('call-logs.index') }}" class="form-inline">
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
                                    <label for="agent_number" class="mr-2">Agent Number:</label>
                                    <input type="text" name="agent_number" id="agent_number" class="form-control form-control-sm" 
                                           value="{{ request('agent_number') }}" placeholder="Agent Number">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="destination_number" class="mr-2">Destination:</label>
                                    <input type="text" name="destination_number" id="destination_number" class="form-control form-control-sm" 
                                           value="{{ request('destination_number') }}" placeholder="Destination Number">
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
                                <a href="{{ route('call-logs.index') }}" class="btn btn-secondary btn-sm">
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
                                    <th>Destination</th>
                                    <th>Status</th>
                                    <th>Duration</th>
                                    <th>Date</th>
                                    <th>Time</th>
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
                                    <td>{{ $callLog->destinationNumber ?? 'N/A' }}</td>
                                    <td>{!! $callLog->call_status_badge !!}</td>
                                    <td>{{ $callLog->formatted_duration }}</td>
                                    <td>{{ $callLog->date ? $callLog->date->format('Y-m-d') : 'N/A' }}</td>
                                    <td>{{ $callLog->start_time ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('call-logs.show', $callLog) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="deleteCallLog({{ $callLog->id }})">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No call logs found</td>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this call log? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function deleteCallLog(callLogId) {
    $('#deleteModal').modal('show');
    
    $('#confirmDelete').off('click').on('click', function() {
        $.ajax({
            url: `/admin/call-logs/${callLogId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
}
</script>
@endsection