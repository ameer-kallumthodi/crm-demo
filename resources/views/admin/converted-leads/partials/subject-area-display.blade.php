@php
    $areas = $convertedLead->subjectAreas ?? collect();
@endphp
@if($areas->isNotEmpty())
    <span class="converted-lead-subject-areas-display">{{ $areas->pluck('title')->filter()->implode(', ') }}</span>
@else
    <span class="text-muted">N/A</span>
@endif
