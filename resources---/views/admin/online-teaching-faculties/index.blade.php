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
                <div
                    id="jsOnlineTeachingFacultyConfig"
                    data-data-url="{{ route('admin.online-teaching-faculties.data') }}"
                    data-columns='@json($columns)'
                ></div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered w-100" id="onlineTeachingFacultyTable">
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Table cell styling */
    #onlineTeachingFacultyTable thead th {
        white-space: nowrap;
        background: #f8f9fa;
        font-weight: 600;
        font-size: 12px;
        padding: 12px 8px;
        vertical-align: middle;
    }

    #onlineTeachingFacultyTable tbody td {
        white-space: nowrap;
        vertical-align: middle;
        padding: 10px 8px;
        font-size: 13px;
    }

    #onlineTeachingFacultyTable tbody tr:hover {
        background: #f8f9ff;
    }

    #onlineTeachingFacultyTable td .display-value {
        display: inline-block;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Fixed column widths for better alignment */
    #onlineTeachingFacultyTable thead th:nth-child(1),
    #onlineTeachingFacultyTable tbody td:nth-child(1) { min-width: 50px; max-width: 60px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(2),
    #onlineTeachingFacultyTable tbody td:nth-child(2) { min-width: 80px; max-width: 100px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(3),
    #onlineTeachingFacultyTable tbody td:nth-child(3) { min-width: 180px; max-width: 220px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(4),
    #onlineTeachingFacultyTable tbody td:nth-child(4) { min-width: 120px; max-width: 150px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(5),
    #onlineTeachingFacultyTable tbody td:nth-child(5) { min-width: 180px; max-width: 220px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(6),
    #onlineTeachingFacultyTable tbody td:nth-child(6) { min-width: 140px; max-width: 160px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(7),
    #onlineTeachingFacultyTable tbody td:nth-child(7) { min-width: 80px; max-width: 100px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(8),
    #onlineTeachingFacultyTable tbody td:nth-child(8) { min-width: 100px; max-width: 120px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(9),
    #onlineTeachingFacultyTable tbody td:nth-child(9) { min-width: 100px; max-width: 120px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(10),
    #onlineTeachingFacultyTable tbody td:nth-child(10) { min-width: 120px; max-width: 140px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(11),
    #onlineTeachingFacultyTable tbody td:nth-child(11) { min-width: 140px; max-width: 160px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(12),
    #onlineTeachingFacultyTable tbody td:nth-child(12) { min-width: 130px; max-width: 150px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(13),
    #onlineTeachingFacultyTable tbody td:nth-child(13) { min-width: 120px; max-width: 140px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(14),
    #onlineTeachingFacultyTable tbody td:nth-child(14) { min-width: 140px; max-width: 160px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(15),
    #onlineTeachingFacultyTable tbody td:nth-child(15) { min-width: 120px; max-width: 140px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(16),
    #onlineTeachingFacultyTable tbody td:nth-child(16) { min-width: 100px; max-width: 120px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(17),
    #onlineTeachingFacultyTable tbody td:nth-child(17) { min-width: 110px; max-width: 130px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(18),
    #onlineTeachingFacultyTable tbody td:nth-child(18) { min-width: 120px; max-width: 140px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(19),
    #onlineTeachingFacultyTable tbody td:nth-child(19) { min-width: 110px; max-width: 130px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(20),
    #onlineTeachingFacultyTable tbody td:nth-child(20) { min-width: 100px; max-width: 120px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(21),
    #onlineTeachingFacultyTable tbody td:nth-child(21) { min-width: 180px; max-width: 220px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(22),
    #onlineTeachingFacultyTable tbody td:nth-child(22) { min-width: 120px; max-width: 140px; }
    
    #onlineTeachingFacultyTable thead th:nth-child(23),
    #onlineTeachingFacultyTable tbody td:nth-child(23) { min-width: 140px; max-width: 160px; }

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

    /* Responsive table wrapper */
    .table-responsive {
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/online-teaching-faculties.js') }}"></script>
@endpush
@endsection

