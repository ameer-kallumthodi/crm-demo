<div class="p-0">
    <!-- Modal Body -->
    <div class="modal-body p-4">
        <div class="text-center mb-4">
            <div class="avtar avtar-xl rounded-circle bg-light-warning mx-auto mb-3 d-flex align-items-center justify-content-center">
                <i class="ti ti-key text-warning" style="font-size: 2rem;"></i>
            </div>
            <h5 class="mb-1">Change Password</h5>
            <p class="text-muted mb-0">Update password for {{ $admin->name }}</p>
        </div>
        
        <form id="changePasswordForm">
            @csrf
            
            <div class="mb-3">
                <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="form-text">Password must be at least 8 characters long.</div>
                <div class="invalid-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                <div class="invalid-feedback"></div>
            </div>
        </form>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="updatePassword({{ $admin->id }})">
            <i class="ti ti-key"></i> Update Password
        </button>
    </div>
</div>

<script>
function updatePassword(adminId) {
    const form = document.getElementById('changePasswordForm');
    const formData = new FormData(form);
    
    // Clear previous validation errors
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    // Show loading state
    const submitBtn = document.querySelector('button[onclick="updatePassword(' + adminId + ')"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="ti ti-loader-2 spin"></i> Updating...';
    submitBtn.disabled = true;
    
    fetch('{{ route("admin.admins.update-password", $admin->id) }}', {
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
        toast_danger('An error occurred while updating the password. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
