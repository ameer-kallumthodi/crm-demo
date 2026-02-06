@extends('layouts.mantis')

@section('title', 'Profile')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">User Profile</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Profile</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row g-3">
    <div class="col-12 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="avtar avtar-xl mx-auto mb-3">
                        <img src="{{ asset('assets/mantis/images/user/avatar-2.jpg') }}" alt="user-image" class="rounded-circle">
                    </div>
                    <h5 class="mb-1">{{ \App\Helpers\AuthHelper::getUserName() ?? 'User' }}</h5>
                    <p class="text-muted mb-3">{{ \App\Helpers\AuthHelper::getRoleTitle() ?? 'User' }}</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-edit"></i> <span class="d-none d-sm-inline">Edit Profile</span>
                        </a>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-settings"></i> <span class="d-none d-sm-inline">Settings</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="avtar avtar-s bg-light-primary me-3">
                                <i class="ti ti-mail f-16"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Email</h6>
                                <p class="text-muted mb-0">{{ \App\Helpers\AuthHelper::getCurrentUser()->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="avtar avtar-s bg-light-success me-3">
                                <i class="ti ti-phone f-16"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Phone</h6>
                                <p class="text-muted mb-0">{{ \App\Helpers\AuthHelper::getCurrentUser()->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="avtar avtar-s bg-light-warning me-3">
                                <i class="ti ti-map-pin f-16"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Location</h6>
                                <p class="text-muted mb-0">{{ \App\Helpers\AuthHelper::getCurrentUser()->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Profile Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="{{ \App\Helpers\AuthHelper::getUserName() ?? 'User' }}" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ \App\Helpers\AuthHelper::getRoleTitle() ?? 'User' }}" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="{{ \App\Helpers\AuthHelper::getCurrentUser()->email ?? 'N/A' }}" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" value="{{ \App\Helpers\AuthHelper::getCurrentUser()->phone ?? 'N/A' }}" readonly>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" rows="3" readonly>{{ \App\Helpers\AuthHelper::getCurrentUser()->address ?? 'N/A' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Activity Summary</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-4 col-md-4">
                        <div class="text-center">
                            <h4 class="text-primary mb-1 f-24">0</h4>
                            <p class="text-muted mb-0 f-14">Total Leads</p>
                        </div>
                    </div>
                    <div class="col-4 col-md-4">
                        <div class="text-center">
                            <h4 class="text-success mb-1 f-24">0</h4>
                            <p class="text-muted mb-0 f-14">Converted</p>
                        </div>
                    </div>
                    <div class="col-4 col-md-4">
                        <div class="text-center">
                            <h4 class="text-warning mb-1 f-24">0</h4>
                            <p class="text-muted mb-0 f-14">Pending</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-s rounded-circle text-primary bg-light-primary">
                                    <i class="ti ti-user-plus f-16"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Profile Updated</h6>
                                <p class="mb-0 text-muted">Your profile information has been updated</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-s rounded-circle text-success bg-light-success">
                                    <i class="ti ti-login f-16"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Login Successful</h6>
                                <p class="mb-0 text-muted">You have successfully logged in to the system</p>
                                <small class="text-muted">1 day ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection