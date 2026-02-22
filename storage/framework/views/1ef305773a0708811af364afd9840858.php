<?php if(session('current_role') == 'admin'): ?>
    <li class="nav-item">
        <a class="nav-link small-text <?php echo e(@$li_active == 'recognition' ? 'active' : ''); ?>"
            href="<?php echo e(route('recognition.InboundStaffRecognition')); ?>">
            <i class="fas fa-folder-open me-2"></i> Data Ajuan Rekrutmen Adjunct Professor
            <div id="notifRecognition"></div>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link small-text <?php echo e(@$li_active == 'dokumen_pendukung_rekognisi' ? 'active' : ''); ?>"
            href="<?php echo e(route('recognition.dokumenPendukungRecognition')); ?>">
            <i class="bx bx-cloud-download me-2"></i> Download File Pendukung
        </a>
    </li>
<?php elseif(in_array(session('current_role'), ['user', 'verifikator'])): ?>
    <li class="nav-item">
        <a class="nav-link small-text <?php echo e(@$li_active == 'data_ajuan' ? 'active' : ''); ?>"
            href="<?php echo e(route('recognition.dataAjuan')); ?>">
            <i class="bx bx-book-open me-2"></i> Data Adjunct Professor
            <div id="notifRecognition"></div>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link small-text <?php echo e(@$li_active == 'ajuan_saya' ? 'active' : ''); ?>"
            href="<?php echo e(route('recognition.dataAjuanSaya')); ?>">
            <i class="bx bx-paper-plane me-2"></i> Ajuan Saya
            <div id="notifRecognitionUser"></div>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link small-text <?php echo e(@$li_active == 'dokumen_pendukung_rekognisi' ? 'active' : ''); ?>"
            href="<?php echo e(route('recognition.dokumenPendukungRecognition')); ?>">
            <i class="bx bx-cloud-download me-2"></i> Download File Pendukung
        </a>
    </li>
<?php endif; ?>
<?php /**PATH /var/www/resources/views/layouts/components/menu_header/recognition.blade.php ENDPATH**/ ?>