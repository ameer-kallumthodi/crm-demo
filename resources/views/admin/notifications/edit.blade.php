<form id="editNotificationForm">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $notification->title }}" required>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                <textarea class="form-control" id="message" name="message" rows="4" required>{{ $notification->message }}</textarea>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">Select Type</option>
                    <option value="info" {{ $notification->type === 'info' ? 'selected' : '' }}>Info</option>
                    <option value="success" {{ $notification->type === 'success' ? 'selected' : '' }}>Success</option>
                    <option value="warning" {{ $notification->type === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="error" {{ $notification->type === 'error' ? 'selected' : '' }}>Error</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="target_type" class="form-label">Target Type <span class="text-danger">*</span></label>
                <select class="form-control" id="target_type" name="target_type" required onchange="toggleUserSelection()">
                    <option value="">Select Target</option>
                    <option value="all" {{ $notification->target_type === 'all' ? 'selected' : '' }}>All Users</option>
                    <option value="role" {{ $notification->target_type === 'role' ? 'selected' : '' }}>Specific Role</option>
                    <option value="user" {{ $notification->target_type === 'user' ? 'selected' : '' }}>Specific User</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                <select class="form-control" id="role_id" name="role_id" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $notification->role_id == $role->id ? 'selected' : '' }}>{{ $role->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6" id="user_selection" style="display: {{ $notification->target_type === 'user' ? 'block' : 'none' }};">
            <div class="mb-3">
                <label for="user_id" class="form-label">User</label>
                <select class="form-control" id="user_id" name="user_id">
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" data-role="{{ $user->role_id }}" {{ $notification->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->role->title ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ $notification->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-save"></i> Update Notification
                </button>
            </div>
        </div>
    </div>
</form>

<script>
function toggleUserSelection() {
    const targetType = document.getElementById('target_type').value;
    const userSelection = document.getElementById('user_selection');
    const userIdSelect = document.getElementById('user_id');
    
    if (targetType === 'user') {
        userSelection.style.display = 'block';
        userIdSelect.required = true;
        // Filter users based on selected role
        filterUsersByRole();
    } else {
        userSelection.style.display = 'none';
        userIdSelect.required = false;
        userIdSelect.value = '';
    }
}

function filterUsersByRole() {
    const roleId = document.getElementById('role_id').value;
    const userIdSelect = document.getElementById('user_id');
    const options = userIdSelect.querySelectorAll('option');
    
    // Show/hide options based on role
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else {
            const userRoleId = option.getAttribute('data-role');
            option.style.display = userRoleId === roleId ? 'block' : 'none';
        }
    });
}

// Add event listener for role change
document.getElementById('role_id').addEventListener('change', filterUsersByRole);

$(document).ready(function() {
    // Initialize user selection based on current target type
    toggleUserSelection();
    
    $('#editNotificationForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.notifications.update", $notification->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toast_success(response.message);
                    $('#modal-common').modal('hide');
                    location.reload();
                } else {
                    toast_danger(response.message || 'An error occurred');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = '';
                    Object.values(errors).forEach(error => {
                        errorMessage += error[0] + '<br>';
                    });
                    toast_danger(errorMessage);
                } else {
                    toast_danger('An error occurred while updating the notification');
                }
            }
        });
    });
});
</script>
