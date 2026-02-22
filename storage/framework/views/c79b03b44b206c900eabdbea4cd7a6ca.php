<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?php echo e(@$title ?? 'Dashboard'); ?></title>
    <meta name="description" content="" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="base_url" content="<?php echo e(config('app.url')); ?>">
    <meta name="user-id" content="<?php echo e(auth()->id()); ?>">

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
    <!-- Pastikan style ini sudah ada -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11/font/bootstrap-icons.css" rel="stylesheet">




    <!-- jQuery -->
    <!-- DataTables Core -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    

    <link rel="stylesheet" href="https://cdn.amcharts.com/lib/5/index.css">
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>


    <script>
        let csrftoken = '<?php echo e(csrf_token()); ?>';
    </script>

    <?php echo $__env->make('layouts.components.css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
    <!-- Fonts -->
    <!-- Tema jQuery UI (Cupertino) -->

</head>

<body>

    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        

        <style>
            .app-header {
                width: 100% !important;
                padding: 0px;
            }

            .body-wrapper {
                margin-left: 0px !important;
            }

            .body-wrapper>.container-fluid,
            .body-wrapper>.container-lg,
            .body-wrapper>.container-md,
            .body-wrapper>.container-sm,
            .body-wrapper>.container-xl,
            .body-wrapper>.container-xxl {
                max-width: 100%;
            }
        </style>

        <div class="body-wrapper">

            <?php echo $__env->make('layouts.components.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="container-fluid body-konten">

                <?php echo $__env->yieldContent('contents'); ?>

            </div>
            <div class="modal fade" id="modal-detail-log" aria-labelledby="DetailLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bx bx-detail"></i> Draft Log</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="content-detail-log">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $__env->make('layouts.components.modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('layouts.components.button-whatsapp', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('layouts.components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        </div>
    </div>
    
    <script>
        var menuSession = <?php echo json_encode(session('menu'), 15, 512) ?>;
        var RoleGlobal = <?php echo json_encode(session('current_role'), 15, 512) ?>;
    </script>
    
    <!-- jQuery UI -->
    <?php echo $__env->make('layouts.components.js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('layouts.components.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/dataTables.fixedColumns.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/fixedColumns.dataTables.js"></script>

    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.bootstrap5.min.js"></script>

    <!-- Plugins Export (Pastikan ini ada!) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css">

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script src="<?php echo e(asset('js/notifikasi.js')); ?>"></script>
     <script>
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip-body';

        const arrow = document.createElement('div');
        arrow.className = 'tooltip-arrow';

        tooltip.appendChild(arrow);
        document.body.appendChild(tooltip);

        document.addEventListener('mouseover', e => {
            const el = e.target.closest('[data-title-tooltip]');
            if (!el) return;

            tooltip.textContent = el.getAttribute('data-title-tooltip');
            tooltip.appendChild(arrow);

            const rect = el.getBoundingClientRect();

            const top = rect.top + window.scrollY - tooltip.offsetHeight - 8;
            const left = rect.left + window.scrollX + rect.width / 2;

            tooltip.style.top = `${top}px`;
            tooltip.style.left = `${left}px`;
            tooltip.style.transform = 'translateX(-50%) translateY(6px)';

            tooltip.classList.add('show');
        });

        document.addEventListener('mouseout', e => {
            if (e.target.closest('[data-title-tooltip]')) {
                tooltip.classList.remove('show');
            }
        });
    </script>
    <script>
        let activeDropdown = null;

        document.addEventListener('click', e => {
            const btn = e.target.closest('.btn-dropdown-print');

            // klik di luar dropdown → tutup
            if (!btn && activeDropdown) {
                activeDropdown.remove();
                activeDropdown = null;
                return;
            }

            if (!btn) return;

            e.preventDefault();

            // tutup dropdown sebelumnya
            if (activeDropdown) {
                activeDropdown.remove();
                activeDropdown = null;
            }

            const contentId = btn.getAttribute('data-dropdown-content');
            const source = document.getElementById(contentId);
            if (!source) return;

            const dropdown = document.createElement('div');
            dropdown.className = 'custom-dropdown';
            dropdown.innerHTML = source.innerHTML;

            document.body.appendChild(dropdown);

            const rect = btn.getBoundingClientRect();
            const top = rect.bottom + window.scrollY + 6;
            const left = rect.left + window.scrollX;

            dropdown.style.top = `${top}px`;
            dropdown.style.left = `${left}px`;

            // Tambahkan event listener untuk button export
            dropdown.querySelectorAll('.btn-export-proposal').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    let id = this.getAttribute('data-id_hibah');
                    window.open("/hibah-kerjasama/export_proposal/" + id, "_blank");
                    activeDropdown.remove();
                    activeDropdown = null;
                });
            });

            dropdown.querySelectorAll('.btn-export-laporan').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    let id = this.getAttribute('data-id_hibah');
                    window.open("/hibah-kerjasama/export_laporan/" + id, "_blank");
                    activeDropdown.remove();
                    activeDropdown = null;
                });
            });

            activeDropdown = dropdown;
        });
    </script>
    <script>
        var placeStateSession = <?php echo json_encode(auth()->user()->place_state, 15, 512) ?>;
        if (placeStateSession == null) {
            Swal.fire({
                icon: "info",
                title: "Profil User",
                html: "Akun anda belum mempunyai lembaga. Silahkan hubungi admin untuk menambahkan lembaga pada akun anda.",
            });
        }
    </script>
</body>

</html>
<?php /**PATH /var/www/resources/views/layouts/app.blade.php ENDPATH**/ ?>