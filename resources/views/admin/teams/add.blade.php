<div class="container p-2">
    <form action="{{ route('admin.teams.submit') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="name">Team Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter Team Name" required>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="3" placeholder="Enter Description"></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success float-end">Submit</button>
    </form>
</div>
