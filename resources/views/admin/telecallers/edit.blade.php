<div class="container p-2">
    <form action="{{ route('admin.telecallers.update', $edit_data->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" id="name" value="{{ $edit_data->name }}" placeholder="Enter Name" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" id="email" value="{{ $edit_data->email }}" placeholder="Enter Email" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="code">Country Code</label>
                        <select class="form-select" id="code" name="code">
                            <option value="">Select Country</option>
                            @foreach($country_codes as $code => $country)
                                <option value="{{ $code }}" {{ $edit_data->code == $code ? 'selected' : '' }}>{{ $code }} - {{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ $edit_data->phone }}" placeholder="Enter Phone">
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter New Password">
                    <small class="form-text text-muted">Leave blank to keep current password</small>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="team_id">Team</label>
                    <select class="form-select" id="team_id" name="team_id">
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ $edit_data->team_id == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_team_lead" id="is_team_lead" value="1" {{ $edit_data->is_team_lead ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_team_lead">
                            <i class="ti ti-crown me-1"></i>Is Team Lead
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Update</button>
    </form>
</div>
