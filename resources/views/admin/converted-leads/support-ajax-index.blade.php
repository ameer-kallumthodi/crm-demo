@extends('layouts.mantis')

@section('title', 'Support Converted Leads (Ajax)')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Support Converted Leads</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Support Converted Leads</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- [ Filter Section ] start -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-select" id="batch_id" name="batch_id">
                                <option value="">All Batches</option>
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}">{{ $batch->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" id="btnFilter">
                                <i class="ti ti-search"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnClear">
                                <i class="ti ti-refresh"></i> Clear
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Filter Section ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Support Converted Leads List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="supportAjaxTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Converted Date</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Phone</th>
                                <th>WhatsApp</th>
                                <th>Admission Batch</th>
                                <th>Feedback Count</th>
                                <th>Action</th>
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

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#supportAjaxTable').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.support-ajax-converted-leads.data') }}",
                data: function(d) {
                    d.course_id = $('#course_id').val();
                    d.batch_id = $('#batch_id').val();
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                }
            },
            columns: [
                { data: 0, name: 'sl_no', orderable: false, searchable: false }, // Sl No
                { data: 1, name: 'created_at' }, // Converted Date
                { data: 2, name: 'name' },       // Name
                { data: 3, name: 'is_b2b' },     // Type
                { data: 4, name: 'phone' },      // Phone
                { data: 5, name: 'leadDetail.whatsapp_number' }, // WhatsApp
                { data: 6, name: 'admissionBatch.title' }, // Admission Batch
                { data: 7, name: 'support_feedback_history_count', orderable: false, searchable: false }, // Feedback Count
                { data: 8, orderable: false, searchable: false } // Action
            ],
            order: [[1, 'desc']],
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            }
        });

        $('#btnFilter').click(function() {
            table.draw();
        });

        $('#btnClear').click(function() {
            $('#filterForm')[0].reset();
            table.draw();
        });
    });
</script>
@endpush
