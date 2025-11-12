<form id="assignForm">
    @csrf
    <div class="row g-3">
        <div class="col-lg-12">
            <div class="p-1">
                <label for="telecaller_id" class="form-label">Assign To Telecaller</label>
                <select class="form-control" name="telecaller_id" id="telecaller_id" required>
                    <option value="">Select Telecaller</option>
                    @foreach ($telecallers as $telecaller)
                    <option value="{{ $telecaller->id }}">{{ $telecaller->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="p-1">
                <label for="marketing_remarks" class="form-label">Marketing Remarks (Optional)</label>
                <textarea class="form-control" name="marketing_remarks" id="marketing_remarks" rows="3" placeholder="Enter remarks if needed">{{ old('marketing_remarks', $marketingLead->remarks ?? '') }}</textarea>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="submit" id="assign_btn">Assign</button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#assignForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var marketingLeadId = {{ $marketingLeadId }};
        var url = '{{ route("admin.marketing.assign-to-telecaller", ":id") }}'.replace(':id', marketingLeadId);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toast_success === 'function') {
                        toast_success(response.message);
                    } else {
                        alert(response.message);
                    }
                    $('#ajax_modal').modal('hide');
                    // Reload the DataTable
                    if (typeof marketingLeadsTable !== 'undefined' && marketingLeadsTable) {
                        marketingLeadsTable.ajax.reload();
                    } else {
                        location.reload();
                    }
                }
            },
            error: function(xhr) {
                var errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                if (typeof alert_modal_error === 'function') {
                    alert_modal_error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        });
    });
});
</script>

