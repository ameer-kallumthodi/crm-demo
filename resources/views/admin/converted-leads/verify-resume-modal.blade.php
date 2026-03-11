<div class="container p-2">
    <p class="text-muted small mb-3">Resume verification for <strong>{{ $convertedLead->name }}</strong>.</p>

    @if($convertedLead->mentorDetails?->placement_resume)
        <div class="mb-3">
            <label class="form-label text-muted">Current status</label>
            <p class="mb-0">
                @if($convertedLead->mentorDetails->is_resume_verified)
                    <span class="badge bg-success">Verified</span>
                    @if($convertedLead->mentorDetails->resume_verified_at)
                        <span class="text-muted ms-2">{{ $convertedLead->mentorDetails->resume_verified_at->format('d M Y h:i A') }}</span>
                    @endif
                    @if($convertedLead->mentorDetails->resumeVerifiedBy)
                        <br><small class="text-muted">By: {{ $convertedLead->mentorDetails->resumeVerifiedBy->name }}</small>
                    @endif
                @else
                    <span class="badge bg-secondary">Not verified</span>
                @endif
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <form action="{{ route('admin.converted-leads.unverify-resume', $convertedLead->id) }}" method="post" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning"><i class="ti ti-circle-x"></i> Unverify</button>
            </form>
            <form action="{{ route('admin.converted-leads.verify-resume', $convertedLead->id) }}" method="post" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success"><i class="ti ti-circle-check"></i> Verify</button>
            </form>
        </div>
    @else
        <p class="text-muted mb-0">No resume uploaded yet.</p>
        <div class="mt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    @endif
</div>
