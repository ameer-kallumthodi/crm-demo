<form id="assignPostSalesForm">
    @csrf
    <div class="modal-body">
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="ti ti-user me-2"></i>Student</h6>
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
                        <small class="text-muted d-block">Current assignment</small>
                        <span class="fw-semibold">{{ $convertedLead->postSalesUser?->name ?? 'Unassigned' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="post_sales_user_id" class="form-label fw-semibold">Assign to Post-Sales <span class="text-danger">*</span></label>
            <select class="form-select" name="post_sales_user_id" id="post_sales_user_id" required>
                <option value="">Select Post-Sales User</option>
                @foreach($postSalesUsers as $user)
                    <option value="{{ $user->id }}" {{ $convertedLead->post_sales_user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Assign</button>
    </div>
</form>

<script>
(function () {
    const assignPostSalesSubmitUrl = "{{ route('admin.post-sales.converted-leads.assign-submit', $convertedLead->id) }}";
    $(function () {
        $('#assignPostSalesForm').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

            $.ajax({
                url: assignPostSalesSubmitUrl,
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
                        var table = $('#postSalesConvertedTable').DataTable();
                        table.ajax.reload(null, false);
                    } else {
                        location.reload();
                    }
                },
                error: function (xhr) {
                    let message = 'Unable to assign.';
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
})();
</script>
