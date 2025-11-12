@forelse($marketingLeads as $index => $lead)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $lead->lead_name }}</td>
    <td>{{ ($lead->code ? $lead->code . ' ' : '') . $lead->phone }}</td>
    <td>{{ $lead->location }}</td>
    <td>
        @if($lead->interested_courses && count($lead->interested_courses) > 0)
            @foreach($lead->interested_courses as $course)
                <span class="badge bg-secondary me-1">{{ $course }}</span>
            @endforeach
        @else
            -
        @endif
    </td>
    <td>
        @if($lead->remarks)
            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $lead->remarks }}">
                {{ \Illuminate\Support\Str::limit($lead->remarks, 50) }}
            </span>
        @else
            -
        @endif
    </td>
    <td>{{ $lead->date_of_visit ? $lead->date_of_visit->format('M d, Y') : '-' }}</td>
    <td>
        <input type="checkbox" name="marketing_lead_id[]" value="{{ $lead->id }}" class="bulk-checkbox">
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center">No unassigned marketing leads found for the selected criteria.</td>
</tr>
@endforelse

