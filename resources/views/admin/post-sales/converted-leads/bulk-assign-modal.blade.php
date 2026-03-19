<div class="p-3">
    <div class="row g-3 align-items-end mb-4">
        <div class="col-6 col-md-3">
            <label for="bulk_date_from" class="form-label">From Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control form-control-sm" name="date_from" id="bulk_date_from" required>
        </div>
        <div class="col-6 col-md-3">
            <label for="bulk_date_to" class="form-label">To Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control form-control-sm" name="date_to" id="bulk_date_to" required>
        </div>
        <div class="col-6 col-md-3">
            <label for="bulk_course_id" class="form-label">Course <span class="text-danger">*</span></label>
            <select class="form-select form-select-sm" name="course_id" id="bulk_course_id" required>
                <option value="">Select Course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="bulk_batch_id" class="form-label">Batch <small class="text-muted">(Optional)</small></label>
            <select class="form-select form-select-sm" name="batch_id" id="bulk_batch_id">
                <option value="">All batches</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label for="bulk_assign_to" class="form-label">Assign to Post-Sales</label>
            <select class="form-select form-select-sm" id="bulk_assign_to">
                <option value="">Select user</option>
                @foreach($postSalesUsers as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div id="bulkAssignTableWrap" class="mb-3" style="display: none;">
        <div class="d-flex align-items-center gap-2 mb-2">
            <label for="bulk_select_count" class="form-label mb-0 small fw-semibold">Count</label>
            <input type="number" class="form-control form-control-sm" id="bulk_select_count" min="1" placeholder="Select first N" style="width: 100px;">
        </div>
        <div class="table-responsive bulk-assign-table-wrap">
            <table class="table table-sm table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="bulk_check_all" class="form-check-input" title="Select all"></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Register #</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Batch</th>
                        <th>Post-Sales (Assigned)</th>
                        <th>Converted Date</th>
                    </tr>
                </thead>
                <tbody id="bulkAssignTableBody">
                </tbody>
            </table>
        </div>
        <p class="small text-muted mb-0 mt-2" id="bulkAssignCount"></p>
        <div class="d-flex justify-content-end mt-2">
            <button type="button" class="btn btn-success btn-sm" id="bulkAssignSubmitBtn" disabled>
                <i class="ti ti-user-plus me-1"></i> Bulk Assign
            </button>
        </div>
    </div>

    <div id="bulkAssignEmpty" class="text-center text-muted py-4">
        <i class="ti ti-filter-off d-block mb-2" style="font-size: 2rem;"></i>
        <p class="mb-0">Select From Date, To Date and Course to load students.</p>
    </div>
</div>
<style>
.bulk-assign-table-wrap { max-height: 320px; overflow-y: auto; }
.bulk-assign-table-wrap thead th { position: sticky; top: 0; background: #f8f9fa; z-index: 1; }
</style>
