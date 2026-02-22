<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'dokumen_kerjasama' ? 'active' : '' }}"
        href="{{ route('dokumen.home') }}"><i class="fa-regular fa-handshake me-2"></i> Dokumen
        Kerja Sama UMS</a>
</li>

@if (auth()->user()->place_state != null)
    <li class="nav-item">
        <a class="nav-link small-text {{ @$li_active == 'pengajuan_kerjasama' ? 'active' : '' }}"
            href="{{ route('pengajuan.home') }}"><i class="bx bx-send me-2"></i> Pengajuan Dokumen
            <div id="notifPengajuanKerjaSama"></div>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link small-text {{ @$li_active == 'lapor_implementasi' ? 'active' : '' }}"
            href="{{ route('implementasi.home') }}"><i class="bx bx-check-circle me-2"></i> Lapor
            Implementasi
            <div id="notifImplementasi"></div>
        </a>
    </li>
@endif
<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'dokumen_pendukung' ? 'active' : '' }}"
        href="{{ route('dokumenPendukung.home') }}">
        <i class="bx bx-folder me-2"></i> Dokumen Pendukung
    </a>
</li>
<li class="nav-item">
    <a class="nav-link small-text {{ @$li_active == 'survei' ? 'active' : '' }}" href="{{ route('survei.home') }}"><i
            class="bx bx-notepad me-2"></i> Survei</a>
</li>
