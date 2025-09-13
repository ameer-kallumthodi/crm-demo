<div class="p-0">
    <!-- Modal Body -->
    <div class="modal-body p-4">
        <!-- Lead Info Header -->
        <div class="text-center mb-4 pb-3 border-bottom">
            <div class="avatar-lg mx-auto mb-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span class="text-white fw-bold" style="font-size: 1.5rem;">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
            </div>
            <h5 class="mb-1">{{ $lead->title }}</h5>
            <p class="text-muted mb-0">{{ $lead->phone }}</p>
        </div>

        <form id="statusUpdateForm">
            @csrf
            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
            
            <!-- Current Status Display -->
            <div class="mb-4">
                <label class="form-label fw-bold">Current Status:</label>
                <div class="p-3 bg-light rounded">
                    <span class="badge bg-{{ $lead->leadStatus->color ?? 'secondary' }} text-dark fs-6">
                        {{ $lead->leadStatus->title ?? 'No Status' }}
                    </span>
                </div>
            </div>

            <!-- New Status Selection -->
            <div class="mb-4">
                <label for="new_status" class="form-label fw-bold">Update Status To:</label>
                <select name="lead_status_id" id="new_status" class="form-select" required>
                    <option value="">Select New Status</option>
                    @foreach($leadStatuses as $status)
                        <option value="{{ $status->id }}" 
                                {{ old('lead_status_id') == $status->id ? 'selected' : '' }}
                                data-color="{{ $status->color ?? 'secondary' }}">
                            {{ $status->title }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>

            <!-- Remarks -->
            <div class="mb-4">
                <label for="remarks" class="form-label fw-bold">Remarks:</label>
                <textarea name="remarks" id="remarks" class="form-control" rows="3" 
                          placeholder="Enter remarks for this status update...">{{ old('remarks') }}</textarea>
                <div class="invalid-feedback"></div>
            </div>

            <!-- Date and Time -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="followup_date" class="form-label fw-bold">Date:</label>
                    <input type="date" name="followup_date" id="followup_date" class="form-control" 
                           value="{{ old('followup_date', date('Y-m-d')) }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="followup_time" class="form-label fw-bold">Time:</label>
                    <input type="time" name="followup_time" id="followup_time" class="form-control" 
                           value="{{ old('followup_time', date('H:i')) }}" required>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

        </form>

        <!-- Lead Activity History -->
        <div class="mt-4 pt-4 border-top">
            <h6 class="fw-bold mb-3">Recent Activity History</h6>
            @if($lead->leadActivities && $lead->leadActivities->count() > 0)
                <div class="timeline-simple">
                    @foreach($lead->leadActivities->take(5) as $activity)
                    <div class="timeline-item-simple mb-3">
                        <div class="d-flex align-items-start">
                            <div class="timeline-marker-simple me-3">
                                <i class="ti ti-circle-fill text-{{ $activity->leadStatus->color ?? 'secondary' }}"></i>
                            </div>
                            <div class="timeline-content-simple flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 text-dark">
                                        {{ $activity->leadStatus->title ?? ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                    </h6>
                                    <small class="text-muted">{{ $activity->created_at->format('M d, Y H:i') }}</small>
                                </div>
                                <p class="mb-1 text-muted small">{{ $activity->description ?? 'No description' }}</p>
                                @if($activity->leadStatus)
                                    <p class="mb-1 small"><strong>Status:</strong> {{ $activity->leadStatus->title }}</p>
                                @endif
                                @if($activity->remarks)
                                    <p class="mb-1 small"><strong>Remarks:</strong> {{ $activity->remarks }}</p>
                                @endif
                                @if($activity->followup_date)
                                    <p class="mb-0 small text-info">
                                        <i class="ti ti-calendar"></i> Follow-up: {{ \Carbon\Carbon::parse($activity->followup_date)->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-3">
                    <i class="ti ti-history f-48 text-muted mb-2 d-block"></i>
                    <p class="text-muted mb-0">No activity history found</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="updateLeadStatus()">
            <i class="ti ti-check"></i> Update Status
        </button>
    </div>
</div>

<script>
function updateLeadStatus() {
    const form = document.getElementById('statusUpdateForm');
    const formData = new FormData(form);
    
    // Clear previous validation errors
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    // Show loading state
    const submitBtn = document.querySelector('button[onclick="updateLeadStatus()"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="ti ti-loader-2 spin"></i> Updating...';
    submitBtn.disabled = true;
    
    fetch('{{ route("leads.status-update", $lead->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            toast_success(data.message);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.querySelector('#ajaxModal'));
            modal.hide();
            
            // Reload the page to show updated data
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            // Show error message
            toast_danger(data.message);
            
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    const feedback = input.nextElementSibling;
                    if (input && feedback) {
                        input.classList.add('is-invalid');
                        feedback.textContent = data.errors[field][0];
                    }
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast_danger('An error occurred while updating the status. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Status update form ready
document.addEventListener('DOMContentLoaded', function() {
    // Form is ready for submission
});
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.timeline-simple {
    position: relative;
}

.timeline-item-simple {
    position: relative;
}

.timeline-marker-simple {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.timeline-marker-simple i {
    font-size: 12px;
}

.timeline-content-simple {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    border-left: 3px solid #dee2e6;
}

.timeline-item-simple:not(:last-child) .timeline-content-simple::after {
    content: '';
    position: absolute;
    left: 9px;
    top: 100%;
    width: 2px;
    height: 20px;
    background: #dee2e6;
}

.timeline-item-simple:last-child .timeline-content-simple {
    border-left-color: #28a745;
}
</style>
