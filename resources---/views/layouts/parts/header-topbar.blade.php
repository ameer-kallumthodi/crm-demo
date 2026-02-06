<div class="main-header">
    <div class="main-header-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="{{ asset('assets/img/kaiadmin/logo_light.svg') }}" alt="navbar brand" class="navbar-brand" height="20" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
            <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="submit" class="btn btn-search pe-1">
                            <i class="fa fa-search search-icon"></i>
                        </button>
                    </div>
                    <input type="text" placeholder="Search ..." class="form-control" />
                </div>
            </nav>

            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
                        <i class="fa fa-search"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-search animated fadeIn">
                        <form class="navbar-left navbar-form nav-search">
                            <div class="input-group">
                                <input type="text" placeholder="Search ..." class="form-control" />
                            </div>
                        </form>
                    </ul>
                </li>
                
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
                        <i class="fa fa-bell"></i>
                        <span class="notification">4</span>
                    </a>
                    <ul class="dropdown-menu dropdown-notifications animated fadeIn">
                        <li>
                            <div class="dropdown-header">Notifications</div>
                        </li>
                        <li>
                            <a href="#" class="dropdown-item">
                                <div class="notification-icon">
                                    <i class="fa fa-info-circle text-info"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-text">New lead added</div>
                                    <div class="notification-time">2 minutes ago</div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="dropdown-item">
                                <div class="notification-icon">
                                    <i class="fa fa-check-circle text-success"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-text">Lead converted</div>
                                    <div class="notification-time">5 minutes ago</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
                        <i class="fa fa-envelope"></i>
                        <span class="notification">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-messages animated fadeIn">
                        <li>
                            <div class="dropdown-header">Messages</div>
                        </li>
                        <li>
                            <a href="#" class="dropdown-item">
                                <div class="message">
                                    <div class="message-content">
                                        <div class="message-text">New message from client</div>
                                        <div class="message-time">10 minutes ago</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
                        <div class="avatar">
                            <img src="{{ asset('assets/img/profile.jpg') }}" alt="..." class="avatar-img rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <li>
                            <div class="dropdown-header">
                                <div class="avatar">
                                    <img src="{{ asset('assets/img/profile.jpg') }}" alt="..." class="avatar-img rounded-circle">
                                </div>
                                <div class="user-info">
                                    <h6 class="text-overflow m-0">{{ \App\Helpers\AuthHelper::getUserName() ?? 'User' }}</h6>
                                    <p class="text-muted mb-0">{{ \App\Helpers\AuthHelper::getRoleTitle() ?? 'User' }}</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fa fa-user"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fa fa-cog"></i> Settings
                            </a>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out-alt"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>