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
                    <div class="list-group list-group-flush" id="notificationListPage">
                        @foreach($notifications as $notification)
                            <div class="list-group-item notification-item {{ !$notification->isReadBy(auth()->id()) ? 'unread' : '' }}" 
                                 data-notification-id="{{ $notification->id }}"
                                 style="padding: 12px 16px; margin-bottom: 8px; border-radius: 8px; border: 1px solid #e9ecef;">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="notification-icon bg-{{ $notification->type === 'success' ? 'success' : ($notification->type === 'error' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'primary')) }} text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; min-width: 40px;">
                                            @if($notification->type === 'success')
                                                <i class="ti ti-circle-check" style="font-size: 18px;"></i>
                                            @elseif($notification->type === 'error')
                                                <i class="ti ti-alert-circle" style="font-size: 18px;"></i>
                                            @elseif($notification->type === 'warning')
                                                <i class="ti ti-alert-triangle" style="font-size: 18px;"></i>
                                            @else
                                                <i class="ti ti-info-circle" style="font-size: 18px;"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 {{ !$notification->isReadBy(auth()->id()) ? 'fw-semibold' : 'text-muted' }}" style="font-size: 14px;">{{ $notification->title }}</h6>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 ms-2">
                                                @if(!$notification->isReadBy(auth()->id()))
                                                    <span class="badge bg-primary" style="font-size: 10px;">New</span>
                                                @endif
                                                <small class="text-muted" style="white-space: nowrap; font-size: 11px;">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        <p class="text-body mb-2 {{ $notification->isReadBy(auth()->id()) ? 'text-muted' : '' }}" style="font-size: 13px; line-height: 1.4;">{{ $notification->message }}</p>
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <small class="text-muted" style="font-size: 11px;">
                                                <i class="ti ti-user me-1"></i>
                                                by {{ $notification->creator->name ?? 'System' }}
                                            </small>
                                            @if($notification->target_type !== 'all')
                                                <small class="text-muted" style="font-size: 11px;">
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
    background-color: #f8f9fa !important;
    border-left: 3px solid #007bff !important;
}

.notification-item.unread .fw-semibold {
    color: #007bff;
}

#notificationListPage .list-group-item {
    margin-bottom: 8px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    padding: 12px 16px;
    transition: all 0.2s ease;
}

#notificationListPage .list-group-item:hover {
    background-color: #f8f9fa !important;
    border-color: #dee2e6;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.notification-icon {
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
