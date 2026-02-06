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

                    <!-- Demo Booking Button - Only shown when status 6 is selected -->
                    <div class="mb-3" id="demoBookingSection" style="display: none;">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="ti ti-info-circle me-2"></i>
                            <div class="flex-grow-1">
                                <strong>Demo Conduction Required:</strong> Please complete the demo conduction form before updating the status.
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="https://docs.google.com/forms/d/e/1FAIpQLSchtc8xlKUJehZNmzoKTkRvwLwk4-SGjzKSHM2UFToAhgdTlQ/viewform?usp=sf_link" 
                               target="_blank" 
                               class="btn btn-warning" 
                               id="demoBookingBtn"
                               title="Open Demo Conduction Form">
                                <i class="ti ti-file-text me-2"></i>Complete Demo Conduction Form
                            </a>
                            <div class="mt-2">
                                <small class="text-muted">Click the button above to open the demo conduction form in a new tab. After clicking this button, you can update the status.</small>
                            </div>
                        </div>
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
                    <button type="submit" class="btn btn-primary" id="updateStatusBtn">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let formCompleted = false;
    
    // Handle status selection change
    $('#lead_status_id').on('change', function() {
        const selectedStatus = $(this).val();
        const demoBookingSection = $('#demoBookingSection');
        const updateStatusBtn = $('#updateStatusBtn');
        
        if (selectedStatus == '6') {
            // Show demo booking section
            demoBookingSection.show();
            // Disable update button until form is completed
            updateStatusBtn.prop('disabled', true);
            updateStatusBtn.html('Complete Demo Booking First');
            formCompleted = false;
        } else {
            // Hide demo booking section
            demoBookingSection.hide();
            // Enable update button
            updateStatusBtn.prop('disabled', false);
            updateStatusBtn.html('Update Status');
            formCompleted = true;
        }
    });
    
    // Handle demo booking form button click
    $('#demoBookingBtn').on('click', function() {
        formCompleted = true;
        const updateStatusBtn = $('#updateStatusBtn');
        
        // Enable update button
        updateStatusBtn.prop('disabled', false);
        updateStatusBtn.html('Update Status');
        
        // Show a brief success message
        if (typeof toast_success === 'function') {
            toast_success('Demo conduction form opened. You can now update the status.');
        }
    });
    
    // Handle form submission
    $('#statusChangeForm').on('submit', function(e) {
        e.preventDefault();
        
        // Check if status 6 is selected and form is not completed
        const selectedStatus = $('#lead_status_id').val();
        if (selectedStatus == '6' && !formCompleted) {
            if (typeof toast_error === 'function') {
                toast_error('Please click the "Complete Demo Conduction Form" button before updating the status.');
            } else {
                alert('Please click the "Complete Demo Conduction Form" button before updating the status.');
            }
            return;
        }
        
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
    
    // Initialize form state on page load
    const initialStatus = $('#lead_status_id').val();
    if (initialStatus == '6') {
        $('#demoBookingSection').show();
        // Don't disable the button if current status is already 6
        $('#updateStatusBtn').prop('disabled', false).html('Update Status');
        formCompleted = true; // Allow immediate submission since current status is 6
    }
});
</script>