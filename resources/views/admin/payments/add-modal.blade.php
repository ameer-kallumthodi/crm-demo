<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">Add New Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPaymentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount_paid" class="form-label">Amount Paid <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="amount_paid" name="amount_paid" step="0.01" min="0.01" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="payment_type" name="payment_type" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Online">Online</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Card">Card</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_id" class="form-label">Transaction ID</label>
                                <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date</label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="file_upload" class="form-label">Receipt/Proof</label>
                                <input type="file" class="form-control" id="file_upload" name="file_upload" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Information Display -->
                    <div class="alert alert-info">
                        <h6 class="mb-2"><strong>Invoice Information:</strong></h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Invoice #:</strong> <span id="modal_invoice_number">-</span></p>
                                <p class="mb-1"><strong>Student:</strong> <span id="modal_student_name">-</span></p>
                                <p class="mb-1"><strong>Course:</strong> <span id="modal_course_name">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Total Amount:</strong> ₹<span id="modal_total_amount">-</span></p>
                                <p class="mb-1"><strong>Paid Amount:</strong> ₹<span id="modal_paid_amount">-</span></p>
                                <p class="mb-1"><strong>Pending Amount:</strong> ₹<span id="modal_pending_amount">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitPaymentBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="paymentSpinner"></span>
                        <span id="submitPaymentText">Add Payment</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle form submission
    $('#addPaymentForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Show loading state
        $('#submitPaymentBtn').prop('disabled', true);
        $('#paymentSpinner').removeClass('d-none');
        $('#submitPaymentText').text('Adding...');
        
        // Get form data
        var formData = new FormData(this);
        var invoiceId = $('#addPaymentModal').data('invoice-id');
        
        $.ajax({
            url: '{{ route("admin.payments.store", ":invoiceId") }}'.replace(':invoiceId', invoiceId),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Show success message
                showNotification('success', response.message || 'Payment added successfully!');
                
                // Close modal
                $('#addPaymentModal').modal('hide');
                
                // Reload the page to show updated data
                location.reload();
            },
            error: function(xhr) {
                // Show error message
                var message = 'An error occurred while adding the payment.';
                
                if (xhr.status === 422) {
                    // Validation errors
                    var errors = xhr.responseJSON.errors;
                    displayFormErrors(errors);
                    message = 'Please correct the errors below.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                showNotification('danger', message);
            },
            complete: function() {
                // Reset loading state
                $('#submitPaymentBtn').prop('disabled', false);
                $('#paymentSpinner').addClass('d-none');
                $('#submitPaymentText').text('Add Payment');
            }
        });
    });
    
    // Function to display form errors
    function displayFormErrors(errors) {
        $.each(errors, function(field, messages) {
            var fieldElement = $('[name="' + field + '"]');
            fieldElement.addClass('is-invalid');
            fieldElement.siblings('.invalid-feedback').text(messages[0]);
        });
    }
    
    // Function to show notification
    function showNotification(type, message) {
        // You can implement your notification system here
        // For now, using alert as fallback
        if (type === 'success') {
            alert('Success: ' + message);
        } else {
            alert('Error: ' + message);
        }
    }
});

// Function to show the add payment modal
function showAddPaymentModal(invoiceId, invoiceData) {
    // Set invoice ID
    $('#addPaymentModal').data('invoice-id', invoiceId);
    
    // Populate invoice information
    $('#modal_invoice_number').text(invoiceData.invoice_number || '-');
    $('#modal_student_name').text(invoiceData.student_name || '-');
    $('#modal_course_name').text(invoiceData.course_name || '-');
    $('#modal_total_amount').text(parseFloat(invoiceData.total_amount || 0).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));
    $('#modal_paid_amount').text(parseFloat(invoiceData.paid_amount || 0).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));
    $('#modal_pending_amount').text(parseFloat(invoiceData.pending_amount || 0).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));
    
    // Reset form
    $('#addPaymentForm')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    
    // Show modal
    $('#addPaymentModal').modal('show');
}
</script>
