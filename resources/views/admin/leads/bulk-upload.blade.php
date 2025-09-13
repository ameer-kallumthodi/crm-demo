<div class="p-3">
    <form action="{{ route('leads.bulk-upload.submit') }}" method="post" enctype="multipart/form-data" id="bulkUploadForm">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="excel_file">Select Excel File <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls" required />
                    <small class="text-muted">Supported formats: .xlsx, .xls (Max size: 10MB)</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_source_id">Lead Source <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_source_id" id="lead_source_id" required>
                        <option value="">Select Lead Source</option>
                        @foreach($leadSources as $source)
                            <option value="{{ $source->id }}">{{ $source->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_status_id">Lead Status <span class="text-danger">*</span></label>
                    <select class="form-select" name="lead_status_id" id="lead_status_id" required>
                        <option value="">Select Lead Status</option>
                        @foreach($leadStatuses as $status)
                            <option value="{{ $status->id }}">{{ $status->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="course_id">Course <span class="text-danger">*</span></label>
                    <select class="form-select" name="course_id" id="course_id" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="team_id">Team <span class="text-danger">*</span></label>
                    <select class="form-select" name="team_id" id="team_id" required>
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="assign_to_all" name="assign_to_all" value="1">
                        <label class="form-check-label" for="assign_to_all">
                            <strong>Assign to all Telecallers</strong> - Leads will be assigned to all team telecallers equally
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-12" id="telecaller-selection">
                <div class="mb-3">
                    <label class="form-label" for="telecallers">Assign to Telecallers <span class="text-danger">*</span></label>
                    <select class="form-select" name="telecallers[]" id="telecallers" multiple>
                        @foreach($telecallers as $telecaller)
                            <option value="{{ $telecaller->id }}">{{ $telecaller->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple telecallers</small>
                </div>
            </div>

            <div class="col-md-12">
                <div class="alert alert-info">
                    <h6>Excel Format Guide:</h6>
                    <p class="mb-2"><strong>Required columns:</strong> Name, Phone, Remarks</p>
                    <p class="mb-0"><strong>Note:</strong> Duplicate phone numbers will be automatically skipped. Remarks field is optional.</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Upload & Process</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Handle team selection to load telecallers
    $('#team_id').on('change', function() {
        const teamId = $(this).val();
        const telecallerSelect = $('#telecallers');
        
        if (teamId) {
            $.get('{{ route("leads.telecallers-by-team") }}', { team_id: teamId })
                .done(function(data) {
                    telecallerSelect.empty();
                    $.each(data.telecallers, function(index, telecaller) {
                        telecallerSelect.append(
                            $('<option></option>').val(telecaller.id).text(telecaller.name)
                        );
                    });
                })
                .fail(function() {
                    console.error('Failed to load telecallers');
                });
        } else {
            telecallerSelect.empty();
        }
    });

    // Handle assign to all checkbox
    $('#assign_to_all').on('change', function() {
        const isChecked = $(this).is(':checked');
        const telecallerSelection = $('#telecaller-selection');
        
        if (isChecked) {
            telecallerSelection.hide();
            $('#telecallers').prop('required', false);
        } else {
            telecallerSelection.show();
            $('#telecallers').prop('required', true);
        }
    });

    // Form submission with loading state
    $('#bulkUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const form = $(this);
        const formData = new FormData(this);
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2"></i> Processing...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Close modal
                $('#ajax_modal').modal('hide');
                
                // Show success message
                if (response.message) {
                    toast_success(response.message);
                } else {
                    toast_success('Leads uploaded successfully!');
                }
                
                // Reload the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while uploading leads.';
                
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