<div class="container p-2">
    <form action="{{ route('leads.bulk-upload.post') }}" method="post" enctype="multipart/form-data" id="bulkUploadForm">
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
                    <label class="form-label" for="default_lead_status_id">Default Lead Status</label>
                    <select class="form-select" name="default_lead_status_id" id="default_lead_status_id">
                        <option value="">Select Default Status</option>
                        @foreach($leadStatuses as $status)
                            <option value="{{ $status->id }}">{{ $status->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="default_lead_source_id">Default Lead Source</label>
                    <select class="form-select" name="default_lead_source_id" id="default_lead_source_id">
                        <option value="">Select Default Source</option>
                        @foreach($leadSources as $source)
                            <option value="{{ $source->id }}">{{ $source->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="default_course_id">Default Course</label>
                    <select class="form-select" name="default_course_id" id="default_course_id">
                        <option value="">Select Default Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="telecallers">Assign to Telecallers <span class="text-danger">*</span></label>
                    <select class="form-select" name="telecallers[]" id="telecallers" multiple required>
                        @foreach($telecallers as $telecaller)
                            <option value="{{ $telecaller->id }}">{{ $telecaller->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple telecallers</small>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" value="1">
                        <label class="form-check-label" for="skip_duplicates">
                            Skip duplicate entries (based on phone number)
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="alert alert-info">
                    <h6>Excel Format Guide:</h6>
                    <p class="mb-2">Required columns: Name, Phone, Email, Gender, Age, Place</p>
                    <p class="mb-0">Optional columns: WhatsApp, Qualification, Address, Course, Remarks</p>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Upload & Process</button>
    </form>
</div>

<script>
$(document).ready(function() {
    // Form submission with loading state
    $('#bulkUploadForm').on('submit', function(e) {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="ti ti-loader-2"></i> Processing...');
        
        // Re-enable after 30 seconds as fallback
        setTimeout(() => {
            submitBtn.prop('disabled', false);
            submitBtn.html(originalText);
        }, 30000);
    });
});
</script>