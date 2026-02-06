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

    <form id="universityCourseAddForm" action="{{ route('admin.university-courses.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="university_id">University <span class="text-danger">*</span></label>
                    <select name="university_id" class="form-control" id="university_id" required>
                        <option value="">Select University</option>
                        @foreach($universities as $university)
                            <option value="{{ $university->id }}">{{ $university->title }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="university_id-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" 
                           id="title" placeholder="Enter Course Title" required>
                    <div class="invalid-feedback" id="title-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="amount">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" 
                           id="amount" placeholder="0.00" step="0.01" min="0" required>
                    <div class="invalid-feedback" id="amount-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="course_type">Course Type</label>
                    <select name="course_type" class="form-control" id="course_type">
                        <option value="">Select Course Type</option>
                        <option value="UG">UG (Undergraduate)</option>
                        <option value="PG">PG (Postgraduate)</option>
                    </select>
                    <div class="invalid-feedback" id="course_type-error"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <div class="form-check mt-4">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" class="form-control" 
                              id="description" rows="3" placeholder="Enter Course Description"></textarea>
                    <div class="invalid-feedback" id="description-error"></div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Submit</button>
    </form>

    <script>
    $(document).ready(function() {
        $('#universityCourseAddForm').on('submit', function(e) {
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
