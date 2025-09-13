<div class="p-3">
    <div class="text-center mb-4">
        <i class="ti ti-alert-triangle text-warning" style="font-size: 3rem;"></i>
        <h4 class="mt-3">Confirm Delete</h4>
        <p class="text-muted">Are you sure you want to delete this lead? This action cannot be undone.</p>
    </div>
    
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="card-title">Lead Details:</h6>
            <p class="mb-1"><strong>Name:</strong> {{ $lead->title }}</p>
            <p class="mb-1"><strong>Phone:</strong> {{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</p>
            @if($lead->email)
                <p class="mb-1"><strong>Email:</strong> {{ $lead->email }}</p>
            @endif
            @if($lead->leadStatus)
                <p class="mb-1"><strong>Status:</strong> {{ $lead->leadStatus->title }}</p>
            @endif
            @if($lead->leadSource)
                <p class="mb-0"><strong>Source:</strong> {{ $lead->leadSource->title }}</p>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
            <i class="ti ti-trash"></i> Delete Lead
        </button>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#confirmDeleteBtn').on('click', function() {
        const deleteBtn = $(this);
        const originalText = deleteBtn.html();
        
        deleteBtn.prop('disabled', true);
        deleteBtn.html('<i class="ti ti-loader-2"></i> Deleting...');
        
        $.ajax({
            url: '{{ route("leads.destroy", $lead->id) }}',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toast_success(response.message);
                    $('#ajax_modal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while deleting the lead. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                toast_danger(errorMessage);
                
                deleteBtn.prop('disabled', false);
                deleteBtn.html(originalText);
            }
        });
    });
});
</script>
