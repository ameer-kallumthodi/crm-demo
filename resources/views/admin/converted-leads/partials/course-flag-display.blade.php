@php
    $courseFlag = $courseFlag ?? null;
@endphp
@if($courseFlag)
<span class="d-inline-flex align-items-center gap-2 course-flag-display">
    <span class="rounded border flex-shrink-0" style="width:18px;height:18px;background-color:{{ $courseFlag->color }};"></span>
    <span class="fw-medium">{{ $courseFlag->title }}</span>
</span>
@else
<span class="text-muted">N/A</span>
@endif
