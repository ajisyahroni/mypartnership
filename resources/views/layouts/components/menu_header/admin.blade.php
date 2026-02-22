<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'delegasi_user' ? 'active' : '' }}"
        href="{{ route('user-management.home') }}">
        <i class="bx bx-user me-2"></i> Delegasi User
    </a>
</li>

<!-- Dropdown "Kerja Sama" -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle small-text {{ @$li_active == 'kerjasama' ? 'active' : '' }}" href="#"
        id="kerjaSamaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-regular fa-handshake me-2"></i> Kerja Sama
        <div id="notifMenuKerjaSama"></div>
    </a>
    <ul class="dropdown-menu" aria-labelledby="kerjaSamaDropdown">
        <li>
            <a class="dropdown-item {{ @$li_sub_active == 'dokumen_kerjasama_ums' ? 'active' : '' }}"
                href="{{ route('dokumen.home') }}">
                <i class="bx bx-file me-2"></i> Dokumentasi Kerja UMS
            </a>
        </li>
        <li class="position-relative">
            <a class="dropdown-item {{ @$li_sub_active == 'pengajuan_kerjasama' ? 'active' : '' }}"
                href="{{ route('pengajuan.home') }}">
                <i class="bx bx-send me-2"></i> Pengajuan Dokumen
                <div id="notifPengajuanKerjaSama"></div>
            </a>
        </li>
        <li class="position-relative">
            <a class="dropdown-item {{ @$li_sub_active == 'lapor_implementasi' ? 'active' : '' }}"
                href="{{ route('implementasi.home') }}">
                <i class="bx bx-check-circle me-2"></i> Lapor Implementasi
                <div id="notifImplementasi"></div>
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ @$li_sub_active == 'Kuesioner_kerjasama' ? 'active' : '' }}"
                href="{{ route('kuesioner.home') }}">
                <i class="bx bx-list-check me-2"></i> Kuesioner Kerja Sama
            </a>
        </li>
    </ul>
</li>


<!-- Dropdown "Reminder" -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle small-text {{ @$li_active == 'reminder' ? 'active' : '' }}" href="#"
        id="ReminderDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-bell me-2"></i> Reminder
    </a>
    <ul class="dropdown-menu" aria-labelledby="ReminderDropdown">
        <li><a class="dropdown-item {{ @$li_sub_active == 'reminder-list' ? 'active' : '' }}"
                href="{{ route('reminder.list') }}"><i class="bx bx-calendar me-2"></i> Data
                Reminder</a></li>
        <li><a class="dropdown-item {{ @$li_sub_active == 'reminder' ? 'active' : '' }}"
                href="{{ route('reminder.home') }}"><i class="bx bx-alarm me-2"></i> Reminder</a>
        </li>
    </ul>
</li>

<!-- Dropdown "Mail" -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle small-text {{ @$li_active == 'mail' ? 'active' : '' }}" href="#"
        id="MailDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-envelope me-2"></i> Mail
    </a>
    <ul class="dropdown-menu" aria-labelledby="MailDropdown">
        <li><a class="dropdown-item {{ @$li_sub_active == 'mail_records' ? 'active' : '' }}"
                href="{{ route('mail.home') }}"><i class="bx bx-archive me-2"></i> Mail
                Records</a>
        </li>
        <li><a class="dropdown-item {{ @$li_sub_active == 'mail_settings' ? 'active' : '' }}"
                href="{{ route('mail.setting') }}"><i class="bx bx-cog me-2"></i> Mail Settings</a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'dokumen_pendukung' ? 'active' : '' }}"
        href="{{ route('dokumenPendukung.home') }}">
        <i class="bx bx-folder me-2"></i> Dokumen Pendukung
    </a>
</li>

<!-- Dropdown "Referensi" -->
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle small-text {{ @$li_active == 'referensi' ? 'active' : '' }}" href="#"
        id="ReferensiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-book me-2"></i> Referensi
    </a>
    <ul class="dropdown-menu" aria-labelledby="ReferensiDropdown">
        <li class="dropdown-submenu">
            <a class="dropdown-item {{ @$li_sub_active == 'dokumen_kerjasama' ? 'active' : '' }} dropdown-toggle"
                href="#" id="submenuDropdown1">
                <i class="bx bx-folder-open me-2"></i> Dokumen Kerja Sama
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="submenuDropdown1">
                <li><a class="dropdown-item {{ @$li_sub_menu_active == 'jenis_dokumen' ? 'active' : '' }}"
                        href="{{ route('referensi.jenis_dokumen.home') }}"><i class="bx bx-file me-2"></i> Jenis
                        Dokumen</a></li>
                <li><a class="dropdown-item {{ @$li_sub_menu_active == 'pelaksana_kerjasama' ? 'active' : '' }}"
                        href="{{ route('referensi.pelaksana_kerjasama.home') }}"><i class="bx bx-user-check me-2"></i>
                        Pelaksana Kerja
                        Sama</a>
                </li>
                <li><a class="dropdown-item {{ @$li_sub_menu_active == 'bentuk_kerjasama' ? 'active' : '' }}"
                        href="{{ route('referensi.bentuk_kerjasama.home') }}"><i class="bx bx-briefcase me-2"></i>
                        Bentuk Kerja Sama</a>
                </li>
            </ul>
        </li>
        <li class="dropdown-submenu">
            <a class="dropdown-item {{ @$li_sub_active == 'dokumen_instansi' ? 'active' : '' }} dropdown-toggle"
                href="#" id="submenuDropdown2">
                <i class="bx bx-building me-2"></i> Dokumen Instansi
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="submenuDropdown2">
                <li><a class="dropdown-item {{ @$li_sub_menu_active == 'lembaga_ums' ? 'active' : '' }}"
                        href="{{ route('referensi.lembaga_ums.home') }}"><i class="fa fa-landmark me-2"></i>
                        Lembaga UMS</a></li>
                <li><a class="dropdown-item {{ @$li_sub_menu_active == 'jenis_institusi_mitra' ? 'active' : '' }}"
                        href="{{ route('referensi.jenis_institusi_mitra.home') }}"><i
                            class="bx bx-network-chart me-2"></i> Jenis Institusi
                        Mitra</a></li>
                <li><a class="dropdown-item {{ @$li_sub_menu_active == 'negara' ? 'active' : '' }}"
                        href="{{ route('referensi.negara.home') }}"><i class="bx bx-world me-2"></i> Negara</a></li>
                <li><a class="dropdown-item {{ @$li_sub_menu_active == 'jabatan' ? 'active' : '' }}"
                        href="{{ route('referensi.jabatan.home') }}"><i class="fa-solid fa-user-shield me-2"></i> Jabatan UMS</a></li>
            </ul>
        </li>
        <li><a class="dropdown-item {{ @$li_sub_active == 'rangking_universitas' ? 'active' : '' }}"
                href="{{ route('referensi.rangking_universitas.home') }}"><i class="bx bx-bar-chart-alt me-2"></i>
                Rangking Universitas</a>
        </li>
        <li><a class="dropdown-item {{ @$li_sub_active == 'pertanyaan_survei' ? 'active' : '' }}"
                href="{{ route('referensi.pertanyaan_survei.home') }}"><i class="bx bx-notepad me-2"></i>
                Pertanyaan Survei</a>
        </li>
    </ul>
</li>
<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'setting' ? 'active' : '' }}"
        href="{{ route('pengajuan.setting') }}">
        <i class="bx bx-cog me-2"></i> Setting
    </a>
</li>
<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'survei' ? 'active' : '' }}" href="{{ route('survei.home') }}">
        <i class="bx bx-notepad me-2"></i> Survei
    </a>
</li>
