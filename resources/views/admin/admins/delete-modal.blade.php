<div class="p-0">
    <!-- Modal Body -->
    <div class="modal-body p-4">
        <div class="text-center">
            <div class="mb-4">
                <div class="avtar avtar-xl rounded-circle bg-light-danger mx-auto mb-3 d-flex align-items-center justify-content-center">
                    <i class="ti ti-alert-triangle text-danger" style="font-size: 2rem;"></i>
                </div>
                <h5 class="mb-3">Delete Admin User</h5>
                <p class="text-muted mb-4">
                    Are you sure you want to delete <strong>{{ $admin->name }}</strong>? 
                    This action cannot be undone and will permanently remove the admin user from the system.
                </p>
            </div>
            
            <div class="row text-start">
                <div class="col-6">
                    <strong>Name:</strong>
                </div>
                <div class="col-6">
                    {{ $admin->name }}
                </div>
                <div class="col-6">
                    <strong>Email:</strong>
                </div>
                <div class="col-6">
                    {{ $admin->email }}
                </div>
                <div class="col-6">
                    <strong>Phone:</strong>
                </div>
                <div class="col-6">
                    {{ $admin->phone }}
                </div>
                <div class="col-6">
                    <strong>Role:</strong>
                </div>
                <div class="col-6">
                    {{ $admin->role->title ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" onclick="deleteAdmin({{ $admin->id }})">
            <i class="ti ti-trash"></i> Delete Admin
        </button>
    </div>
</div>

<script>
function deleteAdmin(adminId) {
    // Show loading state
    const submitBtn = document.querySelector('button[onclick="deleteAdmin(' + adminId + ')"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="ti ti-loader-2 spin"></i> Deleting...';
    submitBtn.disabled = true;
    
    fetch('{{ route("admin.admins.destroy", $admin->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            _method: 'DELETE'
        })
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
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast_danger('An error occurred while deleting the admin. Please try again.');
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
