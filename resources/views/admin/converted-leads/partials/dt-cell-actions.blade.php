@php
    $canManageCancelFlag = \App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor();
    $cancelBtnClass = $convertedLead->is_cancelled ? 'btn-danger' : 'btn-outline-danger';
    $cancelBtnTitle = $convertedLead->is_cancelled ? 'Update cancellation confirmation' : 'Confirm cancellation';
    $courseChanged = (bool) ($convertedLead->is_course_changed ?? false);
@endphp
<div class="" role="group">
    <a href="{{ route('admin.converted-leads.show', $convertedLead->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
        <i class="ti ti-eye"></i>
    </a>
    <a href="{{ route('admin.invoices.index', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View Invoice">
        <i class="ti ti-receipt"></i>
    </a>
    @if($canManageCancelFlag)
    <button type="button" class="btn btn-sm {{ $cancelBtnClass }} js-cancel-flag" title="{{ $cancelBtnTitle }}"
        data-cancel-url="{{ route('admin.converted-leads.cancel-flag', $convertedLead->id) }}"
        data-modal-title="Cancellation Confirmation">
        <i class="ti ti-ban"></i>
    </button>
    @endif
    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_support_team())
    <button type="button" class="btn btn-sm btn-info update-register-btn" title="Update Register Number"
        data-url="{{ route('admin.converted-leads.update-register-number-modal', $convertedLead->id) }}"
        data-title="Update Register Number">
        <i class="ti ti-edit"></i>
    </button>
    @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor())
    <button type="button" class="btn btn-sm {{ $courseChanged ? 'btn-success' : 'btn-danger' }} js-change-course-modal"
        title="Change Course"
        data-modal-url="{{ route('admin.converted-leads.change-course-modal', $convertedLead->id) }}"
        data-modal-title="Change Course">
        <i class="ti ti-exchange"></i>
    </button>
    @endif
    @if($convertedLead->register_number)
        @if($hasIdCard)
        <a href="{{ route('admin.converted-leads.id-card-view', $convertedLead->id) }}" class="btn btn-sm btn-success" title="View ID Card" target="_blank">
            <i class="ti ti-id"></i>
        </a>
        @else
        <form action="{{ route('admin.converted-leads.id-card-generate', $convertedLead->id) }}" method="post" style="display:inline-block" class="id-card-generate-form">
            @csrf
            <button type="submit" class="btn btn-sm btn-warning" title="Generate ID Card" data-loading-text="Generating...">
                <i class="ti ti-id"></i>
            </button>
        </form>
        @endif
    @endif
    @endif
</div>
