<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('dokumen.home') }}" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="bx bx-file me-2"></i>
        </span>
        <span class="hide-menu">Dokumen Kerja Sama UMS</span>
    </a>
</li>

<li class="nav-small-cap">
    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
    <span class="hide-menu">Menu</span>
</li>
@if (auth()->user()->place_state != null)
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('pengajuan.home') }}" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-send me-2"></i>
            </span>
            <span class="hide-menu">Pengajuan Kerja Sama</span>
            <div id="notifPengajuanKerjaSama"></div>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('implementasi.home') }}" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-check-circle me-2"></i>
            </span>
            <span class="hide-menu">Lapor Implementasi</span>
            <div id="notifImplementasiMobile"></div>
        </a>
    </li>
@endif
<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('dokumenPendukung.home') }}">
        <span class="sidebar-icon">
           <i class="fa-solid fa-folder"></i>
        </span>
        <span class="hide-menu">Dokumen Pendukung</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('survei.home') }}">
        <span class="sidebar-icon">
            <i class="bx bx-notepad"></i>
        </span>
        <span class="hide-menu">Survei</span>
    </a>
</li>
