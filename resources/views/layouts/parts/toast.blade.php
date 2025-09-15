<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <img src="{{ asset('assets/images/logo-sm.png') }}" class="rounded me-2" alt="..." height="16">
            <strong class="me-auto">{{ config('app.name', '') }}</strong>
            <small class="text-muted">just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <!-- Toast message will be inserted here -->
        </div>
    </div>
</div>

<script>
    // Toast notification functions
    function showToast(message, type = 'info') {
        const toastElement = document.getElementById('liveToast');
        const toastBody = toastElement.querySelector('.toast-body');
        const toastHeader = toastElement.querySelector('.toast-header');
        
        // Set message
        toastBody.textContent = message;
        
        // Set type-specific styling
        toastHeader.className = 'toast-header';
        if (type === 'success') {
            toastHeader.classList.add('bg-success-subtle');
        } else if (type === 'error') {
            toastHeader.classList.add('bg-danger-subtle');
        } else if (type === 'warning') {
            toastHeader.classList.add('bg-warning-subtle');
        } else {
            toastHeader.classList.add('bg-info-subtle');
        }
        
        // Show toast
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }
    
    // Auto-hide flash messages
    @if(session('message_success'))
        showToast('{{ session('message_success') }}', 'success');
    @endif
    
    @if(session('message_danger'))
        showToast('{{ session('message_danger') }}', 'error');
    @endif
    
    @if(session('message_warning'))
        showToast('{{ session('message_warning') }}', 'warning');
    @endif
    
    @if(session('message_info'))
        showToast('{{ session('message_info') }}', 'info');
    @endif
</script>
