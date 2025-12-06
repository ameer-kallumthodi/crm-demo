<div class="container p-2">
    <form id="hodChangePasswordForm" action="{{ route('admin.hod.update-password', $hodUser->id) }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="password">New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter New Password" required>
                    <div class="invalid-feedback" id="password-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm New Password" required>
                    <div class="invalid-feedback" id="password_confirmation-error"></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Change Password</button>
    </form>
</div>

<script>
document.getElementById('hodChangePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Clear previous errors
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    submitButton.disabled = true;
    submitButton.textContent = 'Changing...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => Promise.reject(data));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            toast_success(data.message);
            setTimeout(function() {
                $('#small_modal').modal('hide');
                location.reload();
            }, 1000);
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    const errorDiv = form.querySelector(`#${field}-error`);
                    if (input) {
                        input.classList.add('is-invalid');
                    }
                    if (errorDiv) {
                        errorDiv.textContent = data.errors[field][0];
                    }
                });
            }
            const errorMessage = data.message || 'Please correct the errors and try again.';
            toast_danger(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMessage = error.message || 'An error occurred. Please try again.';
        toast_danger(errorMessage);
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.textContent = 'Change Password';
    });
});
</script>

