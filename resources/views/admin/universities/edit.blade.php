<div class="container p-2">
    <form id="universityEditForm" action="{{ route('admin.universities.update', $edit_data->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" value="{{ $edit_data->title }}" placeholder="Enter University Title" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="3" placeholder="Enter Description">{{ $edit_data->description }}</textarea>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="ug_amount">UG Amount <span class="text-danger">*</span></label>
                    <input type="number" name="ug_amount" class="form-control" id="ug_amount" step="0.01" min="0" value="{{ $edit_data->ug_amount }}" placeholder="Enter UG Amount" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="pg_amount">PG Amount <span class="text-danger">*</span></label>
                    <input type="number" name="pg_amount" class="form-control" id="pg_amount" step="0.01" min="0" value="{{ $edit_data->pg_amount }}" placeholder="Enter PG Amount" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $edit_data->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
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
    $('#universityEditForm').on('submit', function(e) {
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
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                // Close modal
                $('#small_modal').modal('hide');
                
                // Show success message
                toast_success('University updated successfully!');
                
                // Redirect to the index page
                setTimeout(() => {
                    window.location.href = '{{ route("admin.universities.index") }}';
                }, 1000);
            },
            error: function(xhr) {
                console.log('Error response:', xhr);
                console.log('Status:', xhr.status);
                console.log('Response:', xhr.responseText);
                
                let errorMessage = 'An error occurred while updating the university.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                } else if (xhr.status === 422) {
                    errorMessage = 'Validation failed. Please check your input.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again.';
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
