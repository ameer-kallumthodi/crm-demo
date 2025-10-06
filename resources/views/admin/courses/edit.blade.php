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

    <form id="courseEditForm" action="{{ route('admin.courses.update', $edit_data->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" 
                           id="title" value="{{ $edit_data->title }}" 
                           placeholder="Enter Course Title" readonly required>
                    <div class="invalid-feedback" id="title-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="code">Code</label>
                    <input type="text" name="code" class="form-control" 
                           id="code" value="{{ $edit_data->code }}" 
                           placeholder="Enter Course Code">
                    <div class="invalid-feedback" id="code-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="amount">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" 
                           id="amount" value="{{ $edit_data->amount }}"
                           placeholder="0.00" step="0.01" min="0" required>
                    <div class="invalid-feedback" id="amount-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="hod_number">HOD Number</label>
                    <input type="text" name="hod_number" class="form-control" 
                           id="hod_number" value="{{ $edit_data->hod_number ?? '' }}" 
                           placeholder="Enter HOD Number">
                    <div class="invalid-feedback" id="hod_number-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ $edit_data->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Update</button>
    </form>

    <script>
    $(document).ready(function() {
        $('#courseEditForm').on('submit', function(e) {
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
