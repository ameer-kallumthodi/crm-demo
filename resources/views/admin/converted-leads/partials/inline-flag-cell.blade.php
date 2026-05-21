<td>
    <div class="inline-edit mentor-flag-field" data-field="flag_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->flag_id }}">
        <span class="display-value">@include('admin.converted-leads.partials.flag-display', ['flag' => $convertedLead->flag])</span>
        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_mentor() || \App\Helpers\RoleHelper::is_mentor_head() || \App\Helpers\RoleHelper::is_hod())
        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit Flag">
            <i class="ti ti-edit"></i>
        </button>
        @endif
    </div>
</td>
