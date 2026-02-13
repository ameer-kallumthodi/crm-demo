<div class="container p-2">
    <form id="b2bServiceEditForm" action="{{ route('admin.b2b-services.update', $edit_data->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Service Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" value="{{ $edit_data->title }}" placeholder="Enter Service Title" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control" id="status" required>
                        <option value="active" {{ $edit_data->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $edit_data->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
    $('#b2bServiceEditForm').on('submit', function(e) {
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
                toast_success('B2B Service updated successfully!');
                
                // Redirect to the index page
                setTimeout(() => {
                    window.location.href = '{{ route("admin.b2b-services.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the B2B Service.';
                
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
