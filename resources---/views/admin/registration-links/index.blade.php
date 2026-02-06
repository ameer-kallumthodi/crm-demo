@extends('layouts.mantis')

@section('title', 'Registration Links')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">Registration Links Management</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Registration Links</li>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Registration Links List</h5>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm px-3 js-open-registration-link-modal"
                        data-url="{{ route('admin.registration-links.add') }}" data-modal-title="Add Registration Link">
                        <i class="ti ti-plus"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Color</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrationLinks as $index => $registrationLink)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $registrationLink->title }}</td>
                                <td>
                                    <span class="badge rounded-pill text-uppercase"
                                        style="<?php echo e('background-color: ' . ($registrationLink->color_code ?? '#6c757d') . '; color: #fff;'); ?>">
                                        {{ $registrationLink->color_code ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $registrationLink->created_at->format('M d, Y') }}</td>
                                <td>{{ $registrationLink->updated_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info js-open-registration-link-modal"
                                            data-url="{{ route('admin.registration-links.edit', $registrationLink->id) }}" data-modal-title="Edit Registration Link">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-danger js-delete-registration-link"
                                            data-url="{{ route('admin.registration-links.delete', $registrationLink->id) }}" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
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
    document.addEventListener('click', function(event) {
        const openTrigger = event.target.closest('.js-open-registration-link-modal');
        if (openTrigger) {
            event.preventDefault();
            const url = openTrigger.getAttribute('data-url');
            const title = openTrigger.getAttribute('data-modal-title') || 'Registration Link';
            if (typeof show_small_modal === 'function' && url) {
                show_small_modal(url, title);
            }
            return;
        }

        const deleteTrigger = event.target.closest('.js-delete-registration-link');
        if (deleteTrigger) {
            event.preventDefault();
            const url = deleteTrigger.getAttribute('data-url');
            if (typeof delete_modal === 'function' && url) {
                delete_modal(url);
            }
        }
    });
</script>
@endpush
