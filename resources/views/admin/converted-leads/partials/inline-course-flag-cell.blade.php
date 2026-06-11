<td>
    <div class="inline-edit course-flag-field" data-field="course_flag_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->course_flag_id }}">
        <span class="display-value">@include('admin.converted-leads.partials.course-flag-display', ['courseFlag' => $convertedLead->courseFlag])</span>
        @if(\App\Support\CourseFlagFieldSupport::canUserUpdateCourseFlag())
        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 course-flag-edit-btn" title="Edit Course Flag">
            <i class="ti ti-edit"></i>
        </button>
        @endif
    </div>
</td>
