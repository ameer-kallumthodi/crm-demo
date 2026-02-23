@extends('layouts.mantis')

@section('title', 'Online Teaching Faculty')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Online Teaching Faculty</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Online Teaching Faculty</li>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Faculty List</h5>
                <button type="button" class="btn btn-primary btn-sm" id="jsAddOnlineTeachingFaculty"
                    data-url="{{ route('admin.online-teaching-faculties.add') }}">
                    <i class="ti ti-plus"></i> Add Faculty
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="onlineTeachingFacultyTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Actions</th>
                                <th>Full Name</th>
                                <th>Primary Mobile</th>
                                <th>Official Email</th>
                                <th>Department</th>
                                <th>Gender</th>
                                <th>DOB</th>
                                <th>Teaching Exp.</th>
                                <th>Faculty ID</th>
                                <th>Class Level</th>
                                <th>Employment Type</th>
                                <th>Work Schedule</th>
                                <th>Candidate Status</th>
                                <th>Platform</th>
                                <th>Tech Ready</th>
                                <th>Demo Date</th>
                                <th>Demo By</th>
                                <th>Offer Issued</th>
                                <th>Joining</th>
                                <th>Remarks</th>
                                <th>Offer Letter</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@push('styles')
<style>
    /* Faculty table - same pattern as other admin DataTables: proper column widths, no collapsing */
    .card-body .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #onlineTeachingFacultyTable {
        width: max-content !important;
        min-width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    #onlineTeachingFacultyTable thead th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 12px;
        padding: 10px 8px;
        vertical-align: middle;
        white-space: nowrap;
        border-bottom: 2px solid #dee2e6;
        text-align: left;
    }

    #onlineTeachingFacultyTable tbody td {
        font-size: 12px;
        padding: 8px;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
    }

    #onlineTeachingFacultyTable tbody tr:hover {
        background: #f8f9ff;
    }

    #onlineTeachingFacultyTable td .display-value {
        display: inline-block;
        max-width: 100%;
        vertical-align: middle;
    }

    /* Column min-widths so fields don't collapse - each column has room to show full content */
    #onlineTeachingFacultyTable th:nth-child(1),  #onlineTeachingFacultyTable td:nth-child(1)  { min-width: 40px; }   /* # */
    #onlineTeachingFacultyTable th:nth-child(2),  #onlineTeachingFacultyTable td:nth-child(2)  { min-width: 155px; }  /* Actions */
    #onlineTeachingFacultyTable th:nth-child(3),  #onlineTeachingFacultyTable td:nth-child(3)  { min-width: 120px; }  /* Full Name */
    #onlineTeachingFacultyTable th:nth-child(4),  #onlineTeachingFacultyTable td:nth-child(4)  { min-width: 115px; }  /* Primary Mobile */
    #onlineTeachingFacultyTable th:nth-child(5),  #onlineTeachingFacultyTable td:nth-child(5)  { min-width: 160px; }  /* Official Email */
    #onlineTeachingFacultyTable th:nth-child(6),  #onlineTeachingFacultyTable td:nth-child(6)  { min-width: 100px; }  /* Department */
    #onlineTeachingFacultyTable th:nth-child(7),  #onlineTeachingFacultyTable td:nth-child(7)  { min-width: 70px; }   /* Gender */
    #onlineTeachingFacultyTable th:nth-child(8),  #onlineTeachingFacultyTable td:nth-child(8)  { min-width: 100px; }  /* DOB */
    #onlineTeachingFacultyTable th:nth-child(9),  #onlineTeachingFacultyTable td:nth-child(9)  { min-width: 95px; }   /* Teaching Exp. */
    #onlineTeachingFacultyTable th:nth-child(10), #onlineTeachingFacultyTable td:nth-child(10) { min-width: 85px; }   /* Faculty ID */
    #onlineTeachingFacultyTable th:nth-child(11), #onlineTeachingFacultyTable td:nth-child(11) { min-width: 100px; }  /* Class Level */
    #onlineTeachingFacultyTable th:nth-child(12), #onlineTeachingFacultyTable td:nth-child(12) { min-width: 115px; }  /* Employment Type */
    #onlineTeachingFacultyTable th:nth-child(13), #onlineTeachingFacultyTable td:nth-child(13) { min-width: 110px; }  /* Work Schedule */
    #onlineTeachingFacultyTable th:nth-child(14), #onlineTeachingFacultyTable td:nth-child(14) { min-width: 120px; }  /* Candidate Status */
    #onlineTeachingFacultyTable th:nth-child(15), #onlineTeachingFacultyTable td:nth-child(15) { min-width: 85px; }   /* Platform */
    #onlineTeachingFacultyTable th:nth-child(16), #onlineTeachingFacultyTable td:nth-child(16) { min-width: 85px; }   /* Tech Ready */
    #onlineTeachingFacultyTable th:nth-child(17), #onlineTeachingFacultyTable td:nth-child(17) { min-width: 95px; }   /* Demo Date */
    #onlineTeachingFacultyTable th:nth-child(18), #onlineTeachingFacultyTable td:nth-child(18) { min-width: 95px; }   /* Demo By */
    #onlineTeachingFacultyTable th:nth-child(19), #onlineTeachingFacultyTable td:nth-child(19) { min-width: 100px; }  /* Offer Issued */
    #onlineTeachingFacultyTable th:nth-child(20), #onlineTeachingFacultyTable td:nth-child(20) { min-width: 85px; }   /* Joining */
    #onlineTeachingFacultyTable th:nth-child(21), #onlineTeachingFacultyTable td:nth-child(21) { min-width: 180px; }  /* Remarks */
    #onlineTeachingFacultyTable th:nth-child(22), #onlineTeachingFacultyTable td:nth-child(22) { min-width: 100px; }  /* Offer Letter */
    #onlineTeachingFacultyTable th:nth-child(23), #onlineTeachingFacultyTable td:nth-child(23) { min-width: 115px; }  /* Created At */

    /* Actions - buttons stay on one line */
    #onlineTeachingFacultyTable td:nth-child(2) .btn-group {
        display: inline-flex;
        flex-wrap: nowrap;
        gap: 4px;
    }
    #onlineTeachingFacultyTable td:nth-child(2) .btn-group .btn {
        flex-shrink: 0;
    }

    /* Remarks - full text can wrap */
    #onlineTeachingFacultyTable td:nth-child(21) .display-value {
        white-space: normal;
        word-wrap: break-word;
    }

    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .inline-edit {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .inline-edit:hover {
        background: #f0f7ff;
        border-radius: 3px;
    }

    .inline-edit.editing .display-value {
        background: #e3f2fd;
        padding: 2px 6px;
        border-radius: 3px;
    }

    /* Overlay edit form - positioned fixed to escape table overflow */
    .edit-form-overlay {
        position: fixed;
        z-index: 9999;
    }

    .edit-form-overlay .edit-form {
        background: #fff;
        border: 2px solid #7367f0;
        border-radius: 6px;
        padding: 12px;
        min-width: 320px;
        max-width: 520px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    }

    .edit-form-overlay .edit-form input,
    .edit-form-overlay .edit-form select,
    .edit-form-overlay .edit-form textarea {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
    }

    .edit-form-overlay .edit-form input:focus,
    .edit-form-overlay .edit-form select:focus,
    .edit-form-overlay .edit-form textarea:focus {
        border-color: #7367f0;
        outline: none;
        box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.1);
    }

    .edit-form-overlay .edit-form textarea {
        resize: vertical;
        min-height: 60px;
    }

    .edit-form-overlay .edit-form .btn-group {
        margin-top: 8px;
        display: flex;
        gap: 6px;
    }

    .edit-form-overlay .edit-form .btn {
        padding: 4px 12px;
        font-size: 12px;
        flex: 1;
    }

    .edit-btn {
        opacity: 0.5;
        transition: opacity 0.2s;
        margin-left: 4px;
    }

    .inline-edit:hover .edit-btn {
        opacity: 1;
    }

    /* Inline file upload widget */
    .inline-file-upload {
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .inline-file-upload .btn {
        white-space: nowrap;
    }

    .inline-file-upload .js-inline-upload-btn {
        padding: 2px 8px;
    }

    .table-responsive {
        border-radius: 6px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    var dataUrl = @json(route('admin.online-teaching-faculties.data'));
    var columnsDef = @json($columns);

    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#onlineTeachingFacultyTable')) {
        $('#onlineTeachingFacultyTable').DataTable().destroy();
        $('#onlineTeachingFacultyTable tbody').empty();
    }

    var table = $('#onlineTeachingFacultyTable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            type: 'GET',
            error: function(xhr) {
                if (typeof window.showToast === 'function') {
                    window.showToast('Error loading data. Please try again.', 'error');
                }
            }
        },
        columns: columnsDef,
        order: [[22, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        scrollX: false,
        autoWidth: false,
        dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
            emptyTable: 'No faculty records found',
            zeroRecords: 'No matching records found',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            search: 'Search:',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        }
    });

    window.ONLINE_TEACHING_FACULTY_TABLE = table;
});
</script>
<script src="{{ asset('assets/js/online-teaching-faculties.js') }}"></script>
@endpush
@endsection

