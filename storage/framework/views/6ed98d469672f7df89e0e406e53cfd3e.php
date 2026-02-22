<?php $__env->startSection('contents'); ?>
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="mb-4">
                    <a href="<?php echo e(route('recognition.tambah')); ?>"
                        class="btn btn-success rounded-3 d-flex align-items-center justify-content-center py-2">
                        <i class="fas fa-user-plus me-2"></i> Tambah Data Ajuan Adjunct Professor
                    </a>
                </div>

                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card shadow-sm border-0 rounded-3 text-center h-100 d-flex flex-column">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <!-- Tombol tambah -->
                            <!-- Konten card -->
                            <div class="flex-grow-1 d-flex flex-column justify-content-center">
                                <i class="fas fa-paper-plane fa-3x text-info mb-3"></i>
                                <h4 class="fw-bold mb-1"><?php echo e(@$ajuan_baru); ?></h4>
                                <p class="mb-0 text-muted">Ajuan Baru</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Ajuan Selesai -->
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card shadow-sm border-0 rounded-3 text-center h-100 d-flex flex-column">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <i class="fas fa-check fa-3x text-success mb-3"></i>
                            <h4 class="fw-bold mb-1"><?php echo e(@$ajuan_selesai); ?></h4>
                            <p class="mb-0 text-muted">Ajuan Selesai</p>
                        </div>
                    </div>
                </div>


                <div class="col-12 mb-4">
                    <?php if(count(@$dokumenPendukung) == 0): ?>
                    <?php else: ?>
                        <div class="accordion custom-accordion" id="customAccordion">
                            <?php $__currentLoopData = @$dokumenPendukung; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <button class="accordion-button flex-grow-1" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse<?php echo e($item->id); ?>"
                                                aria-expanded="false">
                                                <?php echo e($item->nama_dokumen); ?>

                                                <?php
                                                    $filePath =
                                                        $item->link_dokumen ??
                                                        asset('storage/' . rawurlencode($item->file_dokumen));
                                                ?>
                                                <a href="<?php echo e($filePath); ?>" target="_blank" download
                                                    class="btn btn-sm btn-light ms-2 me-3"
                                                    data-title-tooltip="Download File"
                                                    onclick="event.stopPropagation();">
                                                    <i class="bx bx-download"></i>
                                                </a>
                                                <?php if(session('current_role') == 'admin'): ?>
                                                    <div class="text-center" onclick="event.stopPropagation();">
                                                        <label class="toggle-switch">
                                                            <input type="checkbox" class="check-status"
                                                                data-id="<?php echo e($item->id); ?>"
                                                                <?php echo e($item->is_active == '1' ? 'checked' : ''); ?>>
                                                            <div class="toggle-switch-background">
                                                                <div class="toggle-switch-handle"></div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                <?php endif; ?>
                                            </button>
                                        </div>
                                    </h2>

                                    <div id="collapse<?php echo e($item->id); ?>" class="accordion-collapse collapse">
                                        <div class="accordion-body">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <?php if(session('current_role') == 'admin'): ?>
                                                        <div class="col-12 d-flex gap-2 mb-3">
                                                            <button class="btn w-100 btn-warning btn-edit"
                                                                data-uuid="<?php echo e($item->uuid); ?>"
                                                                data-link_dokumen="<?php echo e($item->link_dokumen); ?>"
                                                                data-nama_dokumen="<?php echo e($item->nama_dokumen); ?>">
                                                                <i class="bx bx-edit"></i> Edit
                                                            </button>
                                                            <button class="btn w-100 btn-danger btn-hapus"
                                                                data-uuid="<?php echo e($item->uuid); ?>"
                                                                data-nama_dokumen="<?php echo e($item->nama_dokumen); ?>">
                                                                <i class="bx bx-trash"></i> Hapus
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="col-12 iframe-container" 
                                                        data-uuid="<?php echo e($item->uuid); ?>"
                                                        data-loaded="false">
                                                        <div class="loading-spinner text-center py-5">
                                                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                            <p class="mt-3 text-muted fw-medium">Memuat dokumen...</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pemberitahuan" aria-labelledby="PemberitahuanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <!-- Header -->
                    <div class="modal-header border-0 bg-primary bg-gradient text-white py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold mb-0" id="PemberitahuanLabel">Pemberitahuan Verifikator</h6>
                                <small class="opacity-75" style="font-size: 0.75rem;">MyPartnership</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3" id="konten-pemberitahuan">
                        
                        <!-- Info Card -->
                        <div class="alert alert-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3 mb-3 py-2">
                            <div class="d-flex align-items-start gap-2">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                    <i class="bi bi-person-badge text-light fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 small">
                                        <strong>Anda masuk sebagai:</strong> <span class="badge bg-primary px-2 py-1">Verifikator</span>
                                    </p>
                                    <p class="mb-2 text-muted" style="font-size: 0.8rem;">Tanggung jawab Anda:</p>

                                    <div class="d-flex align-items-start gap-2 mb-2">
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 0.9rem;"></i>
                                        <span style="font-size: 0.8rem;">Memvalidasi <strong>pengajuan rekognisi dosen</strong></span>
                                    </div>

                                    <div class="alert alert-warning bg-opacity-10 border-0 mb-0 py-1 px-2">
                                        <small class="d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                            <span>Pastikan data sesuai sebelum memberikan persetujuan</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Card -->
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-body text-center p-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                    <i class="bi bi-file-earmark-check text-light fs-3"></i>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Verifikasi Pengajuan</h6>
                                <p class="text-muted mb-2" style="font-size: 0.75rem;">Validasi rekognisi dosen</p>
                                <a href="<?php echo e(route('recognition.dataAjuan')); ?>" 
                                class="btn btn-primary btn-sm w-100 d-flex justify-content-center align-items-center gap-2 rounded-pill">
                                    <i class="bi bi-arrow-right-circle"></i>
                                    <span class="fw-semibold">Mulai Verifikasi</span>
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0 bg-light bg-gradient py-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 rounded-pill px-3" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i>
                            <span>Tutup</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        var getData = "<?php echo e(route('kuesioner.getData')); ?>"
        var roleDashboard = <?php echo json_encode(session('current_role'), 15, 512) ?>;
        var notif_verifikator = <?php echo json_encode($notif_verifikator, 15, 512) ?>;
    
        $(document).ready(function() {
            if (roleDashboard == 'verifikator' && notif_verifikator > 0) {
                $("#modal-pemberitahuan").modal('show');
            }
        })

        $(document).ready(function () {
            loadAllDokumenIframe();

            function loadAllDokumenIframe() {
                $(".iframe-container").each(function () {
                    $(this).find(".loading-spinner").show();
                });

                $.ajax({
                    url: "/recognition/dokumen-pendukung/load-all-iframe",
                    method: "GET",
                    dataType: "json",
                    beforeSend: function () {
                        console.log("Memuat semua dokumen...");
                    },
                    success: function (response) {
                        if (response.success && response.data) {
                            response.data.forEach(function (item) {
                                const container = $(
                                    `.iframe-container[data-uuid="${item.uuid}"]`
                                );

                                if (container.length) {
                                    const loadingSpinner =
                                        container.find(".loading-spinner");

                                    loadingSpinner.fadeOut(300, function () {
                                        container.html(item.html);
                                        container.data("loaded", true);

                                        const iframe = container.find("iframe");
                                        if (iframe.length) {
                                            iframe.css("opacity", "0");

                                            iframe.on("load", function () {
                                                $(this).animate({ opacity: 1 }, 400);
                                            });

                                            iframe.on("error", function () {
                                                handleIframeError($(this));
                                            });
                                        }
                                    });
                                }
                            });

                            console.log(`Berhasil memuat ${response.total} dokumen`);
                        } else {
                            showGlobalError("Gagal memuat dokumen");
                        }
                    },
                    error: function (xhr, status, error) {
                        let errorMessage = "Gagal memuat dokumen. Silakan coba lagi.";

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        showGlobalError(errorMessage);
                        console.error("Error:", errorMessage);
                    },
                    timeout: 60000,
                });
            }

            function showGlobalError(message) {
                $(".iframe-container").each(function () {
                    const container = $(this);
                    const errorHtml = `
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bx bx-error-circle me-2" style="font-size: 24px;"></i>
                            <div>
                                <strong>Error!</strong> ${message}
                            </div>
                        </div>
                    `;

                    container.find(".loading-spinner").fadeOut(200, function () {
                        container.html(errorHtml);
                    });
                });

                const retryButton = `
                    <div class="text-center my-3">
                        <button class="btn btn-primary retry-load-all">
                            <i class="bx bx-refresh me-1"></i> Muat Ulang Semua Dokumen
                        </button>
                    </div>
                `;

                if (!$(".retry-load-all").length) {
                    $(".accordion").before(retryButton);
                }
            }

            $(document).on("click", ".retry-load-all", function () {
                $(this).closest("div").remove();

                $(".iframe-container").each(function () {
                    $(this).data("loaded", false);
                    $(this).html(`
                        <div class="loading-spinner text-center py-5">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted fw-medium">Memuat dokumen...</p>
                        </div>
                    `);
                });

                loadAllDokumenIframe();
            });

            window.handleIframeError = function (iframeElement) {
                const container = iframeElement.closest(".iframe-container");
                const uuid = container.data("uuid");

                const errorHtml = `
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bx bx-error-circle me-2" style="font-size: 20px;"></i>
                        <div>
                            <strong>Gagal memuat preview dokumen.</strong><br>
                            <small>Silakan download file untuk melihat dokumen.</small>
                        </div>
                    </div>
                `;

                container.html(errorHtml);
            };
        });
       
    </script>
    <script src="<?php echo e(asset('js/kuesioner/index.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/recognition/index.blade.php ENDPATH**/ ?>