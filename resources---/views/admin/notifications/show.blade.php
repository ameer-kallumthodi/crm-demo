<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti ti-bell me-2"></i>Notification Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Title:</label>
                            <p class="mb-0">{{ $notification->title }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Message:</label>
                            <div class="p-3 bg-light rounded">
                                {!! $notification->message !!}
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Type:</label>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $notification->type === 'success' ? 'success' : ($notification->type === 'error' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info')) }}">
                                            {{ ucfirst($notification->type) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Target Type:</label>
                                    <p class="mb-0">
                                        <span class="badge bg-secondary">
                                            @if($notification->target_type === 'all_role')
                                                All Role
                                            @else
                                                {{ ucfirst(str_replace('_', ' ', $notification->target_type)) }}
                                            @endif
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Role:</label>
                                    <p class="mb-0">{{ $notification->role->title ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">User:</label>
                                    <p class="mb-0">{{ $notification->user->name ?? 'All Users' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status:</label>
                                    <p class="mb-0">
                                        @if($notification->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created By:</label>
                                    <p class="mb-0">{{ $notification->creator->name ?? 'System' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created At:</label>
                                    <p class="mb-0">{{ $notification->created_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Updated At:</label>
                                    <p class="mb-0">{{ $notification->updated_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">Read Status</h6>
                            </div>
                            <div class="card-body">
                                @if($notification->reads->count() > 0)
                                    <div class="mb-3">
                                        <small class="text-muted">Read by {{ $notification->reads->count() }} user(s):</small>
                                    </div>
                                    <div class="list-group list-group-flush">
                                        @foreach($notification->reads as $read)
                                            <div class="list-group-item px-0 py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-medium">{{ $read->user->name ?? 'Unknown User' }}</span>
                                                    <small class="text-muted">{{ $read->read_at->format('M d, H:i') }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-3">
                                        <i class="ti ti-eye-off f-24 mb-2"></i>
                                        <p class="mb-0">No reads yet</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
