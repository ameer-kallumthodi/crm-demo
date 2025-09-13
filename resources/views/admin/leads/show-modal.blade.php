<div class="p-0">
    <!-- Modal Body -->
    <div class="modal-body p-0">
        <div class="row g-0">
            <!-- Left Column - Lead Information -->
            <div class="col-md-6 p-4">
                <!-- Profile Section -->
                <div class="text-center mb-4">
                    <div class="avatar-lg mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span class="text-white fw-bold" style="font-size: 2rem;">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
                    </div>
                    <h4 class="mb-1">{{ $lead->title }}</h4>
                    <p class="text-muted mb-0">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</p>
                </div>

                <!-- Lead Info Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 text-center">
                                <h6 class="card-title text-muted mb-1">Place</h6>
                                <p class="card-text mb-0">{{ $lead->place ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 text-center">
                                <h6 class="card-title text-muted mb-1">Qualification</h6>
                                <p class="card-text mb-0">{{ $lead->qualification ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lead Status Section -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="card-title text-muted mb-3">Lead Status</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-bold">Status:</span>
                                    <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusBadgeClass($lead->leadStatus ? $lead->leadStatus->id : 0) }} px-3 py-2">
                                        {{ $lead->leadStatus ? $lead->leadStatus->title : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Date:</span>
                                    <span class="fw-bold">{{ $lead->created_at ? $lead->created_at->format('d-m-Y') : 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Time:</span>
                                    <span class="fw-bold">{{ $lead->created_at ? $lead->created_at->format('h:i A') : 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Updated Date:</span>
                                    <span class="fw-bold">{{ $lead->updated_at ? $lead->updated_at->format('d-m-Y') : 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Updated Time:</span>
                                    <span class="fw-bold">{{ $lead->updated_at ? $lead->updated_at->format('h:i A') : 'N/A' }}</span>
                                </div>
                            </div>
                            @if($lead->remarks)
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Remarks:</span>
                                    <span class="fw-bold text-end">{{ $lead->remarks }}</span>
                                </div>
                            </div>
                            @endif
                            @if($lead->leadStatus && $lead->leadStatus->id == 7)
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Reason:</span>
                                    <span class="badge bg-danger px-3 py-2">{{ $lead->remarks ?: 'Not specified' }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mt-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <h6 class="card-title text-muted mb-1">Source</h6>
                                    <p class="card-text mb-0">{{ $lead->leadSource ? $lead->leadSource->title : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <h6 class="card-title text-muted mb-1">Course</h6>
                                    <p class="card-text mb-0">{{ $lead->course ? $lead->course->title : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <h6 class="card-title text-muted mb-1">Team</h6>
                                    <p class="card-text mb-0">{{ $lead->team ? $lead->team->name : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <h6 class="card-title text-muted mb-1">Telecaller</h6>
                                    <p class="card-text mb-0">{{ $lead->telecaller ? $lead->telecaller->name : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Lead History -->
            <div class="col-md-6 p-4" style="background-color: #f8f9fa; border-left: 1px solid #dee2e6;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">LEAD HISTORY</h5>
                    <button type="button" class="btn btn-primary btn-sm" onclick="printLeadHistory('{{ $lead->title }}')">
                        <i class="ti ti-printer"></i> Print
                    </button>
                </div>
                
                @if($lead->leadActivities && $lead->leadActivities->count() > 0)
                    <div class="timeline">
                        @foreach($lead->leadActivities as $activity)
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker bg-{{ $activity->activity_type == 'disqualified' ? 'danger' : ($activity->activity_type == 'converted' ? 'success' : 'info') }}"></div>
                            <div class="timeline-content">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0 text-uppercase">
                                                {{ str_replace('_', ' ', $activity->activity_type) }}
                                                @if($activity->activity_type == 'disqualified')
                                                    : {{ $activity->created_at ? $activity->created_at->format('d-m-Y h:i A') : 'N/A' }}
                                                @endif
                                            </h6>
                                            <small class="text-muted">{{ $activity->created_at ? $activity->created_at->format('d-m-Y') : 'N/A' }}</small>
                                        </div>
                                        <p class="card-text mb-2">{{ $activity->description }}</p>
                                        @if($activity->activity_type == 'disqualified' && $activity->description)
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Reason:</span>
                                            <span class="badge bg-danger">{{ $activity->description }}</span>
                                        </div>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">Updated By: {{ $activity->createdBy ? $activity->createdBy->name : 'N/A' }}</small>
                                            <small class="text-muted">{{ $activity->created_at ? $activity->created_at->format('d-m-Y h:i A') : 'N/A' }}</small>
                                        </div>
                                        @if($activity->updated_at && $activity->updated_at != $activity->created_at)
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <small class="text-muted">Last Updated:</small>
                                            <small class="text-muted">{{ $activity->updated_at->format('d-m-Y h:i A') }}</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-history" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-3">No activity history found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<script>
function printLeadHistory(leadName) {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Get the lead history content
    const leadHistoryContent = document.querySelector('.timeline').outerHTML;
    
    // Create the print content
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Lead History - ${leadName}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .print-header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .print-header h1 { margin: 0; color: #333; }
                .print-header p { margin: 5px 0 0 0; color: #666; }
                .timeline { position: relative; padding-left: 30px; }
                .timeline::before { content: ''; position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: #dee2e6; }
                .timeline-item { position: relative; margin-bottom: 20px; }
                .timeline-marker { position: absolute; left: -22px; top: 8px; width: 12px; height: 12px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px #dee2e6; }
                .timeline-content { margin-left: 0; }
                .card { border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 15px; }
                .card-body { padding: 15px; }
                .card-title { font-weight: bold; margin-bottom: 10px; }
                .card-text { margin-bottom: 10px; }
                .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                .bg-danger { background-color: #dc3545; color: white; }
                .bg-success { background-color: #28a745; color: white; }
                .bg-info { background-color: #17a2b8; color: white; }
                .text-muted { color: #6c757d; }
                .d-flex { display: flex; }
                .justify-content-between { justify-content: space-between; }
                .align-items-center { align-items: center; }
                .mb-2 { margin-bottom: 8px; }
                .mt-2 { margin-top: 8px; }
                .small { font-size: 12px; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h1>Lead History Report</h1>
                <p>Lead Name: ${leadName}</p>
                <p>Generated on: ${new Date().toLocaleDateString()}</p>
            </div>
            ${leadHistoryContent}
        </body>
        </html>
    `;
    
    // Write content to print window
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Wait for content to load, then print
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 8px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    margin-left: 0;
}

.avatar-lg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Print styles removed - handled by JavaScript function */
</style>
