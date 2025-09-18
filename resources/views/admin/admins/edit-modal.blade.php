<div class="p-0">
    <!-- Modal Body -->
    <div class="modal-body p-4">
        <form id="adminEditForm">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $admin->phone) }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="joining_date" class="form-label">Joining Date</label>
                    <input type="date" class="form-control" id="joining_date" name="joining_date" value="{{ old('joining_date', $admin->joining_date ? $admin->joining_date->format('Y-m-d') : '') }}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active User
                    </label>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="updateAdmin({{ $admin->id }})">
            <i class="ti ti-check"></i> Update Admin
        </button>
    </div>
</div>

<script>
function updateAdmin(adminId) {
    const form = document.getElementById('adminEditForm');
    const formData = new FormData(form);
    
    // Clear previous validation errors
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    // Show loading state
    const submitBtn = document.querySelector('button[onclick="updateAdmin(' + adminId + ')"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="ti ti-loader-2 spin"></i> Updating...';
    submitBtn.disabled = true;
    
    fetch('{{ route("admin.admins.update", $admin->id) }}', {
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
        toast_danger('An error occurred while updating the admin. Please try again.');
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
