@forelse($leads as $index => $lead)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>
        <div class="d-flex align-items-center">
            <div class="avtar avtar-s rounded-circle bg-light-primary me-2 d-flex align-items-center justify-content-center">
                <span class="f-16 fw-bold text-primary">{{ strtoupper(substr($lead->title, 0, 1)) }}</span>
            </div>
            <div>
                <h6 class="mb-0">{{ $lead->title }}</h6>
                <small class="text-muted">{{ \App\Helpers\PhoneNumberHelper::display($lead->code, $lead->phone) }}</small>
            </div>
        </div>
    </td>
    <td>
        <span class="badge {{ \App\Helpers\StatusHelper::getLeadStatusColorClass($lead->leadStatus->id) }}">
            {{ $lead->leadStatus->title }}
        </span>
    </td>
    <td>{{ $lead->course ? $lead->course->title : 'N/A' }}</td>
    <td>{{ $lead->remarks ?: 'N/A' }}</td>
    <td>{{ $lead->created_at->format('M d, Y') }}</td>
    <td>
        <input type="checkbox" name="lead_id[]" value="{{ $lead->id }}" class="form-check-input bulk-checkbox">
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center">No leads found</td>
</tr>
@endforelse

