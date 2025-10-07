<form id="convertLeadForm" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-lg-12">
            <div class="p-1">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" value="{{ $lead->title }}" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="code" class="form-label">Country Code <span class="text-danger">*</span></label>
                <select class="form-control" name="code" required>
                    <option value="">Select Code</option>
                    @foreach($country_codes as $code => $country)
                        <option value="{{ $code }}" {{ $lead->code == $code ? 'selected' : '' }}>
                            {{ $code }} - {{ $country }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="phone" value="{{ $lead->phone }}" required>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="p-1">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ $lead->email }}">
            </div>
        </div>

        @if($course && $course->title)
        <div class="col-lg-12">
            <div class="p-1">
                <label class="form-label">Course Information</label>
                <div class="form-control-plaintext bg-light p-2 rounded">
                    <strong>{{ $course->title }}</strong> - ₹{{ number_format($course->amount, 2) }}
                </div>
            </div>
        </div>
        @endif


        <div class="col-lg-6">
            <div class="p-1">
                <label for="modal_board_id" class="form-label">Board</label>
                <select class="form-control" name="board_id" id="modal_board_id">
                    <option value="">Select Board</option>
                    @foreach($boards as $board)
                        <option value="{{ $board->id }}">{{ $board->title }} ({{ $board->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="modal_academic_assistant_id" class="form-label">Academic Assistant <span class="text-danger">*</span></label>
                <select class="form-control" name="academic_assistant_id" id="modal_academic_assistant_id" required>
                    <option value="">Select Academic Assistant</option>
                    @foreach($academic_assistants as $assistant)
                        <option value="{{ $assistant->id }}">{{ $assistant->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-12">
            <div class="p-1">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea class="form-control" name="remarks" rows="3" placeholder="Enter conversion remarks">{{ $lead->remarks }}</textarea>
            </div>
        </div>

        <!-- Payment Collection Section -->
        @if($course && $course->title)
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Payment Collection</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="payment_collected" id="modal_payment_collected" value="1">
                                <label class="form-check-label" for="modal_payment_collected">
                                    Payment Collected
                                </label>
                            </div>
                        </div>

                        <div id="payment_fields" style="display: none;" class="col-12">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="modal_total_amount_display" class="form-label">Total Amount</label>
                                        <input type="text" class="form-control" id="modal_total_amount_display" readonly>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="modal_payment_amount" class="form-label">Payment Amount <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="number" class="form-control" name="payment_amount" id="modal_payment_amount" step="0.01" min="0">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="modal_payment_type" class="form-label">Payment Type <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <select class="form-control" name="payment_type" id="modal_payment_type">
                                            <option value="">Select Payment Type</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Online">Online</option>
                                            <option value="Bank">Bank</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Card">Card</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-1">
                                        <label for="modal_transaction_id" class="form-label">Transaction ID <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="text" class="form-control" name="transaction_id" id="modal_transaction_id" placeholder="Enter transaction ID">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-1">
                                        <label for="modal_payment_file" class="form-label">Upload Receipt/Proof <span class="text-danger payment-required" style="display: none;">*</span></label>
                                        <input type="file" class="form-control" name="payment_file" id="modal_payment_file" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="button" id="convertLeadBtn">
                <span class="btn-text">Convert Lead</span>
                <span class="btn-loading" style="display: none;">
                    <i class="ti ti-loader-2 spin"></i> Converting...
                </span>
            </button>
        </div>
    </div>
</form>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.field-error {
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
    color: #dc3545 !important;
    font-weight: 500;
}

.form-control.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.form-control.is-invalid:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

/* Ensure error messages are visible */
.p-1 .field-error {
    margin-top: 0.25rem;
    margin-bottom: 0.5rem;
    display: block;
    width: 100%;
}
</style>

<script>
$(document).ready(function() {
        // Cache jQuery objects
        const $paymentCheckbox = $('#modal_payment_collected');
        const $paymentFields = $('#payment_fields');
        const $totalAmountDisplay = $('#modal_total_amount_display');
        const $paymentAmountInput = $('#modal_payment_amount');
        const $convertBtn = $('#convertLeadBtn');
        
        @if(!$course || !$course->title)
        // Hide payment section if no course is available
        $paymentCheckbox.closest('.card').hide();
        @endif

    // Show/hide payment fields based on checkbox
    function togglePaymentFields() {
        if ($paymentCheckbox.is(':checked')) {
            $paymentFields.show();
            updateTotalAmount();
            
            // Make payment fields required
            $paymentAmountInput.prop('required', true);
            $('#modal_payment_type').prop('required', true);
            $('#modal_transaction_id').prop('required', true);
            $('#modal_payment_file').prop('required', true);
            $('.payment-required').show();
        } else {
            $paymentFields.hide();
            
            // Remove required attribute and clear values
            $paymentAmountInput.prop('required', false).val('');
            $('#modal_payment_type').prop('required', false).val('');
            $('#modal_transaction_id').prop('required', false).val('');
            $('#modal_payment_file').prop('required', false).val('');
            $('.payment-required').hide();
        }
    }
    
    $paymentCheckbox.on('change click', function() {
        setTimeout(togglePaymentFields, 10);
    });


    // Set max payment amount
    $paymentAmountInput.on('input', function() {
        const totalAmount = parseFloat($totalAmountDisplay.val().replace(/[^\d.-]/g, '')) || 0;
        if (parseFloat($(this).val()) > totalAmount) {
            $(this).val(totalAmount);
        }
    });

    function updateTotalAmount() {
        @if($course && $course->title)
        // Use the course amount from the lead's course
        const amount = {{ $course->amount }};
        $totalAmountDisplay.val('₹' + amount.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        $paymentAmountInput.attr('max', amount);
        @else
        // No course information available
        $totalAmountDisplay.val('');
        @endif
    }


    // Initialize on page load
    updateTotalAmount();
    
    // AJAX form submission
    $convertBtn.on('click', function() {
        submitConvertForm();
    });
    
    // Form submission function
    function submitConvertForm() {
        const $form = $('#convertLeadForm');
        const $btnText = $convertBtn.find('.btn-text');
        const $btnLoading = $convertBtn.find('.btn-loading');
        
        // Clear previous errors
        clearFormErrors();
        
        // Client-side validation
        if (!validateForm()) {
            return;
        }
        
        // Show loading state
        $convertBtn.prop('disabled', true);
        $btnText.hide();
        $btnLoading.show();
        
        // Submit form via AJAX using jQuery
        const formData = new FormData($form[0]);
        
        console.log('Submitting form to:', '{{ route("leads.convert.submit", $lead->id) }}');
        console.log('Form data:', Object.fromEntries(formData));
        
        
        $.ajax({
            url: '{{ route("leads.convert.submit", $lead->id) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                console.log('AJAX Success:', data);
                if (data.success) {
                    // Show success message
                    showNotification('success', data.message || 'Lead converted successfully!');
                    
                    // Close modal and refresh page
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        displayFormErrors(data.errors);
                        showNotification('error', 'Please correct the errors below.');
                    } else {
                        showNotification('error', data.message || 'Failed to convert lead. Please try again.');
                    }
                    
                    // Reset button state
                    $convertBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoading.hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error Details:');
                console.error('Status:', xhr.status);
                console.error('Status Text:', xhr.statusText);
                console.error('Response Text:', xhr.responseText);
                console.error('Error:', error);
                
                // Check if it's a validation error (422 status)
                if (xhr.status === 422) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        console.log('Parsed validation response:', data);
                        if (data.errors) {
                            displayFormErrors(data.errors);
                            showNotification('error', 'Please correct the errors below.');
                        } else {
                            showNotification('error', data.message || 'Validation failed.');
                        }
                    } catch (e) {
                        console.error('Error parsing validation response:', e);
                        showNotification('error', 'Validation failed. Please check your input.');
                    }
                } else {
                    // Other errors
                    console.error('Non-validation error occurred');
                    showNotification('error', 'An error occurred. Please try again.');
                }
                
                // Reset button state
                $convertBtn.prop('disabled', false);
                $btnText.show();
                $btnLoading.hide();
            }
        });
    }
    
    // Display form validation errors
    function displayFormErrors(errors) {
        console.log('Displaying form errors:', errors);
        
        // Only proceed if there are actual errors
        if (!errors || Object.keys(errors).length === 0) {
            console.log('No errors to display');
            return;
        }
        
        // Clear previous errors
        $('.field-error').remove();
        $('.form-control').removeClass('is-invalid');
        
        // Display errors for each field
        $.each(errors, function(field, messages) {
            // Map field names to modal IDs
            const modalFieldMap = {
                'academic_assistant_id': 'modal_academic_assistant_id'
            };
            
            const fieldId = modalFieldMap[field] || field;
            let fieldElement = $(`#${fieldId}`);
            if (fieldElement.length === 0) {
                fieldElement = $(`[name="${field}"]`).first();
            }
            
            const fieldValue = fieldElement.val();
            
            if (fieldValue && fieldValue.trim() !== '' && fieldValue !== '0') {
                return; // Skip this field as it has a value
            }
            
            // Use the field element we found
            let fieldElementToUse = fieldElement;
            
            if (fieldElementToUse.length) {
                // Add error class to field
                fieldElementToUse.addClass('is-invalid');
                // Add error message below field
                const errorHtml = `<div class="field-error text-danger small mt-1">${messages[0]}</div>`;
                
                // Try to find the container
                let container = fieldElementToUse.closest('.p-1');
                if (container.length === 0) {
                    container = fieldElementToUse.closest('.form-group');
                }
                if (container.length === 0) {
                    container = fieldElementToUse.closest('.col-lg-6, .col-lg-12, .col-12');
                }
                
                if (container.length) {
                    container.append(errorHtml);
                } else {
                    // Fallback: append after the field element
                    fieldElementToUse.after(errorHtml);
                }
            } else {
                // Try to show error in a general location
                const errorHtml = `<div class="field-error text-danger small mt-1">${field}: ${messages[0]}</div>`;
                $('.card-body').first().append(errorHtml);
            }
        });
    }
    
    // Client-side form validation
    function validateForm() {
        let isValid = true;
        const errors = {};
        
        
        // Check required fields
        const requiredFields = ['name', 'code', 'phone', 'academic_assistant_id'];
        
        requiredFields.forEach(function(field) {
            // Map field names to modal IDs
            const modalFieldMap = {
                'academic_assistant_id': 'modal_academic_assistant_id'
            };
            
            const fieldId = modalFieldMap[field] || field;
            let fieldElement = $(`#${fieldId}`);
            if (fieldElement.length === 0) {
                fieldElement = $(`[name="${field}"]`).first();
            }
            
            const fieldValue = fieldElement.val();
            
            // Check if field is empty or has no value selected
            if (!fieldValue || fieldValue.trim() === '' || fieldValue === '0') {
                errors[field] = [`The ${field.replace('_', ' ')} field is required.`];
                isValid = false;
            }
        });
        
        // Check payment fields if payment collected is checked
        if ($paymentCheckbox.is(':checked')) {
            if (!$paymentAmountInput.val() || parseFloat($paymentAmountInput.val()) <= 0) {
                errors['payment_amount'] = ['The payment amount field is required and must be greater than 0.'];
                isValid = false;
            }
            
            if (!$('#modal_payment_type').val()) {
                errors['payment_type'] = ['The payment type field is required.'];
                isValid = false;
            }
            
            if (!$('#modal_transaction_id').val()) {
                errors['transaction_id'] = ['The transaction id field is required.'];
                isValid = false;
            }
            
            if (!$('#modal_payment_file').val()) {
                errors['payment_file'] = ['The payment file field is required.'];
                isValid = false;
            }
        }
        
        // Only display errors if there are actual validation errors
        if (!isValid && Object.keys(errors).length > 0) {
            displayFormErrors(errors);
            showNotification('error', 'Please correct the errors below.');
        }
        
        return isValid;
    }
    
    // Clear form errors
    function clearFormErrors() {
        $('.field-error').remove();
        $('.form-control').removeClass('is-invalid');
    }
    
    // Clear errors on input change
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
        $(this).closest('.p-1').find('.field-error').remove();
        
        // If field now has a value, clear any validation errors
        const fieldValue = $(this).val();
        if (fieldValue && fieldValue.trim() !== '' && fieldValue !== '0') {
            console.log(`Field ${$(this).attr('name')} now has value, clearing errors`);
        }
    });
    
    // Function to check if field should show validation error
    function shouldShowFieldError(fieldName, fieldValue) {
        // Don't show error if field has a valid value
        if (fieldValue && fieldValue.trim() !== '' && fieldValue !== '0') {
            return false;
        }
        return true;
    }

    // Notification function
    function showNotification(type, message) {
        // Use the project's toast notification system
        if (typeof showToast === 'function') {
            showToast(message, type);
        } else {
            // Fallback to alert if toast is not available
            if (type === 'success') {
                console.log('Success: ' + message);
            } else {
                console.log('Error: ' + message);
            }
        }
    }
});

// Test function for debugging
function testFunctionality() {
    console.log('=== TESTING FUNCTIONALITY ===');
    
    // Test checkbox
    const $checkbox = $('#payment_collected');
    console.log('Checkbox found:', $checkbox.length > 0);
    console.log('Checkbox checked:', $checkbox.is(':checked'));
    
    // Test payment fields
    const $paymentFields = $('#payment_fields');
    console.log('Payment fields found:', $paymentFields.length > 0);
    console.log('Payment fields visible:', $paymentFields.is(':visible'));
    
    
    // Test checkbox toggle
    if ($checkbox.length) {
        $checkbox.prop('checked', !$checkbox.is(':checked'));
        console.log('Checkbox toggled to:', $checkbox.is(':checked'));
        if ($paymentFields.length) {
            $paymentFields.toggle();
            console.log('Payment fields display set to:', $paymentFields.is(':visible'));
        }
    }
}






</script>
