<form id="createNotificationForm">
    @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                <textarea class="form-control" id="message" name="message" rows="10" required></textarea>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">Select Type</option>
                    <option value="info">Info</option>
                    <option value="success">Success</option>
                    <option value="warning">Warning</option>
                    <option value="error">Error</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="target_type" class="form-label">Target Type <span class="text-danger">*</span></label>
                <select class="form-control" id="target_type" name="target_type" required onchange="toggleUserSelection()">
                    <option value="">Select Target</option>
                    <option value="all">All Users</option>
                    <option value="all_role">All Role</option>
                    <option value="role">Specific Role</option>
                    <option value="user">Specific User</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6" id="role_selection">
            <div class="mb-3">
                <label for="role_id" class="form-label">Role <span class="text-danger" id="role_required">*</span></label>
                <select class="form-control" id="role_id" name="role_id" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6" id="user_selection" style="display: none;">
            <div class="mb-3">
                <label for="user_id" class="form-label">User</label>
                <select class="form-control" id="user_id" name="user_id">
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" data-role="{{ $user->role_id }}">{{ $user->name }} ({{ $user->role->title ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-send"></i> Send Notification
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
    const roleIdSelect = document.getElementById('role_id');
    const roleRequired = document.getElementById('role_required');
    const roleSelection = document.getElementById('role_selection');
    
    // Handle role selection requirement
    if (targetType === 'all' || targetType === 'all_role') {
        roleIdSelect.required = false;
        roleIdSelect.value = '';
        if (roleRequired) {
            roleRequired.style.display = 'none';
        }
    } else {
        roleIdSelect.required = true;
        if (roleRequired) {
            roleRequired.style.display = 'inline';
        }
    }
    
    // Handle user selection
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
    // TinyMCE will be initialized by the onload event of the script tag
    
    $('#createNotificationForm').on('submit', function(e) {
        e.preventDefault();
        
        // Update TinyMCE content before form submission
        if (typeof tinymce !== 'undefined' && tinymce.get('message')) {
            tinymce.get('message').save();
        }
        
        // Wait a moment for TinyMCE to save
        setTimeout(function() {
            const formData = new FormData($('#createNotificationForm')[0]);
            
            // Ensure message field has content
            const messageValue = formData.get('message') || $('#message').val();
            if (!messageValue || messageValue.trim() === '') {
                toast_danger('Message field is required');
                return;
            }
            
            $.ajax({
                url: '{{ route("admin.notifications.store") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toast_success(response.message);
                        $('#modal-common').modal('hide');
                        // Destroy TinyMCE instance before reload
                        if (typeof tinymce !== 'undefined') {
                            tinymce.remove('#message');
                        }
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
                        const errorMsg = xhr.responseJSON?.message || 'An error occurred while creating the notification';
                        toast_danger(errorMsg);
                    }
                }
            });
        }, 100);
    });
});
</script>

<!-- TinyMCE Script -->
<script>
(function() {
    function initNotificationTinyMCE() {
        if (typeof tinymce === 'undefined') {
            // Load TinyMCE script if not already loaded
            var script = document.createElement('script');
            script.src = '{{ asset("assets/mantis/js/plugins/tinymce/tinymce.min.js") }}';
            script.onload = function() {
                initializeNotificationEditor();
            };
            document.head.appendChild(script);
        } else {
            initializeNotificationEditor();
        }
    }
    
    function initializeNotificationEditor() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setupTinyMCE();
            });
        } else {
            // Use setTimeout to ensure the textarea is in the DOM (especially for modals)
            setTimeout(setupTinyMCE, 100);
        }
    }
    
    function setupTinyMCE() {
        var messageField = document.getElementById('message');
        if (!messageField) return;
        
        // Check if editor already exists
        if (tinymce.get('message')) {
            tinymce.remove('#message');
        }
        
        tinymce.init({
            selector: '#message',
            apiKey: 'n0hngmr9gekztcyuy5ie1b47d580eanh8x0zb6lmup11b66a',
            height: 300,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    }
    
    // Initialize when script loads
    initNotificationTinyMCE();
})();
</script>
