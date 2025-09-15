<!-- Status Update Modal Content -->
<form id="statusChangeForm">
    @csrf
    <div class="modal-body">
        <!-- Lead Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="ti ti-user me-2"></i>Lead Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Lead Name</label>
                            <p class="mb-0 fw-medium">{{ $lead->title }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Phone</label>
                            <p class="mb-0 fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Current Status</label>
                            <p class="mb-0">
                                <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->leadStatus->id) }}">
                                    {{ $lead->leadStatus->title }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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

        <!-- Activity History Section -->
        @if($lead->leadActivities && $lead->leadActivities->count() > 0)
        <div class="mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="ti ti-history me-2"></i>Recent Activity History</h6>
                </div>
                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                    @foreach($lead->leadActivities->take(5) as $activity)
                    <div class="d-flex align-items-start mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0 me-3">
                            <div class="avtar avtar-s rounded-circle bg-light-info d-flex align-items-center justify-content-center">
                                @if($activity->leadStatus)
                                    <i class="ti ti-arrow-right f-12"></i>
                                @elseif($activity->activity_type == 'bulk_upload')
                                    <i class="ti ti-upload f-12"></i>
                                @else
                                    <i class="ti ti-clock f-12"></i>
                                @endif
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <h6 class="mb-1 f-14 fw-semibold">
                                        @if($activity->leadStatus)
                                            Status Changed to: {{ $activity->leadStatus->title }}
                                        @elseif($activity->activity_type)
                                            {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                        @else
                                            Activity
                                        @endif
                                    </h6>
                                    @if($activity->description)
                                        <p class="mb-1 text-muted f-13">{{ $activity->description }}</p>
                                    @endif
                                    @if($activity->remarks)
                                        <div class="mb-1 f-13 text-dark" style="white-space: pre-wrap; word-wrap: break-word;">{{ $activity->remarks }}</div>
                                    @endif
                                </div>
                                <small class="text-muted f-12">
                                    {{ $activity->created_at->format('M d, h:i A') }}
                                </small>
                            </div>
                            @if($activity->createdBy)
                                <small class="text-muted f-12">
                                    <i class="ti ti-user me-1"></i>By: {{ $activity->createdBy->name }}
                                </small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Status</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Handle form submission
    $('#statusChangeForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2"></i> Updating...');
        
        $.ajax({
            url: '{{ route("leads.status-update-submit", $lead->id) }}',
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
                    $('#ajax_modal').modal('hide');
                    
                    // Reload the page or update the table
                    location.reload();
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
});
</script>