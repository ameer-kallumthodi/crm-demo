<!-- Status Update Modal Content -->
<form id="statusUpdateForm" method="POST">
    @csrf
    <input type="hidden" name="lead_id" id="status_lead_id" value="{{ $lead->id ?? '' }}">
    <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div>
                                <label for="lead_status_id" class="form-label font-size-13 text-muted">Lead Status <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="lead_status_id" id="lead_status_id" required>
                                    <option value="">Select Status</option>
                                    @foreach($leadStatuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div>
                                <label for="remarks" class="form-label font-size-13 text-muted">Remarks <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="remarks" id="remarks" required>
                            </div>
                        </div>
                        
                        
                        <div class="col-lg-12">
                            <div>
                                <label for="date" class="form-label font-size-13 text-muted">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required />
                            </div>
                        </div>
                        
                        <div class="col-lg-12">
                            <div>
                                <label for="time" class="form-label font-size-13 text-muted">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="time" name="time" value="{{ date('H:i') }}" required />
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submit_button">Update Status</button>
                </div>
            </form>

<script>
$(document).ready(function() {

    // Handle form submission
    $('#statusUpdateForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const submitBtn = $('#submit_button');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<i class="ti ti-loader-2 spin"></i> Updating...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("leads.status-update-submit", $lead->id) }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toast_success(response.message);
                    $('#ajax_modal').modal('hide');
                    // Reload the page or update the table
                    location.reload();
                } else {
                    toast_danger(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.message) {
                    toast_danger(response.message);
                } else {
                    toast_danger('An error occurred while updating the status.');
                }
            },
            complete: function() {
                // Reset button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });
});
</script>