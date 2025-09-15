@extends('layouts.mantis')

@section('title', 'Lead Details')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Lead Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item">{{ $lead->title }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Lead Details</h2>
            <div class="btn-group">
                @if($lead->phone && is_telecaller())
                <button class="btn btn-success" onclick="makeCall({{ $lead->id }}, '{{ $lead->code }}{{ $lead->phone }}')">
                    <i class="fas fa-phone"></i> Call Lead
                </button>
                @endif
                <a href="{{ route('leads.call-logs', $lead) }}" class="btn btn-info">
                    <i class="fas fa-phone-alt"></i> Call Logs
                </a>
                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Lead
                </a>
                <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Leads
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Lead Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $lead->title }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $lead->code }}{{ $lead->phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $lead->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $lead->leadStatus->color ?? 'secondary' }}">
                                        {{ $lead->leadStatus->title ?? 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Source:</strong></td>
                                <td>{{ $lead->leadSource->title ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Course:</strong></td>
                                <td>{{ $lead->course->title ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Telecaller:</strong></td>
                                <td>{{ $lead->telecaller->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Place:</strong></td>
                                <td>{{ $lead->place ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Remarks:</strong></td>
                                <td>{{ $lead->remarks ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $lead->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="card-title">Call History</h5>
                        <div class="call-history">
                            @if($lead->leadActivities && $lead->leadActivities->count() > 0)
                                @foreach($lead->leadActivities as $activity)
                                <div class="activity-item mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $activity->activity_type ?? 'Activity' }}</strong>
                                        <small class="text-muted">{{ $activity->created_at->format('d M Y H:i') }}</small>
                                    </div>
                                    <p class="mb-1">{{ $activity->notes ?? 'No notes' }}</p>
                                    <small class="text-muted">By: {{ $activity->user->name ?? 'System' }}</small>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted">No call history available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function makeCall(leadId, phoneNumber) {
    if (!confirm('Are you sure you want to call ' + phoneNumber + '?')) {
        return;
    }

    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calling...';
    button.disabled = true;

    // Make the API call
    $.ajax({
        url: '/api/voxbay/outgoing-call',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            lead_id: leadId,
            phoneNumber: phoneNumber,
            telecaller_id: {{ session('user_id') }},
            country_code: '{{ $lead->code ?? '91' }}'
        }),
        success: function(response) {
            if (response.status === 'success') {
                alert('Call initiated successfully!');
                // Optionally refresh the page to show updated call logs
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while making the call';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        },
        complete: function() {
            // Reset button state
            button.innerHTML = originalText;
            button.disabled = false;
        }
    });
}
</script>
@endpush
