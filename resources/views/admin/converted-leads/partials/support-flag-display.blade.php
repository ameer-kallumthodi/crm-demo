@php
    $supportFlag = $supportFlag ?? null;
@endphp
@if($supportFlag)
<span class="d-inline-flex align-items-center gap-2 support-flag-display">
    <span class="rounded border flex-shrink-0" style="width:18px;height:18px;background-color:{{ $supportFlag->color }};"></span>
    <span class="fw-medium">{{ $supportFlag->title }}</span>
</span>
@else
<span class="text-muted">N/A</span>
@endif
