<div class="row">
    <div class="col-12">
        <!-- Basic Student Information Section -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Basic Student Information</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Student Name:</label>
                        <p class="mb-0">{{ $convertedLead->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Register Number:</label>
                        <p class="mb-0">{{ $convertedLead->register_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Phone:</label>
                        <p class="mb-0">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email:</label>
                        <p class="mb-0">{{ $convertedLead->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Course:</label>
                        <p class="mb-0">{{ $convertedLead->course->title ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Current Batch:</label>
                        <p class="mb-0">{{ $convertedLead->batch->title ?? 'N/A' }}</p>
                    </div>
                    @if($convertedLead->batch && $convertedLead->batch->postponeBatch)
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Postponed To Batch:</label>
                        <p class="mb-0">
                            <span class="badge bg-info">{{ $convertedLead->batch->postponeBatch->title }}</span>
                        </p>
                    </div>
                    @endif
                    @if($convertedLead->batch && $convertedLead->batch->postpone_start_date && $convertedLead->batch->postpone_end_date)
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Postponed Period:</label>
                        <p class="mb-0">
                            {{ \Carbon\Carbon::parse($convertedLead->batch->postpone_start_date)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($convertedLead->batch->postpone_end_date)->format('d M Y') }}
                        </p>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label fw-bold">BDE Name:</label>
                        <p class="mb-0">{{ $convertedLead->lead->telecaller->name ?? 'Unassigned' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Post Sale Status:</label>
                        <p class="mb-0">
                            <span class="badge bg-dark">{{ ucfirst($convertedLead->postsale_status ?? 'N/A') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Postponed Action Section -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">Postponed Batch Action</h6>
            </div>
            <div class="card-body">
                <form id="postponedBatchForm" method="post" action="{{ route('admin.post-sales.converted-leads.postponed-batch.submit', $convertedLead->id) }}">
                    @csrf
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Note:</strong> After Submitting "Postponed" button will move this student to the postponed batch.
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="ti ti-calendar-time me-1"></i> Postponed
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle form submission via AJAX
    document.getElementById('postponedBatchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Disable submit button
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="ti ti-loader me-1"></i> Processing...';
        
        // Submit via AJAX
        $.ajax({
            url: form.action,
            type: 'POST',
            data: new FormData(form),
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

