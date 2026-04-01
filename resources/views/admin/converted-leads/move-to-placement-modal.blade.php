<div class="container p-2">
    <form action="{{ route('admin.converted-leads.move-to-placement.submit', $convertedLead->id) }}" method="post" enctype="multipart/form-data" id="move-to-placement-form">
        @csrf
        @php $hasExistingResume = !empty($convertedLead->mentorDetails?->placement_resume); @endphp
        <p class="text-muted small mb-3">
            @if($hasExistingResume)
                Upload a new resume for <strong>{{ $convertedLead->name }}</strong>. Verification status will be set back to pending.
            @else
                Upload resume for <strong>{{ $convertedLead->name }}</strong> to mark as moved to placement.
            @endif
        </p>
        <div class="mb-3">
            <label class="form-label" for="placement_resume">Resume <span class="text-danger">*</span></label>
            <input type="file" name="placement_resume" class="form-control" id="placement_resume" accept=".pdf,.doc,.docx" required>
            <small class="text-muted">Accepted: PDF, DOC, DOCX</small>
            @error('placement_resume')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>
