@if($convertedLead->is_cancelled && $convertedLead->cancelledBy)
    <div>
        <span class="fw-semibold">{{ $convertedLead->cancelledBy->name }}</span>
        @if($convertedLead->cancelled_at)
            <br><small class="text-muted">{{ $convertedLead->cancelled_at->format('d-m-Y h:i A') }}</small>
        @endif
        @if($convertedLead->cancel_remark)
            <br><small class="text-muted mt-1 d-block"><strong>Remark:</strong> {{ $convertedLead->cancel_remark }}</small>
        @endif
    </div>
@else
    <span class="text-muted">-</span>
@endif
