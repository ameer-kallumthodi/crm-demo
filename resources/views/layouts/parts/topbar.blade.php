<!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper">
        <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="dropdown pc-h-item d-inline-flex d-md-none">
                    <a class="pc-head-link dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-search"></i>
                    </a>
                    <div class="dropdown-menu pc-h-dropdown drp-search">
                        <form class="px-3" action="{{ route('leads.index') }}" method="GET">
                            <div class="form-group mb-0 d-flex align-items-center">
                                <i data-feather="search"></i>
                                <input type="search" name="search_key" class="form-control border-0 shadow-none" placeholder="Search leads by name, phone, email..." value="{{ request('search_key') }}">
                            </div>
                        </form>
                    </div>
                </li>
                <li class="pc-h-item d-none d-md-inline-flex">
                    <form class="header-search" action="{{ route('leads.index') }}" method="GET">
                        <i data-feather="search" class="icon-search"></i>
                        <input type="search" name="search_key" class="form-control" placeholder="Search leads by name, phone, email..." value="{{ request('search_key') }}">
                    </form>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                @if(!\App\Helpers\RoleHelper::is_admin_or_super_admin())
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" id="notificationDropdown">
                        <i class="ti ti-bell"></i>
                        <span class="notification-badge" id="notificationBadge" style="display: none; background-color: #dc3545; color: white;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header d-flex align-items-center justify-content-between">
                            <h5 class="m-0">Notifications</h5>
                            <a href="#!" class="pc-head-link bg-transparent" onclick="event.preventDefault(); document.getElementById('notificationDropdown').click();"><i class="ti ti-x text-danger"></i></a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-header px-0 text-wrap position-relative" style="max-height: 400px; overflow-y: auto;">
                            <div class="list-group list-group-flush w-100" id="notificationList">
                                <div class="text-center py-3" id="notificationLoading">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mt-2">Loading notifications...</p>
                                </div>
                                <div class="text-center py-3" id="notificationEmpty" style="display: none;">
                                    <i class="ti ti-bell-off text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">No notifications</p>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="text-center py-2">
                            <a href="{{ route('notifications.view-all') }}" class="link-primary">View all notifications</a>
                        </div>
                    </div>
                </li>
                @endif
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        <img src="{{ asset('assets/mantis/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar">
                        <span class="user-name-text">{{ \App\Helpers\AuthHelper::getUserName() ?? 'User' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex mb-1 align-items-start">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('assets/mantis/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar wid-35">
                                </div>
                                <div class="flex-grow-1 ms-3 min-w-0">
                                    <h6 class="mb-1 text-truncate user-name-display" title="{{ \App\Helpers\AuthHelper::getUserName() ?? 'User' }}">{{ \App\Helpers\AuthHelper::getUserName() ?? 'User' }}</h6>
                                    <div class="user-role-info">
                                        <span class="d-inline-block text-truncate" style="max-width: 100%;">
                                            {{ \App\Helpers\AuthHelper::getRoleTitle() ?? 'User' }}
                                            @if(\App\Helpers\AuthHelper::isTeamLead())
                                                <span class="badge bg-info">Team Lead</span>
                                            @endif
                                            @if(\App\Helpers\AuthHelper::isTelecaller() && !\App\Helpers\AuthHelper::isTeamLead())
                                                <span class="badge bg-info">Telecaller</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <a href="#" class="pc-head-link bg-transparent flex-shrink-0" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout">
                                    <i class="ti ti-power text-danger"></i>
                                </a>
                            </div>
                        </div>
                        <ul class="nav drp-tabs nav-fill nav-tabs" id="mydrpTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="drp-t1" data-bs-toggle="tab" data-bs-target="#drp-tab-1" type="button" role="tab" aria-controls="drp-tab-1" aria-selected="true">
                                    <i class="ti ti-user"></i> Profile
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="drp-t2" data-bs-toggle="tab" data-bs-target="#drp-tab-2" type="button" role="tab" aria-controls="drp-tab-2" aria-selected="false">
                                    <i class="ti ti-settings"></i> Setting
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="mysrpTabContent">
                            <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel" aria-labelledby="drp-t1" tabindex="0">
                                <a href="{{ route('profile') }}" class="dropdown-item">
                                    <i class="ti ti-edit-circle"></i>
                                    <span>Edit Profile</span>
                                </a>
                                <a href="{{ route('profile') }}" class="dropdown-item">
                                    <i class="ti ti-user"></i>
                                    <span>View Profile</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="ti ti-settings"></i>
                                    <span>Account Settings</span>
                                </a>
                                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ti ti-power"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                            <div class="tab-pane fade" id="drp-tab-2" role="tabpanel" aria-labelledby="drp-t2" tabindex="0">
                                <a href="#" class="dropdown-item">
                                    <i class="ti ti-help"></i>
                                    <span>Support</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="ti ti-user"></i>
                                    <span>Account Settings</span>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="ti ti-lock"></i>
                                    <span>Privacy Center</span>
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="ti ti-messages"></i>
                                    <span>Feedback</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>