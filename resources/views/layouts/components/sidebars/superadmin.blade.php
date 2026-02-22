<!-- Sidebar navigation-->
<nav class="sidebar-nav scroll-sidebar" data-simplebar="">
    <ul id="sidebarnav">
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">Home</span>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('home.dashboard') }}" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-house text-primary"></i>
                </span>
                <span class="hide-menu">Dashboard</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('user-management.home') }}" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-users text-success"></i>
                </span>
                <span class="hide-menu">Delegasi User</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('role-permission.home') }}" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-shield-halved text-primary"></i>
                </span>
                <span class="hide-menu">Roles</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('backup.home') }}" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-database text-danger"></i>
                </span>
                <span class="hide-menu">Backup</span>
            </a>
        </li>
    </ul>
</nav>
<!-- End Sidebar navigation -->
