<form action="{{ route('leads.convert.submit', $lead->id) }}" method="post">
    @csrf
    <div class="row g-3">
        <div class="col-lg-12">
            <div class="p-1">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" value="{{ $lead->title }}" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="code" class="form-label">Country Code <span class="text-danger">*</span></label>
                <select class="form-control" name="code" required>
                    <option value="">Select Code</option>
                    @foreach($country_codes as $code => $country)
                        <option value="{{ $code }}" {{ $lead->code == $code ? 'selected' : '' }}>
                            {{ $code }} - {{ $country }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-1">
                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="phone" value="{{ $lead->phone }}" required>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="p-1">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ $lead->email }}">
            </div>
        </div>

        <div class="col-lg-12">
            <div class="p-1">
                <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                <select class="form-control" name="course_id" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $lead->course_id == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="p-1">
                <label for="academic_assistant_id" class="form-label">Academic Assistant <span class="text-danger">*</span></label>
                <select class="form-control" name="academic_assistant_id" required>
                    <option value="">Select Academic Assistant</option>
                    @foreach($academic_assistants as $assistant)
                        <option value="{{ $assistant->id }}">{{ $assistant->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-12">
            <div class="p-1">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea class="form-control" name="remarks" rows="3" placeholder="Enter conversion remarks">{{ $lead->remarks }}</textarea>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="submit">Convert Lead</button>
        </div>
    </div>
</form>
