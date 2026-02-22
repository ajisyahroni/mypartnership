<!-- Mobile Sidebar - Disamakan dengan Desktop -->
<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('user-management.home') }}" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="fa-solid fa-users text-success"></i>
        </span>
        <span class="hide-menu">Delegasi User</span>
    </a>
</li>

<li class="sidebar-item">
    <a class="sidebar-link text-dark has-arrow" href="#" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="fa-solid fa-handshake"></i>
        </span>
        <span class="hide-menu">Kerja Sama</span>
        <div id="notifMenuKerjaSamaMobile"></div>
    </a>
    <ul class="collapse first-level ps-4">
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('dokumen.home') }}">
                <i class="fa-solid fa-file-alt"></i> Dokumen Kerja Sama UMS
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('pengajuan.home') }}">
                <i class="fa-solid fa-folder-open"></i> Pengajuan Dokumen
                <div id="notifPengajuanKerjaSamaMobile"></div>
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('implementasi.home') }}">
                <i class="fa-solid fa-file-contract"></i> Lapor Implementasi
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('kuesioner.home') }}">
                <i class="fa-solid fa-clipboard-question"></i> Kuesioner Kerja Sama
            </a>
        </li>
    </ul>
</li>

<li class="sidebar-item">
    <a class="sidebar-link text-dark has-arrow" href="#" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="fa-solid fa-bell"></i>
        </span>
        <span class="hide-menu">Reminder</span>
    </a>
    <ul class="collapse first-level ps-4">
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('reminder.home') }}">
                <i class="fa-solid fa-database"></i> Data Reminder
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('reminder.home') }}">
                <i class="fa-solid fa-clock"></i> Reminder
            </a>
        </li>
    </ul>
</li>

<li class="sidebar-item">
    <a class="sidebar-link text-dark has-arrow" href="#" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="fa-solid fa-envelope"></i>
        </span>
        <span class="hide-menu">Mail</span>
    </a>
    <ul class="collapse first-level ps-4">
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('mail.home') }}">
                <i class="fa-solid fa-folder"></i> Mail Records
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('mail.setting') }}">
                <i class="fa-solid fa-gear"></i> Mail Settings
            </a>
        </li>
    </ul>
</li>

<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('dokumenPendukung.home') }}">
        <span class="sidebar-icon">
            <i class="fa-solid fa-folder"></i>
        </span>
        <span class="hide-menu">Dokumen Pendukung</span>
    </a>
</li>

<li class="sidebar-item">
    <a class="sidebar-link text-dark has-arrow" href="#" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="fa-solid fa-book"></i>
        </span>
        <span class="hide-menu">Referensi</span>
    </a>
    <ul class="collapse first-level ps-4">
        <li class="sidebar-item has-arrow">
            <a class="sidebar-link text-dark has-arrow" href="#">Dokumen Kerja Sama</a>
            <ul class="collapse second-level">
                <li class="sidebar-item"><a class="sidebar-link" href="{{ route('referensi.jenis_dokumen.home') }}"><i
                            class="fa fa-file-alt"></i> Jenis Dokumen</a></li>
                <li class="sidebar-item"><a class="sidebar-link"
                        href="{{ route('referensi.pelaksana_kerjasama.home') }}"><i class="fa fa-handshake"></i>
                        Pelaksana Kerja Sama</a></li>
                <li class="sidebar-item"><a class="sidebar-link"
                        href="{{ route('referensi.bentuk_kerjasama.home') }}"><i class="fa fa-handshake-simple"></i>
                        Bentuk Kerja Sama</a></li>
            </ul>
        </li>
        <li class="sidebar-item has-arrow">
            <a class="sidebar-link text-dark has-arrow" href="#">Dokumen Instansi</a>
            <ul class="collapse second-level">
                <li class="sidebar-item"><a class="sidebar-link" href="{{ route('referensi.lembaga_ums.home') }}"><i
                            class="fa fa-university"></i> Lembaga UMS</a></li>
                <li class="sidebar-item"><a class="sidebar-link"
                        href="{{ route('referensi.jenis_institusi_mitra.home') }}"><i class="fa fa-building"></i>
                        Jenis Institusi Mitra</a></li>
                <li class="sidebar-item"><a class="sidebar-link" href="{{ route('referensi.negara.home') }}"><i
                            class="fa fa-flag"></i> Negara</a></li>
                <li class="sidebar-item"><a class="sidebar-link" href="{{ route('referensi.negara.home') }}"><i
                            class="fa-solid fa-user-shield"></i> Jabatan UMS</a></li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('referensi.rangking_universitas.home') }}">
                <i class="fa fa-chart-line"></i> Rangking Universitas
            </a>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link text-dark" href="{{ route('referensi.pertanyaan_survei.home') }}">
                <i class="bx bx-notepad me-2"></i> Pertanyaan Survei
            </a>
        </li>
    </ul>
</li>

<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('pengajuan.setting') }}">
        <span class="sidebar-icon">
            <i class="fa-solid fa-gear"></i>
        </span>
        <span class="hide-menu">Setting</span>
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
