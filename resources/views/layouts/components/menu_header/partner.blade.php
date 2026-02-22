<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'activity' ? 'active' : '' }}"
        href="{{ route('potential_partner.activity') }}">
        <i class="bx bx-run me-2"></i> Daftar Mitra Potensial
        <div id="notifPartner"></div>
    </a>
</li>

@if (session('current_role') == 'admin')
    <li class="nav-item">
        <a class="nav-link small-text {{ @$li_active == 'setting' ? 'active' : '' }}"
            href="{{ route('potential_partner.setting') }}">
            <i class="bx bx-cog me-2"></i> Setting Bobot Penilaian
        </a>
    </li>
@endif

{{-- <li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'reward' ? 'active' : '' }}"
        href="{{ route('potential_partner.reward') }}">
        <i class="bx bx-gift me-2"></i> Reward
    </a>
</li>
<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'profile' ? 'active' : '' }}"
        href="{{ route('potential_partner.profile') }}">
        <i class="bx bx-id-card me-2"></i> Profile
    </a>
</li> --}}
