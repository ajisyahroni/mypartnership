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
            <a class="sidebar-link" href="{{ route('dokumen.home') }}" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-file-alt text-danger"></i>
                </span>
                <span class="hide-menu">Dokumen Kerja Sama UMS</span>
            </a>
        </li>

        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="hide-menu">Menu</span>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('pengajuan.home') }}" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-folder-open text-warning"></i>
                </span>
                <span class="hide-menu">Pengajuan Kerja Sama</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('implementasi.home') }}" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-file-contract text-primary"></i>
                </span>
                <span class="hide-menu">Lapor Implementasi</span>
            </a>
        </li>

    </ul>
</nav>
<!-- End Sidebar navigation -->
