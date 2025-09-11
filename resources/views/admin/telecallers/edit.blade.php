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
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" id="phone" value="{{ $edit_data->phone }}" placeholder="Enter Phone">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="code">Code</label>
                        <input type="text" name="code" class="form-control" id="code" value="{{ $edit_data->code }}" placeholder="Enter Code">
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

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="role_id">Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $edit_data->role_id == $role->id ? 'selected' : '' }}>{{ $role->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
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
        </div>

        <button type="submit" class="btn btn-success float-end">Update</button>
    </form>
</div>
