@extends('layouts.app')

@section('title', 'Change Lead Status')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Change Lead Status</h4>
                    <div class="card-tools">
                        <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Back to Leads
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Lead Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Lead Name:</strong> {{ $lead->title }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Phone:</strong> {{ $lead->code }}{{ $lead->phone }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Current Status:</strong> 
                                            <span class="badge bg-primary">{{ $lead->leadStatus->title ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Lead Source:</strong> {{ $lead->leadSource->title ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-info">
                                            <i class="ti ti-eye"></i> View Lead
                                        </a>
                                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-warning">
                                            <i class="ti ti-edit"></i> Edit Lead
                                        </a>
                                        <a href="{{ route('leads.call-logs', $lead) }}" class="btn btn-secondary">
                                            <i class="ti ti-phone-call"></i> Call Logs
                                        </a>
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

<!-- Status Change Modal -->
<div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeModalLabel">Change Lead Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusChangeForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lead Name</label>
                        <input type="text" class="form-control" value="{{ $lead->title }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" value="{{ $lead->code }}{{ $lead->phone }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <input type="text" class="form-control" value="{{ $lead->leadStatus->title ?? 'N/A' }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="lead_status_id">New Lead Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="lead_status_id" id="lead_status_id" required>
                            <option value="">Select New Status</option>
                            @foreach($leadStatuses as $status)
                                <option value="{{ $status->id }}" {{ $status->id == $lead->lead_status_id ? 'selected' : '' }}>
                                    {{ $status->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="remarks">Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="remarks" id="remarks" rows="3" required placeholder="Enter remarks for this status change...">{{ $lead->remarks }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date" id="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="time">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="time" id="time" value="{{ date('H:i') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Show the modal automatically when page loads
    $('#statusChangeModal').modal('show');
    
    // Handle form submission
    $('#statusChangeForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2"></i> Updating...');
        
        $.ajax({
            url: '{{ route("leads.status-change", $lead->id) }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Show success message
                    if (typeof toast_success === 'function') {
                        toast_success(response.message);
                    } else {
                        alert(response.message);
                    }
                    
                    // Close modal
                    $('#statusChangeModal').modal('hide');
                    
                    // Redirect to leads index
                    setTimeout(() => {
                        window.location.href = '{{ route("leads.index") }}';
                    }, 1000);
                } else {
                    // Show error message
                    if (typeof toast_error === 'function') {
                        toast_error(response.message);
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the status.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                }
                
                if (typeof toast_error === 'function') {
                    toast_error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Handle modal close - redirect to leads index
    $('#statusChangeModal').on('hidden.bs.modal', function () {
        window.location.href = '{{ route("leads.index") }}';
    });
});
</script>
@endsection
