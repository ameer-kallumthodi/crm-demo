<div class="p-0">
    <!-- Modal Body -->
    <div class="modal-body p-0">
        <div class="row g-0">
            <!-- Left Column - Marketing Lead Information -->
            <div class="col-md-6 p-4">
                <!-- Profile Section -->
                <div class="text-center mb-4">
                    <div class="avatar-lg mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span class="text-white fw-bold" style="font-size: 2rem;">{{ strtoupper(substr($marketingLead->lead_name, 0, 1)) }}</span>
                    </div>
                    <h4 class="mb-1">{{ $marketingLead->lead_name }}</h4>
                    <p class="text-muted mb-0">{{ $marketingLead->code }} {{ $marketingLead->phone }}</p>
                </div>

                <!-- Marketing Lead Info Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 text-center">
                                <h6 class="card-title text-muted mb-1">Date of Visit</h6>
                                <p class="card-text mb-0">{{ $marketingLead->date_of_visit ? $marketingLead->date_of_visit->format('d-m-Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 text-center">
                                <h6 class="card-title text-muted mb-1">Location</h6>
                                <p class="card-text mb-0">{{ $marketingLead->location ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Marketing Lead Details Section -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="card-title text-muted mb-3">Marketing Lead Details</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fw-bold">Lead Type:</span>
                                    <span class="badge bg-info px-3 py-2">{{ $marketingLead->lead_type }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">BDE Name:</span>
                                    <span class="fw-bold">{{ $marketingLead->marketingBde ? $marketingLead->marketingBde->name : 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">House Number:</span>
                                    <span class="fw-bold">{{ $marketingLead->house_number ?: 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">WhatsApp:</span>
                                    <span class="fw-bold">{{ $marketingLead->whatsapp ? ($marketingLead->whatsapp_code . ' ' . $marketingLead->whatsapp) : 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Created Date:</span>
                                    <span class="fw-bold">{{ $marketingLead->created_at ? $marketingLead->created_at->format('d-m-Y') : 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Created Time:</span>
                                    <span class="fw-bold">{{ $marketingLead->created_at ? $marketingLead->created_at->format('h:i A') : 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Assignment Status:</span>
                                    <span class="badge {{ $marketingLead->is_telecaller_assigned ? 'bg-success' : 'bg-warning' }} px-3 py-2">
                                        {{ $marketingLead->is_telecaller_assigned ? 'Assigned' : 'Not Assigned' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Telecaller Name:</span>
                                    <span class="fw-bold">{{ ($relatedLead && $relatedLead->telecaller) ? $relatedLead->telecaller->name : 'N/A' }}</span>
                                </div>
                            </div>
                            @if($marketingLead->remarks)
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Marketing Remarks:</span>
                                    <span class="fw-bold text-end">{{ $marketingLead->remarks }}</span>
                                </div>
                            </div>
                            @endif
                            @if($marketingLead->address)
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-muted">Address:</span>
                                    <span class="fw-bold text-end">{{ $marketingLead->address }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Interested Courses -->
                @if($marketingLead->interested_courses && count($marketingLead->interested_courses) > 0)
                <div class="mt-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="card-title text-muted mb-3">Interested Courses</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($marketingLead->interested_courses as $course)
                                    <span class="badge bg-secondary px-3 py-2">{{ $course }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Lead Details (if assigned) -->
                @if($relatedLead)
                <div class="mt-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <h6 class="card-title text-muted mb-1">Lead Status</h6>
                                    <p class="card-text mb-0">
                                        @if($relatedLead->leadStatus)
                                            <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusBadgeClass($relatedLead->leadStatus->id) }}">
                                                {{ $relatedLead->leadStatus->title }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <h6 class="card-title text-muted mb-1">Telecaller</h6>
                                    <p class="card-text mb-0">{{ $relatedLead->telecaller ? $relatedLead->telecaller->name : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @if($relatedLead->remarks)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3">
                                    <h6 class="card-title text-muted mb-2">Telecaller Remarks</h6>
                                    <p class="card-text mb-0">{{ $relatedLead->remarks }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($relatedLead->marketing_remarks)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3">
                                    <h6 class="card-title text-muted mb-2">Marketing Remarks (from Lead)</h6>
                                    <p class="card-text mb-0">{{ $relatedLead->marketing_remarks }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Lead History -->
            <div class="col-md-6 p-4" style="background-color: #f8f9fa; border-left: 1px solid #dee2e6;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">LEAD HISTORY</h5>
                    <button type="button" class="btn btn-primary btn-sm" onclick="printMarketingLeadHistory('{{ $marketingLead->lead_name }}')">
                        <i class="ti ti-printer"></i> Print
                    </button>
                </div>
                
                @if($activities && $activities->count() > 0)
                    <!-- Hidden timeline for printing (contains all activities) -->
                    <div class="timeline print-timeline" style="display: none;">
                        @foreach($activities as $activity)
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker bg-{{ $activity->activity_type == 'disqualified' ? 'danger' : ($activity->activity_type == 'converted' ? 'success' : 'info') }}"></div>
                            <div class="timeline-content">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0 text-uppercase">
                                                @switch($activity->activity_type)
                                                    @case('status_change')
                                                        Status Changed
                                                        @break
                                                    @case('bulk_upload')
                                                        Lead Created
                                                        @break
                                                    @case('reassign')
                                                        Lead Reassigned
                                                        @break
                                                    @case('update')
                                                        Lead Updated
                                                        @break
                                                    @case('marketing_lead_assigned')
                                                        Marketing Lead Assigned
                                                        @break
                                                    @default
                                                        {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                                @endswitch
                                                @if($activity->activity_type == 'disqualified')
                                                    : {{ $activity->created_at ? $activity->created_at->format('d-m-Y h:i A') : 'N/A' }}
                                                @endif
                                            </h6>
                                            <small class="text-muted">{{ $activity->created_at ? $activity->created_at->format('d-m-Y') : 'N/A' }}</small>
                                        </div>
                                        <p class="card-text mb-2">{{ $activity->description }}</p>
                                        @if($activity->reason)
                                        <div class="mt-2">
                                            <span class="text-muted fw-semibold">Reason:</span>
                                            <span class="badge bg-info ms-2">{{ $activity->reason }}</span>
                                        </div>
                                        @endif
                                        @if($activity->lead_status_id == 2 && $activity->followup_date)
                                        <div class="mt-2">
                                            <span class="text-muted fw-semibold">Followup Date:</span>
                                            <span class="badge bg-warning ms-2">{{ $activity->followup_date->format('d M Y') }}</span>
                                        </div>
                                        @endif
                                        @if($activity->remarks)
                                        <div class="mt-2">
                                            <span class="text-muted fw-semibold">Remarks:</span>
                                            <div class="mt-1 p-2 bg-light rounded" style="white-space: pre-wrap; word-wrap: break-word;">{{ $activity->remarks }}</div>
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
                    
                    <!-- Visible scrollable timeline -->
                    <div class="timeline timeline-scrollable" style="max-height: calc(100vh - 250px); overflow-y: auto; overflow-x: hidden; padding-right: 15px;">
                        @foreach($activities as $activity)
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker bg-{{ $activity->activity_type == 'disqualified' ? 'danger' : ($activity->activity_type == 'converted' ? 'success' : 'info') }}"></div>
                            <div class="timeline-content">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0 text-uppercase">
                                                @switch($activity->activity_type)
                                                    @case('status_change')
                                                        Status Changed
                                                        @break
                                                    @case('bulk_upload')
                                                        Lead Created
                                                        @break
                                                    @case('reassign')
                                                        Lead Reassigned
                                                        @break
                                                    @case('update')
                                                        Lead Updated
                                                        @break
                                                    @case('marketing_lead_assigned')
                                                        Marketing Lead Assigned
                                                        @break
                                                    @default
                                                        {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                                @endswitch
                                                @if($activity->activity_type == 'disqualified')
                                                    : {{ $activity->created_at ? $activity->created_at->format('d-m-Y h:i A') : 'N/A' }}
                                                @endif
                                            </h6>
                                            <small class="text-muted">{{ $activity->created_at ? $activity->created_at->format('d-m-Y') : 'N/A' }}</small>
                                        </div>
                                        <p class="card-text mb-2">{{ $activity->description }}</p>
                                        
                                        @if($activity->lead_status_id == 2 && $activity->followup_date)
                                        <div class="mt-2">
                                            <span class="text-muted fw-semibold">Followup Date:</span>
                                            <span class="badge bg-warning ms-2">{{ $activity->followup_date->format('d M Y') }}</span>
                                        </div>
                                        @endif
                                        @if($activity->remarks)
                                        <div class="mt-2">
                                            <span class="text-muted fw-semibold">Remarks:</span>
                                            <div class="mt-1 p-2 bg-light rounded" style="white-space: pre-wrap; word-wrap: break-word;">{{ $activity->remarks }}</div>
                                        </div>
                                        @endif
                                        @if($activity->reason)
                                        <div class="mt-2">
                                            <span class="text-muted fw-semibold">Reason:</span>
                                            <div class="mt-1 p-2 bg-light rounded" style="white-space: pre-wrap; word-wrap: break-word;">{{ $activity->reason }}</div>
                                        </div>
                                        @endif
                                        @if($activity->rating)
                                        <div class="mt-2">
                                            <span class="text-muted fw-semibold">Rating:</span>
                                            <span class="badge bg-success ms-2">{{ $activity->rating }}/10</span>
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
function printMarketingLeadHistory(leadName) {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Get all timeline items from the visible timeline (they contain all activities)
    const timelineItems = document.querySelectorAll('.timeline .timeline-item');
    let timelineContent = '';
    
    // Build timeline content from all items
    timelineItems.forEach((item, index) => {
        const cardTitle = item.querySelector('.card-title');
        const cardText = item.querySelector('.card-text');
        const remarksDiv = item.querySelector('.bg-light');
        const reasonDiv = item.querySelectorAll('.bg-light');
        const followupBadge = item.querySelector('.badge.bg-warning');
        const ratingBadge = item.querySelector('.badge.bg-success');
        const smallElements = item.querySelectorAll('small');
        
        const activityType = cardTitle ? cardTitle.textContent.trim() : 'Activity';
        const description = cardText ? cardText.textContent.trim() : '';
        let remarks = '';
        let reason = '';
        
        // Get remarks and reason from bg-light divs
        if (reasonDiv.length > 0) {
            reasonDiv.forEach((div, idx) => {
                const parent = div.closest('.mt-2');
                if (parent) {
                    const label = parent.querySelector('.fw-semibold');
                    if (label && label.textContent.includes('Remarks')) {
                        remarks = div.textContent.trim();
                    } else if (label && label.textContent.includes('Reason')) {
                        reason = div.textContent.trim();
                    }
                }
            });
        }
        
        const followupDate = followupBadge ? followupBadge.textContent.trim() : '';
        const rating = ratingBadge ? ratingBadge.textContent.trim() : '';
        
        let updatedBy = 'N/A';
        let date = 'N/A';
        
        // Find updated by and date from small elements
        smallElements.forEach(small => {
            const text = small.textContent.trim();
            if (text.includes('Updated By:')) {
                updatedBy = text.replace('Updated By:', '').trim();
            } else if (text.includes('-') && text.includes(':')) {
                date = text;
            }
        });
        
        const markerClass = activityType.toLowerCase().includes('disqualified') ? 'danger' : 
                           activityType.toLowerCase().includes('converted') ? 'success' : 'info';
        
        timelineContent += `
            <div class="timeline-item" style="position: relative; margin-bottom: 20px;">
                <div class="timeline-marker bg-${markerClass}" style="position: absolute; left: -22px; top: 8px; width: 12px; height: 12px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 0 0 2px #dee2e6;"></div>
                <div class="timeline-content" style="margin-left: 0;">
                    <div class="card" style="border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 15px;">
                        <div class="card-body" style="padding: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <h6 style="font-weight: bold; margin-bottom: 10px; text-transform: uppercase;">${activityType}</h6>
                                <small style="color: #6c757d;">${date}</small>
                            </div>
                            <p style="margin-bottom: 10px;">${description}</p>
                            ${reason ? `
                                <div style="margin-top: 8px;">
                                    <span style="color: #6c757d; font-weight: 600;">Reason:</span>
                                    <div style="margin-top: 4px; padding: 8px; background-color: #f8f9fa; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word;">${reason}</div>
                                </div>
                            ` : ''}
                            ${followupDate ? `
                                <div style="margin-top: 8px;">
                                    <span style="color: #6c757d; font-weight: 600;">Followup Date:</span>
                                    <span style="background-color: #ffc107; color: #212529; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-left: 8px;">${followupDate}</span>
                                </div>
                            ` : ''}
                            ${rating ? `
                                <div style="margin-top: 8px;">
                                    <span style="color: #6c757d; font-weight: 600;">Rating:</span>
                                    <span style="background-color: #28a745; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-left: 8px;">${rating}</span>
                                </div>
                            ` : ''}
                            ${remarks ? `
                                <div style="margin-top: 8px;">
                                    <span style="color: #6c757d; font-weight: 600;">Remarks:</span>
                                    <div style="margin-top: 4px; padding: 8px; background-color: #f8f9fa; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word;">${remarks}</div>
                                </div>
                            ` : ''}
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                                <small style="color: #6c757d;">Updated By: ${updatedBy}</small>
                                <small style="color: #6c757d;">${date}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    // Create the print content
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Marketing Lead History - ${leadName}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .print-header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .print-header h1 { margin: 0; color: #333; }
                .print-header p { margin: 5px 0 0 0; color: #666; }
                .timeline { position: relative; padding-left: 30px; }
                .timeline::before { content: ''; position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: #dee2e6; }
                .bg-danger { background-color: #dc3545; }
                .bg-success { background-color: #28a745; }
                .bg-info { background-color: #17a2b8; }
                @media print { 
                    body { margin: 0; }
                    .timeline::before { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h1>Marketing Lead History Report</h1>
                <p>Lead Name: ${leadName}</p>
                <p>Generated on: ${new Date().toLocaleDateString()}</p>
            </div>
            <div class="timeline">
                ${timelineContent}
            </div>
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

/* Scrollable timeline styling */
.timeline-scrollable {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
    position: relative;
}

.timeline-scrollable::-webkit-scrollbar {
    width: 8px;
}

.timeline-scrollable::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 10px;
}

.timeline-scrollable::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

.timeline-scrollable::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>
