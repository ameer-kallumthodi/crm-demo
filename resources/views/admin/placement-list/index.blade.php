@extends('layouts.mantis')

@section('title', 'Placement List')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Placement List</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Converted Leads</li>
                    <li class="breadcrumb-item">Placement List</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Converted Students (Placement Passed)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="placementListTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Admission Batch</th>
                                <th>Starting Date</th>
                                <th>Ending Date</th>
                                <th>Specialization</th>
                                <th>Resume</th>
                                <th>Stage</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection

@push('scripts')
<div id="placementListConfig"
     data-data-url="{{ route('admin.placement-list.data') }}"
     data-specialization-update-url="{{ route('admin.placement-list.update-specialization', ['id' => '__ID__']) }}"
     style="display: none;"></div>
<script>
$(document).ready(function() {
    var configEl = document.getElementById('placementListConfig');
    var dataUrl = configEl ? configEl.getAttribute('data-data-url') : '';
    var specializationUpdateUrlTemplate = configEl ? configEl.getAttribute('data-specialization-update-url') : '';
    if (!dataUrl) return;

    // Prevent global DataTable init from touching this table
    $('#placementListTable').removeClass('datatable').removeClass('data_table_basic');

    if ($.fn.DataTable.isDataTable('#placementListTable')) {
        $('#placementListTable').DataTable().destroy();
    }

    var placementTable = $('#placementListTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            type: 'GET',
            dataSrc: 'data',
            error: function(xhr, error, thrown) {
                console.error('Placement list data error:', error);
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[1, 'asc']],
        columns: [
            { data: 'index', name: 'index', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'phone', name: 'phone' },
            { data: 'email', name: 'email' },
            { data: 'course', name: 'course', orderable: false },
            { data: 'batch', name: 'batch', orderable: false },
            { data: 'admission_batch', name: 'admission_batch', orderable: false },
            { data: 'class_start_date', name: 'class_start_date', orderable: false },
            { data: 'class_end_date', name: 'class_end_date', orderable: false },
            { data: 'specialization', name: 'specialization', orderable: false, searchable: false },
            { data: 'resume', name: 'resume', orderable: false, searchable: false },
            { data: 'stage', name: 'stage', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        columnDefs: [
            {
                targets: 9,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    var val = (data != null && data !== undefined) ? String(data) : '';
                    var id = row.id || '';
                    var esc = function(s) { return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); };
                    return '<div class="placement-spec-cell">' +
                        '<span class="placement-spec-display d-inline-block align-middle">' + (val ? esc(val) : '—') + '</span> ' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary placement-spec-edit-btn" data-id="' + esc(id) + '" title="Edit">' +
                        '<i class="ti ti-edit"></i> Edit</button>' +
                        '<span class="placement-spec-edit-wrap d-none">' +
                        '<input type="text" class="form-control form-control-sm d-inline-block placement-spec-input" style="width:180px;max-width:100%" ' +
                        'data-id="' + esc(id) + '" value="' + esc(val) + '" placeholder="Specialization" maxlength="500"> ' +
                        '<button type="button" class="btn btn-sm btn-primary placement-spec-save-btn"><i class="ti ti-check"></i> Save</button> ' +
                        '<button type="button" class="btn btn-sm btn-secondary placement-spec-cancel-btn"><i class="ti ti-x"></i> Cancel</button>' +
                        '</span></div>';
                }
            },
            {
                targets: 10,
                orderable: false,
                searchable: false,
                render: function (data) {
                    return data || '—';
                }
            },
            {
                targets: 11,
                orderable: false,
                searchable: false,
                render: function (data) {
                    if (data === 'Placed') return '<span class="badge bg-success">Placed</span>';
                    if (data === 'Need Mock Test') return '<span class="badge bg-warning text-dark">Need Mock Test</span>';
                    if (data === 'Passed Mock Test') return '<span class="badge bg-info">Passed Mock Test</span>';
                    return '<span class="badge bg-secondary">Pending</span>';
                }
            },
            {
                targets: 12,
                orderable: false,
                searchable: false,
                render: function (data) {
                    return data || '';
                }
            }
        ],
        language: {
            processing: 'Loading...',
            emptyTable: 'No students in placement list.',
            zeroRecords: 'No matching records found',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            search: 'Search:',
            paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }
        }
    });

    // Edit: show input, hide display and Edit button
    $('#placementListTable').on('click', '.placement-spec-edit-btn', function() {
        var $cell = $(this).closest('.placement-spec-cell');
        $cell.find('.placement-spec-display').addClass('d-none');
        $cell.find('.placement-spec-edit-btn').addClass('d-none');
        $cell.find('.placement-spec-edit-wrap').removeClass('d-none');
        $cell.find('.placement-spec-input').focus();
    });

    // Cancel: hide input, show display and Edit button; restore input value
    $('#placementListTable').on('click', '.placement-spec-cancel-btn', function() {
        var $cell = $(this).closest('.placement-spec-cell');
        var displayVal = $cell.find('.placement-spec-display').text();
        $cell.find('.placement-spec-input').val(displayVal === '—' ? '' : displayVal);
        $cell.find('.placement-spec-edit-wrap').addClass('d-none');
        $cell.find('.placement-spec-display').removeClass('d-none');
        $cell.find('.placement-spec-edit-btn').removeClass('d-none');
    });

    // Save: PATCH then switch back to display
    $('#placementListTable').on('click', '.placement-spec-save-btn', function() {
        var $cell = $(this).closest('.placement-spec-cell');
        var $input = $cell.find('.placement-spec-input');
        var id = $input.data('id');
        var value = $input.val().trim();
        if (!id || !specializationUpdateUrlTemplate) return;
        var url = specializationUpdateUrlTemplate.replace('__ID__', id);
        var $saveBtn = $cell.find('.placement-spec-save-btn');
        $saveBtn.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'PATCH',
            data: { specialization: value, _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function(res) {
                var newVal = (res.specialization != null && res.specialization !== undefined) ? res.specialization : '';
                $cell.find('.placement-spec-display').text(newVal || '—');
                $cell.find('.placement-spec-edit-wrap').addClass('d-none');
                $cell.find('.placement-spec-display').removeClass('d-none');
                $cell.find('.placement-spec-edit-btn').removeClass('d-none');
                $input.val(newVal);
            },
            error: function() {
                // keep edit mode on error
            },
            complete: function() {
                $saveBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
