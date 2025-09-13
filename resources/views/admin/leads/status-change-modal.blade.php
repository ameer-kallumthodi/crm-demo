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
                        <input type="text" class="form-control" value="{{ $lead->phone }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <input type="text" class="form-control" value="{{ $lead->leadStatus->title }}" readonly>
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
            url: '{{ route("leads.status-change.post", $lead->id) }}',
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