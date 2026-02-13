<div class="container p-2">
    <form id="departmentEditForm" action="{{ route('admin.departments.update', $department->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Department Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" value="{{ $department->title }}" placeholder="Enter Department Title" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control" id="status" required>
                        <option value="1" {{ $department->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$department->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Update</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('#departmentEditForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2 spin"></i> Updating...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Close modal
                $('#small_modal').modal('hide');
                
                // Show success message
                toast_success('Department updated successfully!');
                
                // Redirect to the index page
                setTimeout(() => {
                    window.location.href = '{{ route("admin.departments.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the Department.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                }
                
                toast_danger(errorMessage);
                
                // Re-enable submit button
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
});
</script>
