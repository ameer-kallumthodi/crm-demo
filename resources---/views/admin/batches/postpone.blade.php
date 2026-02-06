<div class="row">
    <div class="col-12">
        <!-- Batch Details Section -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Current Batch Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Batch Title:</label>
                        <p class="mb-0">{{ $batch->title }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Course:</label>
                        <p class="mb-0">{{ $batch->course->title ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Amount:</label>
                        <p class="mb-0">
                            @if(!is_null($batch->amount))
                                ₹ {{ number_format($batch->amount, 2) }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status:</label>
                        <p class="mb-0">
                            @if($batch->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                    </div>
                    @if($batch->description)
                    <div class="col-12">
                        <label class="form-label fw-bold">Description:</label>
                        <p class="mb-0">{{ $batch->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Postponed Form -->
        <form id="postponeForm" method="post" action="{{ route('admin.batches.postpone.submit', $batch->id) }}">
            @csrf
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Postponed Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($batch->is_postpone_active)
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="set_inactive" class="form-label">Postponed Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="set_inactive" id="set_inactive" value="1">
                                    <label class="form-check-label" for="set_inactive">Set Postponed Status to Inactive (This will clear all postponed fields)</label>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-12" id="postponeFieldsSection">
                            <div class="mb-3">
                                <label for="postpone_batch_id" class="form-label">Postponed To Batch <span class="text-danger">*</span></label>
                                <select class="form-control" name="postpone_batch_id" id="postpone_batch_id">
                                    <option value="">Select Batch</option>
                                    @foreach($postponeBatches as $postponeBatch)
                                        <option value="{{ $postponeBatch->id }}" {{ $batch->postpone_batch_id == $postponeBatch->id ? 'selected' : '' }}>{{ $postponeBatch->title }}</option>
                                    @endforeach
                                </select>
                                @if($postponeBatches->isEmpty())
                                    <small class="text-muted">No other batches available for this course.</small>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6" id="postponeStartDateSection">
                            <div class="mb-3">
                                <label for="postpone_start_date" class="form-label">Postponed Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="postpone_start_date" id="postpone_start_date" value="{{ $batch->postpone_start_date ? \Carbon\Carbon::parse($batch->postpone_start_date)->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                        <div class="col-md-6" id="postponeEndDateSection">
                            <div class="mb-3">
                                <label for="postpone_end_date" class="form-label">Postponed End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="postpone_end_date" id="postpone_end_date" value="{{ $batch->postpone_end_date ? \Carbon\Carbon::parse($batch->postpone_end_date)->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                        <div class="col-md-6" id="postponeAmountSection">
                            <div class="mb-3">
                                <label for="batch_postpone_amount" class="form-label">Batch Postponed Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" min="0" class="form-control" name="batch_postpone_amount" id="batch_postpone_amount" value="{{ old('batch_postpone_amount', $batch->batch_postpone_amount) }}" placeholder="Enter postpone amount">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Postponed Batch</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Handle inactive checkbox toggle (only if checkbox exists)
    const setInactiveCheckbox = document.getElementById('set_inactive');
    if (setInactiveCheckbox) {
        setInactiveCheckbox.addEventListener('change', function() {
            const isInactive = this.checked;
            const postponeFields = document.getElementById('postponeFieldsSection');
            const postponeStartDate = document.getElementById('postponeStartDateSection');
            const postponeEndDate = document.getElementById('postponeEndDateSection');
            const postponeAmount = document.getElementById('postponeAmountSection');
            
            if (isInactive) {
                // Hide postpone fields and clear values
                postponeFields.style.display = 'none';
                postponeStartDate.style.display = 'none';
                postponeEndDate.style.display = 'none';
                postponeAmount.style.display = 'none';
                
                // Clear values
                document.getElementById('postpone_batch_id').value = '';
                document.getElementById('postpone_start_date').value = '';
                document.getElementById('postpone_end_date').value = '';
                document.getElementById('batch_postpone_amount').value = '';
                
                // Remove required attributes
                document.getElementById('postpone_batch_id').removeAttribute('required');
                document.getElementById('postpone_start_date').removeAttribute('required');
                document.getElementById('postpone_end_date').removeAttribute('required');
            } else {
                // Show postpone fields
                postponeFields.style.display = 'block';
                postponeStartDate.style.display = 'block';
                postponeEndDate.style.display = 'block';
                postponeAmount.style.display = 'block';
                
                // Add required attributes
                document.getElementById('postpone_batch_id').setAttribute('required', 'required');
                document.getElementById('postpone_start_date').setAttribute('required', 'required');
                document.getElementById('postpone_end_date').setAttribute('required', 'required');
            }
        });
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const setInactiveCheckbox = document.getElementById('set_inactive');
        if (setInactiveCheckbox) {
            // Checkbox exists, meaning postpone status is active (1)
            // Show all fields and set required attributes
            document.getElementById('postponeFieldsSection').style.display = 'block';
            document.getElementById('postponeStartDateSection').style.display = 'block';
            document.getElementById('postponeEndDateSection').style.display = 'block';
            document.getElementById('postponeAmountSection').style.display = 'block';
            
            document.getElementById('postpone_batch_id').setAttribute('required', 'required');
            document.getElementById('postpone_start_date').setAttribute('required', 'required');
            document.getElementById('postpone_end_date').setAttribute('required', 'required');
        } else {
            // Checkbox doesn't exist, meaning postpone status is inactive (0)
            // Show all fields for filling in
            document.getElementById('postponeFieldsSection').style.display = 'block';
            document.getElementById('postponeStartDateSection').style.display = 'block';
            document.getElementById('postponeEndDateSection').style.display = 'block';
            document.getElementById('postponeAmountSection').style.display = 'block';
            
            // Add required attributes
            document.getElementById('postpone_batch_id').setAttribute('required', 'required');
            document.getElementById('postpone_start_date').setAttribute('required', 'required');
            document.getElementById('postpone_end_date').setAttribute('required', 'required');
        }
    });

    // Validate that end date is after start date
    document.getElementById('postpone_end_date').addEventListener('change', function() {
        const startDate = document.getElementById('postpone_start_date').value;
        const endDate = this.value;
        
        if (startDate && endDate && endDate < startDate) {
            alert('End date must be after start date.');
            this.value = '';
        }
    });

    document.getElementById('postpone_start_date').addEventListener('change', function() {
        const startDate = this.value;
        const endDate = document.getElementById('postpone_end_date').value;
        
        if (startDate && endDate && endDate < startDate) {
            alert('End date must be after start date.');
            document.getElementById('postpone_end_date').value = '';
        }
    });

    // Handle form submission via AJAX
    document.getElementById('postponeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Disable submit button
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="ti ti-loader"></i> Processing...';
        
        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show toast notification
                    if (typeof toast_success !== 'undefined') {
                        toast_success(response.message);
                    } else {
                        alert(response.message);
                    }
                    $('#ajax_modal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    // Show error toast
                    if (typeof toast_error !== 'undefined') {
                        toast_error(response.message || 'Something went wrong.');
                    } else {
                        alert('Error: ' + (response.message || 'Something went wrong.'));
                    }
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('\n');
                }
                // Show error toast
                if (typeof toast_error !== 'undefined') {
                    toast_error(errorMessage);
                } else {
                    alert('Error: ' + errorMessage);
                }
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
    });
</script>

