<td>
    <div class="inline-edit support-flag-field" data-field="support_flag_id" data-id="{{ $convertedLead->id }}" data-current-id="{{ $convertedLead->support_flag_id }}">
        <span class="display-value">@include('admin.converted-leads.partials.support-flag-display', ['supportFlag' => $convertedLead->supportFlag])</span>
        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_support_team())
        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit Support Flag">
            <i class="ti ti-edit"></i>
        </button>
        @endif
    </div>
</td>
