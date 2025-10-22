<form id="approveForm" action="{{ route('leads.update-registration-status', $lead->id) }}" method="post">
    @csrf
    <input type="hidden" name="status" value="approved">
    <div class="mb-3">
        <label class="form-label">Remark (optional)</label>
        <textarea class="form-control" name="remark" rows="3" placeholder="Enter any notes (optional)"></textarea>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-success" onclick="submitApprove()">Approve</button>
    </div>
</form>
<script>
function submitApprove() {
    const form = document.getElementById('approveForm');
    
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    }).then(r=>r.json()).then(d=>{
        if(d.success){
            toast_success(d.message);
            setTimeout(()=>window.location.reload(), 800);
        } else {
            toast_error(d.message || 'Failed to approve');
        }
    }).catch(()=>toast_error('Failed to approve'));
}
</script>
