@extends('layouts.mantis')

@section('title', 'Course Documents Management')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Course Documents Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Course Documents</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        @foreach($courses as $course)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">{{ $course->title }}</h6>
            </div>
            <div class="card-body">
                <form id="courseDocumentForm{{ $course->id }}" class="course-document-form">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    
                    <div class="row">
                        @php
                            $documentTypes = [
                                'birth_certificate' => 'Birth Certificate',
                                'passport_photo' => 'Passport Photo',
                                'aadhaar_front' => 'Aadhaar Front',
                                'aadhaar_back' => 'Aadhaar Back',
                                'signature' => 'Signature'
                            ];
                            
                            $existingRequirements = $course->courseDocuments->pluck('is_required', 'document_type')->toArray();
                        @endphp
                        
                        @foreach($documentTypes as $type => $name)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input document-checkbox" 
                                       type="checkbox" 
                                       id="course_{{ $course->id }}_{{ $type }}" 
                                       name="document_requirements[{{ $loop->index }}][document_type]" 
                                       value="{{ $type }}"
                                       {{ isset($existingRequirements[$type]) && $existingRequirements[$type] ? 'checked' : '' }}
                                       data-course-id="{{ $course->id }}">
                                <input type="hidden" name="document_requirements[{{ $loop->index }}][is_required]" value="0">
                                <label class="form-check-label" for="course_{{ $course->id }}_{{ $type }}">
                                    {{ $name }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-success btn-sm" onclick="updateCourseRequirements({{ $course->id }})">
                            <i class="ti ti-check"></i> Update Requirements
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update hidden input when checkbox is changed
    $('.document-checkbox').on('change', function() {
        const courseId = $(this).data('course-id');
        const form = $(`#courseDocumentForm${courseId}`);
        const checkboxes = form.find('.document-checkbox');
        
        checkboxes.each(function(index) {
            const hiddenInput = form.find(`input[name="document_requirements[${index}][is_required]"]`);
            hiddenInput.val($(this).is(':checked') ? '1' : '0');
        });
    });
});

function updateCourseRequirements(courseId) {
    const form = $(`#courseDocumentForm${courseId}`);
    const checkboxes = form.find('.document-checkbox:checked');
    
    // Build the data object manually to only include checked items
    const data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        course_id: courseId,
        document_requirements: []
    };
    
    checkboxes.each(function(index) {
        data.document_requirements.push({
            document_type: $(this).val(),
            is_required: true
        });
    });
    
    // Show loading state
    const submitBtn = form.find('button[type="button"]');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true);
    submitBtn.html('<i class="ti ti-loader-2 spin"></i> Updating...');
    
    $.ajax({
        url: '{{ route("admin.course-documents.bulk-update") }}',
        type: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                toast_success('Course document requirements updated successfully!');
            } else {
                toast_danger(response.message);
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while updating the course document requirements.';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                errorMessage = Object.values(errors).flat().join('<br>');
            }
            
            toast_danger(errorMessage);
        },
        complete: function() {
            // Re-enable submit button
            submitBtn.prop('disabled', false);
            submitBtn.html(originalText);
        }
    });
}
</script>
@endpush
