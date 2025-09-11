<div class="container p-2">
    <form action="{{ route('admin.teams.update', $edit_data->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="name">Team Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" id="name" value="{{ $edit_data->name }}" placeholder="Enter Team Name" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="3" placeholder="Enter Description">{{ $edit_data->description }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Update</button>
    </form>
</div>