<div class="row">
    <div class="col-12">
        <form id="updateRegisterNumberForm">
            @csrf
            <div class="mb-3">
                <label for="register_number" class="form-label">Student Register Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="register_number" name="register_number" 
                       value="{{ $convertedLead->register_number }}" 
                       placeholder="Enter student register number" required>
                <div class="invalid-feedback" id="register_number_error"></div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="updateRegisterBtn">
                    <span class="spinner-border spinner-border-sm d-none" id="updateSpinner"></span>
                    Update Register Number
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#updateRegisterNumberForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Show loading state
        $('#updateRegisterBtn').prop('disabled', true);
        $('#updateSpinner').removeClass('d-none');
        
        $.ajax({
            url: '{{ route("admin.converted-leads.update-register-number", $convertedLead->id) }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert_modal_success(response.message, 'Success!');
                    
                    // Close modal
                    $('#small_modal').modal('hide');
                    
                    // Reload the page to show updated data
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`#${field}`).addClass('is-invalid');
                        $(`#${field}_error`).text(errors[field][0]);
                    }
                } else if (xhr.status === 403) {
                    alert_modal_error(xhr.responseJSON.error || 'Access denied.');
                } else {
                    alert_modal_error('Something went wrong. Please try again.');
                }
            },
            complete: function() {
                // Hide loading state
                $('#updateRegisterBtn').prop('disabled', false);
                $('#updateSpinner').addClass('d-none');
            }
        });
    });
});
</script>
