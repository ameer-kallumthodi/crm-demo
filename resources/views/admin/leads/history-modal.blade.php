<div class="modal fade" id="leadHistoryModal" tabindex="-1" aria-labelledby="leadHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leadHistoryModalLabel">Lead History - {{ $lead->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Lead Details Section -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Lead Details</h6>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avtar avtar-s rounded-circle bg-light-primary me-2">
                                        <i class="ti ti-user f-16"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $lead->title }}</h6>
                                        <small class="text-muted">{{ $lead->phone }}</small>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="card bg-light">
                                            <div class="card-body p-2">
                                                <small class="text-muted d-block">Place</small>
                                                <span>{{ $lead->place ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-light">
                                            <div class="card-body p-2">
                                                <small class="text-muted d-block">Qualification</small>
                                                <span>{{ $lead->qualification ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body p-2">
                                                <small class="text-muted d-block">Lead Status</small>
                                                <span class="badge bg-{{ $lead->leadStatus->id == 4 ? 'success' : ($lead->leadStatus->id == 7 ? 'danger' : 'warning') }}">
                                                    {{ $lead->leadStatus->title }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-light">
                                            <div class="card-body p-2">
                                                <small class="text-muted d-block">Date</small>
                                                <span>{{ $lead->created_at->format('d-m-Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card bg-light">
                                            <div class="card-body p-2">
                                                <small class="text-muted d-block">Time</small>
                                                <span>{{ $lead->created_at->format('h:i A') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body p-2">
                                                <small class="text-muted d-block">Remarks</small>
                                                <span>{{ $lead->remarks ?? 'No remarks' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if($lead->leadStatus->id == 7)
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body p-2">
                                                <small class="text-muted d-block">Reason</small>
                                                <span>{{ $lead->reason ?? 'No reason provided' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lead History Section -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Lead History</h6>
                        <div class="timeline">
                            @forelse($activities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h6 class="timeline-title">
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
                                                @default
                                                    {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                            @endswitch
                                        </h6>
                                        <span class="timeline-time">{{ $activity->created_at->format('d-m-Y h:i A') }}</span>
                                    </div>
                                    <div class="timeline-body">
                                        <p class="mb-1">{{ $activity->description }}</p>
                                        @if($activity->remarks)
                                            <p class="mb-1"><strong>Remarks:</strong> {{ $activity->remarks }}</p>
                                        @endif
                                        @if($activity->createdBy)
                                            <small class="text-muted">Updated by: {{ $activity->createdBy->name }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="ti ti-history f-48 text-muted"></i>
                                <p class="text-muted mt-2">No history available for this lead.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printLeadHistory()">
                    <i class="ti ti-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

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
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid #007bff;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.timeline-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #495057;
}

.timeline-time {
    font-size: 12px;
    color: #6c757d;
}

.timeline-body {
    font-size: 13px;
    color: #495057;
}

.timeline-body p {
    margin-bottom: 5px;
}
</style>

<script>
function printLeadHistory() {
    const printContent = document.querySelector('#leadHistoryModal .modal-content').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Lead History - {{ $lead->title }}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .modal-header { border-bottom: 1px solid #dee2e6; padding-bottom: 10px; margin-bottom: 20px; }
                    .modal-title { font-size: 18px; font-weight: bold; }
                    .timeline { padding-left: 30px; }
                    .timeline::before { content: ''; position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: #e9ecef; }
                    .timeline-item { position: relative; margin-bottom: 20px; }
                    .timeline-marker { position: absolute; left: -22px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: #007bff; }
                    .timeline-content { background: #f8f9fa; border-radius: 8px; padding: 15px; border-left: 3px solid #007bff; }
                    .timeline-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
                    .timeline-title { margin: 0; font-size: 14px; font-weight: 600; }
                    .timeline-time { font-size: 12px; color: #6c757d; }
                    .timeline-body { font-size: 13px; }
                    .card { border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 10px; }
                    .card-body { padding: 10px; }
                    .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                    .bg-success { background-color: #28a745 !important; color: white; }
                    .bg-danger { background-color: #dc3545 !important; color: white; }
                    .bg-warning { background-color: #ffc107 !important; color: #212529; }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>
