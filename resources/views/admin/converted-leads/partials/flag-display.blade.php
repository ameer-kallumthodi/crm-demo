@php
    $flag = $flag ?? null;
@endphp
@if($flag)
<span class="d-inline-flex align-items-center gap-2 mentor-flag-display">
    <span class="rounded border flex-shrink-0" style="width:18px;height:18px;background-color:{{ $flag->color }};"></span>
    <span class="fw-medium">{{ $flag->title }}</span>
</span>
@else
<span class="text-muted">N/A</span>
@endif
