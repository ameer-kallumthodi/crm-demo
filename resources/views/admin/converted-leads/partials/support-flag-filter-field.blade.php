<div class="col-12 col-sm-6 col-md-2">
    <label for="support_flag_id" class="form-label">Support Flag</label>
    <select class="form-select" id="support_flag_id" name="support_flag_id">
        <option value="">All Support Flags</option>
        @foreach(($supportFlags ?? collect()) as $supportFlag)
            <option value="{{ $supportFlag->id }}" {{ (string) request('support_flag_id') === (string) $supportFlag->id ? 'selected' : '' }}>
                {{ $supportFlag->title }}
            </option>
        @endforeach
    </select>
</div>
