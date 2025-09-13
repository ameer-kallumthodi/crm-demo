@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Academic Assistant Details</h5>
                    <div>
                        <a href="{{ route('academic-assistants.edit', $academicAssistant->id) }}" class="btn btn-warning">
                            <i class="ti ti-edit"></i> Edit
                        </a>
                        <a href="{{ route('academic-assistants.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Name:</label>
                                <p class="form-control-plaintext">{{ $academicAssistant->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email:</label>
                                <p class="form-control-plaintext">{{ $academicAssistant->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone:</label>
                                <p class="form-control-plaintext">{{ \App\Helpers\PhoneNumberHelper::display($academicAssistant->code, $academicAssistant->phone) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge {{ $academicAssistant->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $academicAssistant->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Address:</label>
                                <p class="form-control-plaintext">{{ $academicAssistant->address ?: 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created By:</label>
                                <p class="form-control-plaintext">{{ $academicAssistant->createdBy ? $academicAssistant->createdBy->name : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created At:</label>
                                <p class="form-control-plaintext">{{ $academicAssistant->created_at->format('M d, Y H:i A') }}</p>
                            </div>
                        </div>
                        @if($academicAssistant->updatedBy)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated By:</label>
                                <p class="form-control-plaintext">{{ $academicAssistant->updatedBy->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Updated At:</label>
                                <p class="form-control-plaintext">{{ $academicAssistant->updated_at->format('M d, Y H:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
