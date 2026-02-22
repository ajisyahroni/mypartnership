<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('hibah.home') }}" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="fa-solid fa-house me-2"></i>
        </span>
        <span class="hide-menu">Dashboard</span>
    </a>
</li>

<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('hibah.ajuan') }}" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="bx bx-file me-2"></i>
        </span>
        <span class="hide-menu">Ajuan Hibah</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('hibah.dokumenPendukung') }}" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="bx bx-paper-plane me-2"></i>
        </span>
        <span class="hide-menu">Dokumen Pendukung Hibah</span>
    </a>
</li>

@if (session('current_role') == 'admin')
    <!-- Mobile Sidebar - Disamakan dengan Desktop -->
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('hibah.setting') }}" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-cog  me-2"></i>
            </span>
            <span class="hide-menu">Setting</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('referensi.jenis_hibah.home') }}" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-folder me-2"></i>
            </span>
            <span class="hide-menu">Referensi Jenis Hibah</span>
        </a>
    </li>
@endif
