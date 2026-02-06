<form action="{{ route('admin.leads.bulk-convert.submit') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <!-- Filter Section -->
        <div class="col-lg-4">
            <div class="p-1">
                <label for="telecaller_id" class="form-label">Telecaller</label>
                <select class="form-control" name="telecaller_id" id="telecaller_id" required>
                    <option value="">Select Tele Caller</option>
                    @foreach ($telecallers as $telecaller)
                        <option value="{{ $telecaller->id }}">{{ $telecaller->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="p-1">
                <label for="lead_date" class="form-label">Date</label>
                <input type="date" id="lead_date" name="lead_date" class="form-control" required>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="p-1">
                <label for="lead_source_id" class="form-label">Lead Source</label>
                <select class="form-control" name="lead_source_id" id="lead_source_id" required>
                    <option value="">Select Source</option>
                    @foreach ($leadSources as $source)
                    <option value="{{ $source->id }}">{{ $source->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Convert Remarks Section -->
        <div class="col-12">
            <hr>
            <h6 class="text-primary">Conversion Details</h6>
        </div>
        
        <div class="col-12">
            <div class="p-1">
                <label for="remarks" class="form-label">Conversion Remarks</label>
                <textarea class="form-control" name="remarks" rows="3" placeholder="Enter conversion remarks (optional)">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <!-- Leads Selection Section -->
        <div class="col-lg-12 pt-2">
            <label for="assign_all_counselor">Select Leads to be Converted.</label>
            <div id="telecaller_list">
                <hr>
                <div class="table-responsive bulk-operations-table">
                    <table class="table table-striped table-bordered bulk-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 60%;">Lead</th>
                                <th style="width: 60%;">Date</th>
                                <th style="width: 30%;">Action <input type="checkbox" id="check_all" class="bulk-checkbox"></th>
                            </tr>
                        </thead>
                        <tbody id="lead_table_body">
                            <!-- Content dynamically populated by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="submit" id="convert_btn" disabled>Convert Leads</button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    // Handle "Check All" functionality
    $('#check_all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('#lead_table_body input[type="checkbox"]').prop('checked', isChecked);
        toggleSubmitButton();
    });

    // Handle individual checkbox changes
    $('#lead_table_body').on('change', 'input[type="checkbox"]', function() {
        toggleSubmitButton();
    });

    // Function to enable/disable the submit button
    function toggleSubmitButton() {
        var anyChecked = $('#lead_table_body input[type="checkbox"]:checked').length > 0;
        $('#convert_btn').prop('disabled', !anyChecked);
    }

    // AJAX to fetch leads based on lead source
    $('#telecaller_id, #lead_source_id, #lead_date').on('change', function() {
        var leadSourceId = $('#lead_source_id').val();
        var teleCallerId = $('#telecaller_id').val();
        var leadDate = $('#lead_date').val();
    
        if (leadSourceId && teleCallerId && leadDate) {
            $.ajax({
                url: '{{ route("admin.leads.get-by-source") }}',
                type: 'POST', 
                data: { 
                    lead_source_id: leadSourceId, 
                    tele_caller_id: teleCallerId,
                    created_at: leadDate 
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) { 
                    $('#lead_table_body').html(response);  
                    $('#check_all').prop('checked', false);
                    toggleSubmitButton();
                }, 
                error: function(xhr, status, error) { 
                    console.log('Error fetching leads. Please try again.');
                    console.log(xhr.responseText);
                }
            });
        } else {
            $('#lead_table_body').html('');
            toggleSubmitButton();
        }
    });

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
