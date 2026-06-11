<div class="col-12 col-sm-6 col-md-2">
    <label for="course_flag_id" class="form-label">Course Flag</label>
    <select class="form-select" id="course_flag_id" name="course_flag_id">
        <option value="">All Course Flags</option>
        @foreach(($courseFlags ?? \App\Support\CourseFlagFieldSupport::forFilterSelect()) as $courseFlag)
            <option value="{{ $courseFlag->id }}" {{ (string) request('course_flag_id') === (string) $courseFlag->id ? 'selected' : '' }}>
                {{ $courseFlag->title }}
            </option>
        @endforeach
    </select>
</div>
