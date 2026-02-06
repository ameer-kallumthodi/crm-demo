@php
    $url = $value ? asset('storage/'.$value) : null;
@endphp
<tr>
    <td>{{ $label }}</td>
    <td>
        @if($url)
            <a class="btn btn-outline-primary btn-sm" target="_blank" href="{{ $url }}">
                <i class="ti ti-download"></i> View / Download
            </a>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>
    <td>
        <div class="d-flex gap-2 align-items-center">
            <input type="file" class="form-control form-control-sm js-doc-file" data-field="{{ $field }}">
            <button type="button" class="btn btn-primary btn-sm js-upload-doc" data-field="{{ $field }}">Upload</button>
        </div>
    </td>
</tr>

