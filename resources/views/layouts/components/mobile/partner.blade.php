<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('potential_partner.home') }}" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="fa-solid fa-house me-2"></i>
        </span>
        <span class="hide-menu">Dashboard</span>
    </a>
</li>
<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('potential_partner.activity') }}" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="bx bx-run me-2"></i>
        </span>
        <span class="hide-menu">Daftar Mitra Potensial</span>
        <div id="notifPartnerMobile"></div>
    </a>
</li>
@if (session('current_role') == 'admin')
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('potential_partner.setting') }}" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-cog me-2"></i>
            </span>
            <span class="hide-menu">Setting Bobot Penilaian</span>
        </a>
    </li>
@endif
