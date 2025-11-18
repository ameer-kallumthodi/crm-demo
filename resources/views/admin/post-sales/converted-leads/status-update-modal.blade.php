<!-- Status Update Modal Content -->
<form id="statusUpdateForm">
    @csrf
    <div class="modal-body">
        <!-- Converted Lead Information Card -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="ti ti-user me-2"></i>Student Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Student Name</label>
                            <p class="mb-0 fw-medium">{{ $convertedLead->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Phone</label>
                            <p class="mb-0 fw-medium">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-muted">Current Status</label>
                            <p class="mb-0">
                                <span class="badge bg-secondary">
                                    {{ $convertedLead->status ?? 'N/A' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Field -->
        <div class="mb-3">
            <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
            <select class="form-select" name="status" id="status" required>
                <option value="">Select Status</option>
                <option value="paid" {{ old('status', $convertedLead->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="unpaid" {{ old('status', $convertedLead->status) == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="cancel" {{ old('status', $convertedLead->status) == 'cancel' ? 'selected' : '' }}>Cancel</option>
                <option value="pending" {{ old('status', $convertedLead->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="followup" {{ old('status', $convertedLead->status) == 'followup' ? 'selected' : '' }}>Followup</option>
            </select>
        </div>

        <!-- Paid Status Field - Only shown when status is 'paid' -->
        <div class="mb-3" id="paidStatusSection" style="display: none;">
            <label class="form-label" for="paid_status">Paid Status <span class="text-danger">*</span></label>
            <select class="form-select" name="paid_status" id="paid_status">
                <option value="">Select Paid Status</option>
                <option value="Fully paid" {{ old('paid_status', $convertedLead->paid_status) == 'Fully paid' ? 'selected' : '' }}>Fully paid</option>
                <option value="Registration Paid" {{ old('paid_status', $convertedLead->paid_status) == 'Registration Paid' ? 'selected' : '' }}>Registration Paid</option>
                <option value="Registration Partially paid" {{ old('paid_status', $convertedLead->paid_status) == 'Registration Partially paid' ? 'selected' : '' }}>Registration Partially paid</option>
                <option value="Certificate Paid" {{ old('paid_status', $convertedLead->paid_status) == 'Certificate Paid' ? 'selected' : '' }}>Certificate Paid</option>
                <option value="Certificate Partially paid" {{ old('paid_status', $convertedLead->paid_status) == 'Certificate Partially paid' ? 'selected' : '' }}>Certificate Partially paid</option>
                <option value="Exam Paid" {{ old('paid_status', $convertedLead->paid_status) == 'Exam Paid' ? 'selected' : '' }}>Exam Paid</option>
                <option value="Exam Partially paid" {{ old('paid_status', $convertedLead->paid_status) == 'Exam Partially paid' ? 'selected' : '' }}>Exam Partially paid</option>
                <option value="Halticket Paid" {{ old('paid_status', $convertedLead->paid_status) == 'Halticket Paid' ? 'selected' : '' }}>Halticket Paid</option>
                <option value="Halticket Partially paid" {{ old('paid_status', $convertedLead->paid_status) == 'Halticket Partially paid' ? 'selected' : '' }}>Halticket Partially paid</option>
            </select>
        </div>

        <div class="row">
            <!-- Call Status Field -->
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="call_status">Call Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="call_status" id="call_status" required>
                        <option value="">Select Call Status</option>
                        <option value="RNR" {{ old('call_status', $convertedLead->call_status) == 'RNR' ? 'selected' : '' }}>RNR</option>
                        <option value="Switch off" {{ old('call_status', $convertedLead->call_status) == 'Switch off' ? 'selected' : '' }}>Switch off</option>
                        <option value="Attended" {{ old('call_status', $convertedLead->call_status) == 'Attended' ? 'selected' : '' }}>Attended</option>
                        <option value="Whatsapp connected" {{ old('call_status', $convertedLead->call_status) == 'Whatsapp connected' ? 'selected' : '' }}>Whatsapp connected</option>
                    </select>
                </div>
            </div>

            <!-- Called Date Field -->
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="called_date">Called Date</label>
                    <input type="date" class="form-control" name="called_date" id="called_date"
                        value="{{ old('called_date', $convertedLead->called_date ? $convertedLead->called_date->format('Y-m-d') : '') }}"
                        max="{{ date('Y-m-d') }}">
                </div>
            </div>

            <!-- Call Time Field -->
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label" for="called_time">Call Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" name="called_time" id="called_time"
                        value="{{ old('called_time', $convertedLead->called_time ? $convertedLead->called_time->format('H:i') : '') }}"
                        required>
                </div>
            </div>
        </div>

        <!-- Followup Date - Hidden when paid_status is 'Fully paid' -->
        <div id="followupSection">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="followup_date">Followup Date</label>
                        <input type="date" class="form-control" name="followup_date" id="followup_date"
                            value="{{ old('followup_date', $convertedLead->postsale_followupdate ? $convertedLead->postsale_followupdate->format('Y-m-d') : '') }}"
                            min="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Remarks Field (Post Sales Remarks) -->
        <div class="mb-3">
            <label class="form-label" for="post_sales_remarks">Remarks</label>
            <textarea class="form-control" name="post_sales_remarks" id="post_sales_remarks" rows="3" placeholder="Enter remarks...">{{ old('post_sales_remarks', $convertedLead->post_sales_remarks) }}</textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Status</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Handle status change to show/hide paid_status field
    $('#status').on('change', function() {
        const status = $(this).val();
        if (status === 'paid') {
            $('#paidStatusSection').show();
            $('#paid_status').prop('required', true);
        } else {
            $('#paidStatusSection').hide();
            $('#paid_status').prop('required', false);
            $('#paid_status').val('');
        }
        // Trigger paid_status change to update followup section
        $('#paid_status').trigger('change');
    });

    // Handle paid_status change to show/hide followup fields
    $('#paid_status').on('change', function() {
        const paidStatus = $(this).val();
        if (paidStatus === 'Fully paid') {
            $('#followupSection').hide();
            $('#followup_date').prop('required', false);
        } else {
            $('#followupSection').show();
            // Only require if status is not 'paid' or if paid_status is set but not 'Fully paid'
            const status = $('#status').val();
            if (status === 'paid' && paidStatus && paidStatus !== 'Fully paid') {
                $('#followup_date').prop('required', true);
            } else if (status !== 'paid') {
                $('#followup_date').prop('required', true);
            }
        }
    });

    // Initial state
    const initialStatus = $('#status').val();
    if (initialStatus === 'paid') {
        $('#paidStatusSection').show();
        $('#paid_status').prop('required', true);
        const paidStatus = $('#paid_status').val();
        if (paidStatus === 'Fully paid') {
            $('#followupSection').hide();
        } else {
            $('#followupSection').show();
            if (paidStatus) {
                $('#followup_date').prop('required', true);
            }
        }
    } else {
        $('#paidStatusSection').hide();
        $('#followupSection').show();
        $('#followup_date').prop('required', true);
    }

    // Handle form submission
    $('#statusUpdateForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const url = '{{ route('admin.post-sales.converted-leads.status-update-submit', $convertedLead->id) }}';

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Close modal first
                    $('#ajax_modal').modal('hide');
                    
                    // Show success toast
                    if (typeof showToast === 'function') {
                        showToast(response.message, 'success');
                    } else if (typeof toast_success === 'function') {
                        toast_success(response.message);
                    } else {
                        alert(response.message);
                    }
                    
                    // Reload DataTable if it exists
                    if ($.fn.DataTable.isDataTable('#postSalesConvertedTable')) {
                        $('#postSalesConvertedTable').DataTable().ajax.reload();
                    } else {
                        location.reload();
                    }
                } else {
                    // Show error toast
                    if (typeof showToast === 'function') {
                        showToast(response.message || 'An error occurred.', 'error');
                    } else if (typeof toast_error === 'function') {
                        toast_error(response.message || 'An error occurred.');
                    } else {
                        alert(response.message || 'An error occurred.');
                    }
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                }
                
                // Show error toast
                if (typeof showToast === 'function') {
                    showToast(errorMessage, 'error');
                } else if (typeof toast_error === 'function') {
                    toast_error(errorMessage);
                } else {
                    alert(errorMessage);
                }
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>

