<form action="{{ route('admin.leads.pullbacked.assign.submit') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <div class="col-lg-3">
            <div class="p-1">
                <label for="telecaller_id" class="form-label">Telecaller</label>
                <select class="form-control" name="telecaller_id" id="telecaller_id" required>
                    <option value="">Select Telecaller</option>
                    @foreach ($telecallers as $telecaller)
                        <option value="{{ $telecaller->id }}">{{ $telecaller->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="p-1">
                <label for="lead_from_date" class="form-label">From</label>
                <input type="date" id="lead_from_date" name="lead_from_date" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="p-1">
                <label for="lead_to_date" class="form-label">To</label>
                <input type="date" id="lead_to_date" name="lead_to_date" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-12 pt-2">
            <div class="d-flex justify-content-between align-items-center">
                <label class="mb-0">Select Pullbacked Leads to Assign</label>
                <div class="d-flex gap-3 align-items-end">
                    <div>
                        <label for="select_count" class="form-label">Count</label>
                        <input type="number" id="select_count" class="form-control" min="1" placeholder="Enter count">
                    </div>
                    <div>
                        <span class="badge bg-primary" id="selected_count">Selected: 0</span>
                    </div>
                </div>
            </div>
            <div id="telecaller_list" class="mt-2">
                <hr>
                <div class="table-responsive bulk-operations-table">
                    <table class="table table-striped table-bordered bulk-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 60%;">Lead</th>
                                <th style="width: 60%;">Lead Status</th>
                                <th style="width: 60%;">Course Interested</th>
                                <th style="width: 60%;">Remarks</th>
                                <th style="width: 60%; white-space: nowrap;">Pullbacked On</th>
                                <th style="width: 30%;">Action <input type="checkbox" id="check_all" class="bulk-checkbox ms-2"></th>
                            </tr>
                        </thead>
                        <tbody id="lead_table_body">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="submit" id="assign_btn" disabled>Assign Pullbacked Leads</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        const $leadTableBody = $('#lead_table_body');
        const $checkAll = $('#check_all');
        const $assignBtn = $('#assign_btn');
        const $selectedCount = $('#selected_count');

        function toggleSubmitButton() {
            const anyChecked = $leadTableBody.find('input[type="checkbox"]:checked').length > 0;
            $assignBtn.prop('disabled', !anyChecked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const count = $leadTableBody.find('input[type="checkbox"]:checked').length;
            $selectedCount.text('Selected: ' + count);
        }

        $checkAll.on('change', function() {
            const isChecked = $(this).is(':checked');
            $leadTableBody.find('input[type="checkbox"]').prop('checked', isChecked);
            toggleSubmitButton();
        });

        $leadTableBody.on('change', 'input[type="checkbox"]', function() {
            const allChecked = $leadTableBody.find('input[type="checkbox"]').length === $leadTableBody.find('input[type="checkbox"]:checked').length;
            $checkAll.prop('checked', allChecked);
            toggleSubmitButton();
        });

        $('#select_count').on('input', function() {
            const count = parseInt($(this).val()) || 0;
            const checkboxes = $leadTableBody.find('input[type="checkbox"]');
            checkboxes.prop('checked', false);
            checkboxes.slice(0, count).prop('checked', true);
            toggleSubmitButton();
        });

        function fetchPullbackedLeads() {
            const telecallerId = $('#telecaller_id').val();
            const fromDate = $('#lead_from_date').val();
            const toDate = $('#lead_to_date').val();

            if (telecallerId && fromDate && toDate) {
                $.ajax({
                    url: '{{ route("admin.leads.get-pullbacked-assign-leads") }}',
                    type: 'POST',
                    data: {
                        tele_caller_id: telecallerId,
                        from_date: fromDate,
                        to_date: toDate
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $leadTableBody.html(response);
                        $checkAll.prop('checked', false);
                        toggleSubmitButton();
                    },
                    error: function(xhr) {
                        console.log('Error fetching pullbacked leads:', xhr.responseText);
                        $leadTableBody.html('<tr><td colspan="7" class="text-center text-muted">Failed to load leads.</td></tr>');
                        toggleSubmitButton();
                    }
                });
            } else {
                $leadTableBody.html('');
                $checkAll.prop('checked', false);
                toggleSubmitButton();
            }
        }

        $('#telecaller_id, #lead_from_date, #lead_to_date').on('change', fetchPullbackedLeads);
    });
</script>

<style>
    .bulk-operations-table {
        max-height: 300px;
        overflow-y: auto;
    }

    .bulk-table thead th {
        background-color: #fff;
        position: sticky;
        top: 0;
        border: 1px solid #ddd;
    }

    .bulk-checkbox {
        width: 22px;
        height: 22px;
    }
</style>

