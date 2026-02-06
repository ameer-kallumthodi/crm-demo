<form id="rejectForm" action="{{ route('leads.update-registration-status', $lead->id) }}" method="post">
    @csrf
    <input type="hidden" name="status" value="rejected">
    <div class="mb-3">
        <label class="form-label">Remark <span class="text-danger">*</span></label>
        <textarea class="form-control" name="remark" rows="3" placeholder="Provide reason for rejection" required></textarea>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-danger" onclick="submitReject()">Reject</button>
    </div>
</form>
<script>
function submitReject() {
    const form = document.getElementById('rejectForm');
    const remark = form.querySelector('textarea[name=remark]').value.trim();
    if(!remark){ toast_error('Remark is required.'); return; }
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    }).then(r=>r.json()).then(d=>{
        if(d.success){
            toast_success(d.message);
            setTimeout(()=>window.location.reload(), 800);
        } else {
            toast_error(d.message || 'Failed to reject');
        }
    }).catch(()=>toast_error('Failed to reject'));
}
</script>
