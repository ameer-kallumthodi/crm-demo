<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('storage/logo.png') }}" class="img-fluid logo-lg" alt="logo" 
                     onerror="this.src='{{ asset('assets/mantis/images/logo-dark.svg') }}'">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Navigation</label>
                </li>
                @if(has_permission('dashboard/index'))
                <li class="pc-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-dashboard"></i>
                        </span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                @endif
                
                @if(has_permission('leads/index'))
                <li class="pc-item {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                    <a href="{{ route('leads.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="pc-mtext">Leads</span>
                    </a>
                </li>
                @endif
                
                {{-- User Management Section --}}
                @if(has_permission('admin/telecallers/index') || has_permission('admin/admins/index'))
                <li class="pc-item pc-caption">
                    <label>User Management</label>
                </li>
                @if(has_permission('admin/telecallers/index'))
                <li class="pc-item {{ request()->routeIs('admin.telecallers.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.telecallers.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-phone"></i>
                        </span>
                        <span class="pc-mtext">Telecallers</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/admins/index'))
                <li class="pc-item {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.admins.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-shield"></i>
                        </span>
                        <span class="pc-mtext">Admin Users</span>
                    </a>
                </li>
                @endif
                @endif
                
                {{-- Academic Assistants Section --}}
                @if(has_permission('admin/academic-assistants/index'))
                <li class="pc-item {{ request()->routeIs('admin.academic-assistants.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.academic-assistants.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user-plus"></i>
                        </span>
                        <span class="pc-mtext">Academic Assistants</span>
                    </a>
                </li>
                @endif
                
                {{-- Lead Management Section --}}
                @if(has_permission('admin/lead-statuses/index') || has_permission('admin/lead-sources/index'))
                <li class="pc-item pc-caption">
                    <label>Lead Management</label>
                </li>
                @if(has_permission('admin/lead-statuses/index'))
                <li class="pc-item {{ request()->routeIs('admin.lead-statuses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lead-statuses.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-flag"></i>
                        </span>
                        <span class="pc-mtext">Lead Status</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/lead-sources/index'))
                <li class="pc-item {{ request()->routeIs('admin.lead-sources.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lead-sources.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-tag"></i>
                        </span>
                        <span class="pc-mtext">Lead Source</span>
                    </a>
                </li>
                @endif
                @endif

                
                {{-- Reports Section --}}
                @if(has_permission('admin/reports/leads'))
                <li class="pc-item pc-caption">
                    <label>Reports</label>
                </li>
                <li class="pc-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reports.leads') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-chart-pie"></i>
                        </span>
                        <span class="pc-mtext">Lead Reports</span>
                    </a>
                </li>
                @endif
                
                {{-- Converted Leads Section --}}
                @if(has_permission('admin/converted-leads/index'))
                <li class="pc-item {{ request()->routeIs('admin.converted-leads.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.converted-leads.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user-check"></i>
                        </span>
                        <span class="pc-mtext">Converted Leads</span>
                    </a>
                </li>
                @endif
                
                {{-- Master Data Section --}}
                @if(has_permission('admin/courses/index') || has_permission('admin/countries/index') || has_permission('admin/teams/index'))
                <li class="pc-item pc-caption">
                    <label>Master Data</label>
                </li>
                @if(has_permission('admin/courses/index'))
                <li class="pc-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.courses.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="pc-mtext">Courses</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/countries/index'))
                <li class="pc-item {{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.countries.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-world"></i>
                        </span>
                        <span class="pc-mtext">Countries</span>
                    </a>
                </li>
                @endif
                @if(has_permission('admin/teams/index'))
                <li class="pc-item {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.teams.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-users"></i>
                        </span>
                        <span class="pc-mtext">Teams</span>
                    </a>
                </li>
                @endif
                @endif
                
                {{-- Settings Section --}}
                @if(has_permission('admin/website/settings') || has_permission('profile/index'))
                <li class="pc-item pc-caption">
                    <label>Settings</label>
                </li>
                @if(has_permission('admin/website/settings'))
                <li class="pc-item {{ request()->routeIs('admin.website.settings.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.website.settings') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-settings"></i>
                        </span>
                        <span class="pc-mtext">Website Settings</span>
                    </a>
                </li>
                @endif
                @if(has_permission('profile/index'))
                <li class="pc-item {{ request()->routeIs('profile*') ? 'active' : '' }}">
                    <a href="{{ route('profile') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-user"></i>
                        </span>
                        <span class="pc-mtext">Profile</span>
                    </a>
                </li>
                @endif
                @endif
            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
