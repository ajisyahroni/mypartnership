<li class="sidebar-item">
    <a class="sidebar-link text-dark" href="<?php echo e(route('recognition.home')); ?>" aria-expanded="false">
        <span class="sidebar-icon">
            <i class="fa-solid fa-house me-2"></i>
        </span>
        <span class="hide-menu">Dashboard</span>
    </a>
</li>
<?php if(session('current_role') == 'admin'): ?>
    <!-- Mobile Sidebar - Disamakan dengan Desktop -->
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="<?php echo e(route('recognition.InboundStaffRecognition')); ?>" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="fas fa-folder-open me-2"></i>
            </span>
            <span class="hide-menu">Data Ajuan Rekrutmen Adjunct Professor</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="<?php echo e(route('recognition.dokumenPendukungRecognition')); ?>"
            aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-cloud-download me-2"></i>
            </span>
            <span class="hide-menu">Download File Pendukung</span>
        </a>
    </li>
<?php elseif(in_array(session('current_role'), ['user', 'verifikator'])): ?>
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="<?php echo e(route('recognition.dataAjuan')); ?>" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-book-open me-2"></i>
            </span>
            <span class="hide-menu">Data Adjunct Professor</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="<?php echo e(route('recognition.dataAjuanSaya')); ?>" aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-paper-plane me-2"></i>
            </span>
            <span class="hide-menu">Ajuan Saya</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link text-dark" href="<?php echo e(route('recognition.dokumenPendukungRecognition')); ?>"
            aria-expanded="false">
            <span class="sidebar-icon">
                <i class="bx bx-cloud-download  me-2"></i>
            </span>
            <span class="hide-menu">Download File Pendukung</span>
        </a>
    </li>
<?php endif; ?>
<?php /**PATH /var/www/resources/views/layouts/components/mobile/recognition.blade.php ENDPATH**/ ?>