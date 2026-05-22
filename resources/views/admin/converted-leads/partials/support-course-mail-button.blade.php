@php
    $canSendCourseMail = filled($convertedLead->email)
        && $convertedLead->course_id
        && $convertedLead->batch_id;
@endphp
@if($canSendCourseMail)
<button type="button"
    class="btn btn-sm btn-primary js-send-support-course-mail"
    title="Send course mail to {{ $convertedLead->email }}"
    data-lead-id="{{ $convertedLead->id }}"
    data-name="{{ $convertedLead->name }}">
    <i class="ti ti-mail"></i>
</button>
@else
<button type="button"
    class="btn btn-sm btn-primary"
    disabled
    title="{{ filled($convertedLead->email) ? 'Course and batch are required' : 'No email on file' }}">
    <i class="ti ti-mail"></i>
</button>
@endif
