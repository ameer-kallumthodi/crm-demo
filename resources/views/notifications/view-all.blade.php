@extends('layouts.mantis')

@section('title', 'My Notifications')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-header-title">
                    <h5 class="m-b-10">My Notifications</h5>
                </div>
            </div>
            <div class="col-md-6">
                <ul class="breadcrumb d-flex justify-content-end">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Notifications</li>
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
                <h5 class="mb-0">
                    <i class="ti ti-bell me-2"></i>My Notifications
                </h5>
            </div>
            <div class="card-body">
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item notification-item {{ !$notification->isReadBy(auth()->id()) ? 'unread' : '' }}" 
                                 data-notification-id="{{ $notification->id }}">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        @if($notification->type === 'success')
                                            <i class="ti ti-check-circle text-success f-20"></i>
                                        @elseif($notification->type === 'error')
                                            <i class="ti ti-x-circle text-danger f-20"></i>
                                        @elseif($notification->type === 'warning')
                                            <i class="ti ti-alert-triangle text-warning f-20"></i>
                                        @else
                                            <i class="ti ti-info-circle text-info f-20"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 fw-bold">{{ $notification->title }}</h6>
                                                <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                                <div class="d-flex align-items-center gap-3">
                                                    <small class="text-muted">
                                                        <i class="ti ti-clock me-1"></i>
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="ti ti-user me-1"></i>
                                                        {{ $notification->creator->name ?? 'System' }}
                                                    </small>
                                                    @if($notification->target_type !== 'all')
                                                        <small class="text-muted">
                                                            <i class="ti ti-target me-1"></i>
                                                            @if($notification->target_type === 'all_role')
                                                                All Role
                                                            @else
                                                                {{ ucfirst(str_replace('_', ' ', $notification->target_type)) }}
                                                            @endif
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if(!$notification->isReadBy(auth()->id()))
                                                    <span class="badge bg-primary">New</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="ti ti-bell f-48 mb-3"></i>
                        <h5>No Notifications</h5>
                        <p>You don't have any notifications yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

@endsection

@section('page-scripts')
<style>
.notification-item.unread {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
}

.notification-item.unread .fw-bold {
    color: #007bff;
}
</style>
@endsection
