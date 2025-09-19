/**
 * Voxbay Calling Integration
 * Handles outgoing calls and call log management
 */

class VoxbayCalling {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadTelecallerExtensions();
    }

    bindEvents() {
        // Bind call button events
        $(document).on('click', '.voxbay-call-btn', (e) => {
            e.preventDefault();
            const leadId = $(e.currentTarget).data('lead-id');
            const telecallerId = $(e.currentTarget).data('telecaller-id');
            this.makeCall(leadId, telecallerId);
        });

        // Bind test connection button
        $(document).on('click', '.voxbay-test-connection', (e) => {
            e.preventDefault();
            this.testConnection();
        });
    }

    async makeCall(leadId, telecallerId) {
        try {
            // Validate required parameters
            if (!leadId || !telecallerId) {
                this.showError('Invalid lead or telecaller information');
                return;
            }

            // Show popup with loading state
            this.showCallingPopup();

            // Make API call
            const requestData = {
                lead_id: leadId,
                telecaller_id: telecallerId
            };
            
            console.log('Making API call with data:', requestData);
            
            const response = await fetch('/api/voxbay/outgoing-call', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(requestData)
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            const data = await response.json();
            console.log('Response data:', data);

            if (data.status === 'success') {
                this.hideCallingPopup();
                this.showSuccess('Call initiated successfully!');
                // Refresh call logs if on call logs page
                if (typeof window.refreshCallLogs === 'function') {
                    window.refreshCallLogs();
                }
            } else {
                this.hideCallingPopup();
                this.showError(data.message || 'Failed to initiate call');
            }

        } catch (error) {
            console.error('Call error:', error);
            this.hideCallingPopup();
            this.showError('Network error occurred while making call');
        }
    }

    async testConnection() {
        try {
            const testBtn = $('.voxbay-test-connection');
            const originalText = testBtn.html();
            testBtn.html('<i class="fas fa-spinner fa-spin"></i> Testing...').prop('disabled', true);

            const response = await fetch('/api/voxbay/test-connection');
            const data = await response.json();

            if (data.status === 'success') {
                this.showSuccess('Voxbay connection test successful!');
            } else {
                this.showError(data.message || 'Connection test failed');
            }

        } catch (error) {
            console.error('Connection test error:', error);
            this.showError('Network error occurred during connection test');
        } finally {
            const testBtn = $('.voxbay-test-connection');
            testBtn.html(originalText).prop('disabled', false);
        }
    }

    async loadTelecallerExtensions() {
        // Load telecaller extensions for call buttons
        $('.voxbay-call-btn').each(async (index, element) => {
            const telecallerId = $(element).data('telecaller-id');
            if (telecallerId) {
                try {
                    const response = await fetch(`/api/voxbay/telecaller/${telecallerId}/extension`);
                    const data = await response.json();
                    
                    if (data.status === 'success' && data.data.extension) {
                        $(element).removeClass('disabled').prop('disabled', false);
                        $(element).attr('title', `Extension: ${data.data.extension}`);
                    } else {
                        $(element).addClass('disabled').prop('disabled', true);
                        $(element).attr('title', 'No extension configured');
                    }
                } catch (error) {
                    console.error('Error loading telecaller extension:', error);
                    $(element).addClass('disabled').prop('disabled', true);
                }
            }
        });
    }

    showSuccess(message) {
        // Use existing notification system or create a simple alert
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                timer: 3000
            });
        } else {
            alert(message);
        }
    }

    showError(message) {
        // Use existing notification system or create a simple alert
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        } else {
            alert('Error: ' + message);
        }
    }

    // Show calling popup
    showCallingPopup() {
        // Remove existing popup if any
        this.hideCallingPopup();
        
        // Create popup HTML
        const popupHtml = `
            <div id="voxbay-calling-popup" class="voxbay-calling-popup">
                <div class="voxbay-calling-content">
                    <div class="voxbay-calling-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <h5>Initiating Call...</h5>
                    <p>Please wait while we connect you to the lead.</p>
                </div>
            </div>
        `;
        
        // Add popup to body
        $('body').append(popupHtml);
        
        // Add CSS if not already added
        if (!$('#voxbay-calling-styles').length) {
            const styles = `
                <style id="voxbay-calling-styles">
                    .voxbay-calling-popup {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 9999;
                    }
                    .voxbay-calling-content {
                        background: white;
                        padding: 2rem;
                        border-radius: 10px;
                        text-align: center;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                        max-width: 300px;
                        width: 90%;
                    }
                    .voxbay-calling-spinner {
                        font-size: 2rem;
                        color: #28a745;
                        margin-bottom: 1rem;
                    }
                    .voxbay-calling-content h5 {
                        margin-bottom: 0.5rem;
                        color: #333;
                    }
                    .voxbay-calling-content p {
                        color: #666;
                        margin: 0;
                    }
                </style>
            `;
            $('head').append(styles);
        }
    }

    // Hide calling popup
    hideCallingPopup() {
        $('#voxbay-calling-popup').remove();
    }

    // Static method to refresh call logs
    static refreshCallLogs() {
        if (typeof window.refreshCallLogs === 'function') {
            window.refreshCallLogs();
        } else if ($('.call-logs-table').length) {
            location.reload();
        }
    }
}

// Initialize when document is ready
$(document).ready(function() {
    new VoxbayCalling();
});

// Global function for refreshing call logs
window.refreshCallLogs = function() {
    if ($('.call-logs-table').length) {
        location.reload();
    }
};

// Export for use in other scripts
window.VoxbayCalling = VoxbayCalling;
