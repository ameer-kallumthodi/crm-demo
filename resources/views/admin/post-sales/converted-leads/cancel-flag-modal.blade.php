<form id="cancelFlagForm">
    @csrf
    <div class="modal-body">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="ti ti-user me-2"></i>Student Details</h6>
            </div>
            <div class="card-body">
                <div class="row gy-2">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Name</small>
                        <span class="fw-semibold">{{ $convertedLead->name }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Phone</small>
                        <span class="fw-semibold">{{ \App\Helpers\PhoneNumberHelper::display($convertedLead->code, $convertedLead->phone) }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-danger text-uppercase">{{ $convertedLead->status }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-warning d-flex align-items-start">
            <i class="ti ti-alert-triangle me-2 mt-1"></i>
            <div>
                <strong>Note:</strong> Use this option only after confirming the student cancellation with all stakeholders.
                This flag helps reporting teams understand whether the cancellation is final.
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold d-block">Is cancellation confirmed?</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_cancelled" id="is_cancelled_yes" value="1" {{ $convertedLead->is_cancelled ? 'checked' : '' }}>
                <label class="form-check-label" for="is_cancelled_yes">Yes</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_cancelled" id="is_cancelled_no" value="0" {{ !$convertedLead->is_cancelled ? 'checked' : '' }}>
                <label class="form-check-label" for="is_cancelled_no">No</label>
            </div>
        </div>

        <div class="mb-0">
            <span class="badge {{ $convertedLead->is_cancelled ? 'bg-danger' : 'bg-secondary' }}">
                Current flag: {{ $convertedLead->is_cancelled ? 'Confirmed' : 'Cancelled' }}
            </span>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger">Update Flag</button>
    </div>
</form>

<script>
const cancelFlagSubmitUrl = "{{ route('admin.post-sales.converted-leads.cancel-flag-submit', $convertedLead->id) }}";
$(function () {
    $('#cancelFlagForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: cancelFlagSubmitUrl,
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
                $('#ajax_modal').modal('hide');
                if (typeof showToast === 'function') {
                    showToast(response.message, 'success');
                } else if (typeof toast_success === 'function') {
                    toast_success(response.message);
                } else {
                    alert(response.message);
                }
                if ($.fn.DataTable.isDataTable('#postSalesConvertedTable')) {
                    $('#postSalesConvertedTable').DataTable().ajax.reload();
                } else {
                    location.reload();
                }
            },
            error: function (xhr) {
                let message = 'Unable to update cancellation flag.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                if (typeof showToast === 'function') {
                    showToast(message, 'error');
                } else if (typeof toast_error === 'function') {
                    toast_error(message);
                } else {
                    alert(message);
                }
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>

