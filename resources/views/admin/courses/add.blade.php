<div class="container p-2">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="courseAddForm" action="{{ route('admin.courses.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" 
                           id="title" placeholder="Enter Course Title" required>
                    <div class="invalid-feedback" id="title-error"></div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Submit</button>
    </form>

    <script>
    $(document).ready(function() {
        $('#courseAddForm').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toast_success(response.message);
                        
                        // Close modal and reload page after a short delay
                        $('#small_modal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Validation errors
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            var input = $('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(messages[0]);
                        });
                    } else {
                        // Other errors
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
    </script>
</div>
