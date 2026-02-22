<!-- Sidebar navigation-->
<nav class="sidebar-nav scroll-sidebar" data-simplebar="" style="margin-bottom: 50px;">
    <ul id="sidebarnav">
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            {{-- <span class="hide-menu">Home</span> --}}
            <span class="sidebar-icon">Home</span>
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
        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('kuesioner.home') }}" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-clipboard-question text-warning"></i> <!-- Ikon baru -->
                </span>
                <span class="hide-menu">Kuesioner Kerja Sama</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-bell text-warning"></i> <!-- Ikon Reminder -->
                </span>
                <span class="hide-menu">Reminder</span>
            </a>
            <ul class="collapse first-level ps-4">
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('reminder.home') }}"
                        style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-database text-primary"></i>
                        <span class="hide-menu">Data Reminder</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('reminder.home') }}"
                        style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-clock text-success"></i>
                        <span class="hide-menu">Reminder</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-envelope text-warning"></i> <!-- Ikon Mail -->
                </span>
                <span class="hide-menu">Mail</span>
            </a>
            <ul class="collapse first-level ps-4">
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('mail.home') }}"
                        style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-folder text-warning"></i> <!-- Ikon Mail Records -->
                        <span class="hide-menu">Mail Records</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('mail.home') }}"
                        style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-gear text-success"></i> <!-- Ikon Mail Settings -->
                        <span class="hide-menu">Mail Settings</span>
                    </a>
                </li>
            </ul>
        </li>


        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="sidebar-icon">Setting</span>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" href="{{ route('user-management.home') }}" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-users text-success"></i>
                </span>
                <span class="hide-menu">Delegasi User</span>
            </a>
        </li>
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
            <span class="sidebar-icon">Master Referensi</span>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-book text-warning"></i>
                </span>
                <span class="hide-menu">Dokumen Kerja Sama</span>
            </a>
            <ul class="collapse first-level ps-4">
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-file-alt text-primary"></i>
                        <span class="hide-menu">Jenis Dokumen</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-handshake text-success"></i>
                        <span class="hide-menu">Pelaksana Kerja Sama</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-handshake-simple text-success"></i>
                        <span class="hide-menu">Bentuk Kerja Sama</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-book"></i>
                </span>
                <span class="hide-menu">Instansi</span>
            </a>
            <ul class="collapse first-level ps-4">
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-university"></i>
                        <span class="hide-menu">Lembaga UMS</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-building"></i>
                        <span class="hide-menu">Jenis Institusi Mitra</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-school"></i>
                        <span class="hide-menu">Fakultas</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-flag"></i>
                        <span class="hide-menu">Negara</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="" style="font-size: 0.85rem; padding: 5px 10px;">
                        <i class="fa-solid fa-chart-line"></i>
                        <span class="hide-menu">Rangking Universitas</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
<!-- End Sidebar navigation -->
