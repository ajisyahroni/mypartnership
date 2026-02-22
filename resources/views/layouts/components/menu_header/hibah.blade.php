<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'ajuan' ? 'active' : '' }}" href="{{ route('hibah.ajuan') }}">
        <i class="bx bx-clipboard me-2"></i> Ajuan Hibah Kerja Sama
        <div id="notifHibah"></div>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'dokumenPendukungHibah' ? 'active' : '' }}"
        href="{{ route('hibah.dokumenPendukung') }}">
        <i class="bx bx-file me-2"></i> Dokumen Pendukung Hibah
    </a>
</li>

@if (session('current_role') == 'admin')
    <li class="nav-item">
        <a class="nav-link small-text {{ @$li_active == 'setting' ? 'active' : '' }}"
            href="{{ route('hibah.setting') }}">
            <i class="bx bx-cog me-2"></i> Setting
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link small-text {{ @$li_active == 'jenis_hibah' ? 'active' : '' }}"
            href="{{ route('referensi.jenis_hibah.home') }}">
            <i class="bx bx-folder me-2"></i> Referensi Jenis Hibah
        </a>
    </li>
@endif
