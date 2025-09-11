<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('assets/mantis/images/logo-dark.svg') }}" class="img-fluid logo-lg" alt="logo">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Navigation</label>
                </li>
                @if(\App\Helpers\PermissionHelper::can_access_menu('dashboard/index'))
                <li class="pc-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-dashboard"></i>
                        </span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                @endif
                
                @if(\App\Helpers\PermissionHelper::can_access_menu('leads/index'))
                <li class="pc-item {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                    <a href="{{ route('leads.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="pc-mtext">Leads</span>
                    </a>
                </li>
                @endif
                
                @if(has_permission('admin/telecallers/index') || has_permission('admin/settings/index'))
                <li class="pc-item pc-caption">
                    <label>Management</label>
                </li>
                @endif
                
                @if(has_permission('admin/telecallers/index'))
                <li class="pc-item pc-caption">
                    <label>User Management</label>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.telecallers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.telecallers.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-phone"></i>
                        </span>
                        <span class="pc-mtext">Telecallers</span>
                    </a>
                </li>
                @endif
                
                @if(has_permission('admin/settings/index'))
                <li class="pc-item pc-caption">
                    <label>Settings</label>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.lead-statuses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lead-statuses.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-flag"></i>
                        </span>
                        <span class="pc-mtext">Lead Status</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.lead-sources.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lead-sources.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-source-code"></i>
                        </span>
                        <span class="pc-mtext">Lead Source</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.courses.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="pc-mtext">Courses</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.countries.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-world"></i>
                        </span>
                        <span class="pc-mtext">Countries</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.teams.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-users-group"></i>
                        </span>
                        <span class="pc-mtext">Teams</span>
                    </a>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-settings"></i>
                        </span>
                        <span class="pc-mtext">Site Settings</span>
                    </a>
                </li>
                @endif
                
                @if(\App\Helpers\PermissionHelper::can_access_menu('profile/index'))
                <li class="pc-item {{ request()->routeIs('profile*') ? 'active' : '' }}">
                    <a href="{{ route('profile') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user"></i>
                        </span>
                        <span class="pc-mtext">Profile</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
