<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="{{ route('recognition.home') }}" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="fa-solid fa-house me-2"></i>
        </span>
        <span class="hide-menu">Dashboard</span>
    </a>
</li>
@if (session('current_role') == 'admin')
    <!-- Mobile Sidebar - Disamakan dengan Desktop -->
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('recognition.InboundStaffRecognition') }}" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="fas fa-folder-open me-2"></i>
            </span>
            <span class="hide-menu">Data Ajuan Rekrutmen Adjunct Professor</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('recognition.dokumenPendukungRecognition') }}"
            aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-cloud-download me-2"></i>
            </span>
            <span class="hide-menu">Download File Pendukung</span>
        </a>
    </li>
@elseif(in_array(session('current_role'), ['user', 'verifikator']))
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('recognition.dataAjuan') }}" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-book-open me-2"></i>
            </span>
            <span class="hide-menu">Data Adjunct Professor</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('recognition.dataAjuanSaya') }}" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-paper-plane me-2"></i>
            </span>
            <span class="hide-menu">Ajuan Saya</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="{{ route('recognition.dokumenPendukungRecognition') }}"
            aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-cloud-download  me-2"></i>
            </span>
            <span class="hide-menu">Download File Pendukung</span>
        </a>
    </li>
@endif
