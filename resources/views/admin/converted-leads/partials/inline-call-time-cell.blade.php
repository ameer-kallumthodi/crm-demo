<td>
    @php
        $callTimeValue = $convertedLead->mentorDetails?->call_time;
        $callTimeDisplay = '-';
        if (!empty($callTimeValue)) {
            try {
                $callTimeDisplay = \Carbon\Carbon::createFromFormat('H:i:s', $callTimeValue)->format('h:i A');
            } catch (\Throwable $e) {
                try {
                    $callTimeDisplay = \Carbon\Carbon::createFromFormat('H:i', $callTimeValue)->format('h:i A');
                } catch (\Throwable $e2) {
                    $callTimeDisplay = $callTimeValue;
                }
            }
        }
    @endphp
    <div class="inline-edit" data-field="call_time" data-id="{{ $convertedLead->id }}" data-current="{{ $convertedLead->mentorDetails?->call_time }}">
        <span class="display-value">{{ $callTimeDisplay }}</span>
        @if(\App\Helpers\RoleHelper::is_admin_or_super_admin() || \App\Helpers\RoleHelper::is_admission_counsellor() || \App\Helpers\RoleHelper::is_academic_assistant() || \App\Helpers\RoleHelper::is_mentor() || \App\Helpers\RoleHelper::is_mentor_head() || \App\Helpers\RoleHelper::is_hod())
        <button type="button" class="btn btn-sm btn-outline-secondary ms-1 edit-btn" title="Edit Call Time">
            <i class="ti ti-edit"></i>
        </button>
        @endif
    </div>
</td>
