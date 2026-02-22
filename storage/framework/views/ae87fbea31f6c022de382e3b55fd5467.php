    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo e(@$title ?? 'MyPartnership UMS'); ?></title>
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        
        <meta name="description" content="<?php echo e($meta_description ?? 'Sistem MyPartnership Universitas Muhammadiyah Surakarta untuk pengelolaan kerja sama, rekognisi dosen, mitra potensial, dan hibah kerja sama.'); ?>">
        <meta name="keywords" content="MyPartnership, UMS, Kerja Sama, Rekognisi Dosen, Hibah, Mitra Potensial, Universitas Muhammadiyah Surakarta">
        <meta name="author" content="Universitas Muhammadiyah Surakarta">

        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />

        <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('images/favicon.png')); ?>" />

        <meta property="og:title" content="<?php echo e(@$title ?? 'MyPartnership UMS'); ?>">
        <meta property="og:description" content="<?php echo e($meta_description ?? 'Portal kerja sama dan rekognisi UMS.'); ?>">
        <meta property="og:image" content="<?php echo e(asset('images/favicon.png')); ?>">
        <meta property="og:url" content="<?php echo e(url()->current()); ?>">
        <meta property="og:type" content="website">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?php echo e(@$title ?? 'MyPartnership UMS'); ?>">
        <meta name="twitter:description" content="<?php echo e($meta_description ?? 'Portal kerja sama dan rekognisi UMS.'); ?>">
        <meta name="twitter:image" content="<?php echo e(asset('images/favicon.png')); ?>">

        <meta name="theme-color" content="#0d47a1">

        <link rel="canonical" href="<?php echo e(url()->current()); ?>">

        

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11/font/bootstrap-icons.css" rel="stylesheet">

        <link rel="stylesheet" href="<?php echo e(asset('assets/css/transition.css')); ?>">
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://code.highcharts.com/maps/highmaps.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <script src="https://code.highcharts.com/themes/adaptive.js"></script>

        <link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">
        <script>
            let csrftoken = '<?php echo e(csrf_token()); ?>';
        </script>

    </head>

    <body>

        <section class="hero-login">

            <div class="container-fluid h-100 position-relative">
                <div class="row h-100">
                    <!-- Left Section -->
                    <div class="col-md-6 left-section d-flex flex-column align-items-center position-relative curva-bg"
                        style="padding-top:6rem;">
                        <!-- Logo & Text -->
                        <div class="d-flex align-items-center w-100 px-4 scroll-animate">
                            <div class="d-flex align-items-center justify-content-center" style="height: 50px;">
                                <img src="<?php echo e(asset('images/ums_white.png')); ?>" alt="Logo UMS" style="height: 100%;">
                                
                            </div>

                            <!-- Divider -->
                            <div class="vertical-divider mx-3"></div>

                            <!-- Text -->
                            <div>
                                <h6 class="mb-0" style="font-size: 12px;">Office of Collaboration <br> and
                                    International
                                    Affairs</h6>
                                <div class="horizontal-divider"></div>
                                <span style="font-size: 10px;">Universitas Muhammadiyah Surakarta</span>
                            </div>
                        </div>

                        <!-- Image & Padding -->
                        <div class="w-75 scroll-animate">
                            
                            <img src="<?php echo e(asset('images/Edutorium-UMS-2-1.jpg')); ?>"
                                class="img-fluid mt-4 shadow-lg rounded mahasiswa-ums" alt="Mahasiswa UMS">
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="col-md-6 right-section d-flex flex-column" style="padding-top:6rem;">
                        <div class="title-bar"></div>
                        <h1 class="typing duration-200 scroll-animate">MyPartnership</h1>
                        <p class="scroll-animate duration-200" style="color: #2d2f92;">
                            Temukan informasi terbaru mengenai berbagai program kemitraan, kolaborasi,
                            dan peluang untuk berkembang bersama Universitas Muhammadiyah Surakarta
                        </p>

                        <form class="mt-4 scroll-animate" action="<?php echo e(route('login')); ?>" method="post" id="formLogin">
                            <?php echo csrf_field(); ?>
                            <!-- Username Input with Icon -->
                            <div class="input-group mb-3">

                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-person-fill"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="exampleInputUsername"
                                    placeholder="Username" name="username" required>
                            </div>

                            <!-- Password Input with Icon + Show/Hide -->
                            <div class="input-group mb-3 position-relative">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" class="form-control border-start-0 pe-5" name="password"
                                    placeholder="Password" id="passwordInput">
                                <span class="toggle-password position-absolute end-0 top-50 translate-middle-y me-3"
                                    style="cursor:pointer; z-index:5;">
                                    <i class="bi bi-eye-slash-fill"></i>
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-login">Login</button>
                                <span>or</span>
                                <a href="<?php echo e(route('redirectToGoogle')); ?>" class="btn btn-google">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 48 48" fill="white">
                                        <path
                                            d="M44.5 20H24v8.5h11.8c-1.7 5-6.2 8.5-11.8 8.5-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l6-6C33.9 6.2 29.2 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.4-.1-2.7-.5-4z" />
                                    </svg>
                                    Login with Google
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6 right-section d-flex flex-column scroll-animate">
                        <div class="mb-4 ms-4 ms-lg-0 d-flex justify-content-center justify-content-lg-start">

                            <a href="#" class="btn btn-download px-4 py-2" onclick="dokumenPendukungModal()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                    <path
                                        d="M.5 9.9a.5.5 0 0 1 .5.5v2.6A1 1 0 0 0 2.5 14h11a1 1 0 0 0 1-1v-2.6a.5.5 0 0 1 1 0v2.6a2 2 0 0 1-2 2h-11a2 2 0 0 1-2-2v-2.6a.5.5 0 0 1 .5-.5z" />
                                    <path
                                        d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                                </svg> Dokumen Pendukung
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <!-- Section: Sebaran Mitra -->
        <!-- 3 Statistic Cards -->

        <section class="summary-data py-5">
            <div class="container-fluid">
                <div class="row justify-content-center text-center g-4">
                    <!-- Card MoU -->
                    <div class="col-md-3 col-sm-6 scroll-animate duration-300" style="cursor: pointer">
                        <div class="card shadow-sm p-4 h-100 summary-card"
                            data-data-title-tooltip="Memorandum of Understanding" data-type="mou"
                            data-value="<?php echo e($dataMoU); ?>">
                            <div class="text-warning mb-2">
                                <i class="bi bi-file-earmark-text-fill" style="font-size: 2.5rem;"></i>
                            </div>
                            <h6 class="fw-bold mb-0 summary-title">Memorandum of Understanding</h6>
                            <h1 class="fw-bold mt-2 display-number"><?php echo e($dataMoU); ?></h1>
                        </div>
                    </div>

                    <!-- Card MoA -->
                    <div class="col-md-3 col-sm-6 scroll-animate duration-200" style="cursor: pointer">
                        <div class="card shadow-sm p-4 h-100 summary-card"
                            data-data-title-tooltip="Memorandum of Agreement" data-type="moa"
                            data-value="<?php echo e($dataMoA); ?>">
                            <div class="text-warning mb-2">
                                <i class="bi bi-shield-check" style="font-size: 2.5rem;"></i>
                            </div>
                            <h6 class="fw-bold mb-0 summary-title">Memorandum of Agreement</h6>
                            <h1 class="fw-bold mt-2 display-number"><?php echo e($dataMoA); ?></h1>
                        </div>
                    </div>

                    <!-- Card IA -->
                    <div class="col-md-3 col-sm-6 scroll-animate duration-300" style="cursor: pointer">
                        <div class="card shadow-sm p-4 h-100 summary-card"
                            data-data-title-tooltip="Implementation Arrangement" data-type="ia"
                            data-value="<?php echo e($dataIA); ?>">
                            <div class="text-warning mb-2">
                                <i class="bi bi-gear-fill" style="font-size: 2.5rem;"></i>
                            </div>
                            <h6 class="fw-bold mb-0 summary-title">Implementation Arrangement</h6>
                            <h1 class="fw-bold mt-2 display-number"><?php echo e($dataIA); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal -->
        <div class="modal fade" id="dokumenModal" tabindex="-1" aria-labelledby="dokumenModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dokumenModalLabel">Dokumen Pendukung</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="dokumenContent">

                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="chartModal" tabindex="-1" aria-labelledby="chartModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-4 rounded-4" style="border: 2px solid #ccc;">
                    <div class="modal-header">
                        <h3 class="fw-bold mb-0">Detail Grafik</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Header -->
                        <!-- Modal Header -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-2">
                                <i id="modalIcon" class="bi bi-file-earmark-text-fill text-warning"
                                    style="font-size: 2rem;"></i>
                            </div>
                            <h4 class="fw-bold mb-0" id="modalTitle">Jumlah MoU</h4>
                        </div>

                        <hr style="border: 2px solid #291F71; margin-top: 0;">
                        <style>
                            .highcharts-figure,
                            .highcharts-data-table table {
                                min-width: 310px;
                                max-width: 800px;
                                margin: 1em auto;
                            }


                            .highcharts-data-table table {
                                font-family: Verdana, sans-serif;
                                border-collapse: collapse;
                                border: 1px solid var(--highcharts-neutral-color-10, #e6e6e6);
                                margin: 10px auto;
                                text-align: center;
                                width: 100%;
                                max-width: 500px;
                            }

                            .highcharts-data-table caption {
                                padding: 1em 0;
                                font-size: 1.2em;
                                color: var(--highcharts-neutral-color-60, #666);
                            }

                            .highcharts-data-table th {
                                font-weight: 600;
                                padding: 0.5em;
                            }

                            .highcharts-data-table td,
                            .highcharts-data-table th,
                            .highcharts-data-table caption {
                                padding: 0.5em;
                            }

                            .highcharts-data-table thead tr,
                            .highcharts-data-table tbody tr:nth-child(even) {
                                background: var(--highcharts-neutral-color-3, #f7f7f7);
                            }

                            .highcharts-description {
                                margin: 0.3rem 10px;
                            }
                        </style>
                        <!-- Chart section -->
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <div id="dalamNegeriChart" style="height: 300px;"></div>
                                <strong>Dalam Negeri</strong>
                            </div>
                            <div class="col-md-6 text-center">
                                <div id="luarNegeriChart" style="height: 300px;"></div>
                                <strong>Luar Negeri</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <?php echo $__env->make('sebaran_mitra', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php
            use App\Models\SettingBobot;
            $dataSetting = SettingBobot::first();
        ?>
        <footer class="footer-section bg-light py-2" style="border-top: none;">
            <!-- Di bawah peta -->
            <div class="map-decorator">
                <div class="purple-bg"></div>
                <div class="yellow-bg"></div>
                <div class="white-bg"></div>
            </div>
            <div class="container d-flex flex-wrap justify-content-between">
                <!-- Logo & Copyright -->
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Logo_resmi_UMS.svg" alt="UMS Logo"
                        style="height: 50px; margin-right: 15px;">
                    <span class="fw-semibold border-start ps-3" style="color: #291F71">Copyright 2025. Biro
                        Kerjasama dan Urusan
                        Internasional</span>
                </div>

                <!-- Tautan Penting -->
                <div class="mt-3">
                    <h6 class="fw-bold" style="color: #291F71">Tautan Penting</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo e(@$dataSetting->website_ums); ?>" target="_blank" class="text-decoration-none"
                                style="color: #291F71">&rsaquo;
                                UMS</a></li>
                        <li><a href="<?php echo e(@$dataSetting->website_bkui); ?>" target="_blank" class="text-decoration-none"
                                style="color: #291F71">&rsaquo;
                                BKUI UMS</a></li>
                    </ul>
                </div>

                <!-- Kontak Kami -->
                <div class="mt-3">
                    <h6 class="fw-bold" style="color: #291F71">Kontak Kami</h6>
                    <div class="d-flex gap-3">
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo e(@$dataSetting->email); ?>"
                            target="_blank" style="color: #291F71">
                                <i class="bi bi-envelope-fill fs-5"></i>
                        </a>
                        <a href="<?php echo e(@$dataSetting->instagram); ?>" target="_blank" style="color: #291F71"><i
                                class="bi bi-instagram fs-5"></i></a>
                        <a href="<?php echo e(@$dataSetting->facebook); ?>" target="_blank" style="color: #291F71"><i
                                class="bi bi-facebook fs-5"></i></a>
                        <a href="<?php echo e(@$dataSetting->twitter); ?>" target="_blank" style="color: #291F71"><i
                                class="bi bi-twitter-x fs-5"></i></a>
                        <a href="<?php echo e(@$dataSetting->tiktok); ?>" target="_blank" style="color: #291F71"><i
                                class="bi bi-tiktok fs-5"></i></a>
                    </div>
                </div>
            </div>
        </footer>
        <?php echo $__env->make('layouts.components.button-whatsapp', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            window.sessionError = <?php echo json_encode(session('error'), 15, 512) ?>;
        </script>

        <!-- Bootstrap Bundle (termasuk Modal JS) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo e(asset('assets/js/transition.js')); ?>"></script>

        <script>
            if (window.sessionError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: window.sessionError
                });
            }

            function dokumenPendukungModal() {
                $('#dokumenContent').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
                $('#dokumenModal').modal('show');

                $.ajax({
                    url: "<?php echo e(route('login.dokumenPendukung')); ?>",
                    method: "GET",
                    success: function(response) {
                        $('#dokumenContent').html(response);
                    },
                    error: function(xhr) {
                        $('#dokumenContent').html('<p class="text-danger">Gagal memuat dokumen pendukung.</p>');
                    }
                });
            }

            $(document).ready(function() {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                $('#formLogin').on('submit', function(e) {
                    e.preventDefault();

                    const form = this;
                    const actionUrl = $(form).attr('action');
                    const formData = new FormData(form);

                    formData.append('_token', csrfToken);

                    Swal.fire({
                        title: 'Proses...',
                        text: 'Silahkan menunggu proses login.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: actionUrl,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Login',
                                    text: 'Kamu akan diarahkan ke dashboard.',
                                    timer: 1000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = response.redirect_url || '/';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Login Gagal',
                                    text: response.message || 'Terjadi kesalahan saat login.'
                                });
                            }
                        },
                        error(xhr) {
                            let message = 'Terjadi kesalahan. Silakan coba lagi.';
                            if (xhr.responseJSON?.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.status === 422) {
                                const errors = xhr.responseJSON?.errors;
                                if (errors) {
                                    message = Object.values(errors).flat().join('\n');
                                }
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Gagal',
                                text: message
                            });
                        }
                    });
                });
            });
        </script>
        <script>
            // Toggle Show/Hide Password
            document.querySelector('.toggle-password').addEventListener('click', function() {
                const passwordInput = document.getElementById('passwordInput');
                const icon = this.querySelector('i');
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    icon.classList.remove("bi-eye-slash-fill");
                    icon.classList.add("bi-eye-fill");
                } else {
                    passwordInput.type = "password";
                    icon.classList.remove("bi-eye-fill");
                    icon.classList.add("bi-eye-slash-fill");
                }
            });
        </script>
        <script type="text/javascript">
            let chartLoaded = false;

            // Ambil data dari Laravel
            const dataNegara = <?php echo json_encode($dataNegara, 15, 512) ?>;

            function drawRegionsMap() {
                const chartData = [
                    ['Country', 'Jumlah']
                ];

                // Masukkan data negara ke array chartData
                dataNegara.forEach(item => {
                    chartData.push([item.nama_negara, item.jumlah]);
                });

                console.log(chartData);

                const data = google.visualization.arrayToDataTable(chartData);

                const options = {
                    backgroundColor: {
                        // fill: '#291F71' // uncomment jika ingin background
                    },
                    colorAxis: {
                        colors: ['#291F71', '#291F71']
                    },
                    datalessRegionColor: '#ccc'
                };

                const chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
                chart.draw(data, options);
            }

            google.charts.load('current', {
                packages: ['geochart']
            });

            // Observer agar chart hanya digambar saat terlihat
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !chartLoaded) {
                        chartLoaded = true;
                        google.charts.setOnLoadCallback(drawRegionsMap);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            document.addEventListener("DOMContentLoaded", () => {
                const mitraSection = document.getElementById('mitra');
                if (mitraSection) {
                    observer.observe(mitraSection);
                }
            });
        </script>
        <script>
            $(document).ready(function() {
                $('.summary-card').on('click', function() {
                    var title = $(this).data('title');
                    var value = $(this).data('value');

                    $('#modalTitle').text(title);
                    $('#modalValue').text(value);
                    $('#summaryModal').modal('show');
                });
            });
        </script>

        <script>
            google.charts.load('current', {
                packages: ['corechart', 'bar']
            });

            const chartData = <?php echo json_encode($chartData, 15, 512) ?>

            // Saat kartu diklik
            document.querySelectorAll('.summary-card').forEach(card => {
                card.addEventListener('click', function() {
                    const type = this.dataset.type;
                    const data = chartData[type];

                    // Update Judul dan Icon
                    document.getElementById('modalTitle').textContent = data.title;
                    document.getElementById('modalIcon').className = 'bi text-warning me-2 ' + data.icon;

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('chartModal'));
                    modal.show();

                    // Tunggu modal muncul sebelum menggambar chart
                    setTimeout(() => {
                        drawChart('dalamNegeriChart', data.dalam);
                        drawChart('luarNegeriChart', data.luar);
                    }, 200); // Delay kecil untuk pastikan modal terbuka
                });
            });

            function drawChart(elementId, dataArray) {
                // Ambil data
                const categories = dataArray.slice(1).map(row => row[0]); // ['Aktif', 'Produktif']
                const values = dataArray.slice(1).map(row => row[1]); // [7, 11]

                Highcharts.chart(elementId, {
                    chart: {
                        type: 'bar',
                        backgroundColor: 'transparent',
                        borderWidth: 0, // Hilangkan border luar
                        plotBorderWidth: 0, // Hilangkan border area plot
                        style: {
                            fontFamily: 'Poppins, sans-serif'
                        }
                    },
                    title: {
                        text: null
                    },

                    xAxis: {
                        categories: categories,
                        title: null,
                        labels: {
                            style: {
                                color: '#333',
                                fontSize: '12px'
                            }
                        },
                        lineWidth: 0,
                        gridLineWidth: 0
                    },

                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Jumlah'
                        },
                        labels: {
                            style: {
                                color: '#555',
                                fontSize: '12px'
                            }
                        },
                        gridLineWidth: 0
                    },

                    tooltip: {
                        shared: true,
                        pointFormat: '<b>{point.y}</b>'
                    },

                    plotOptions: {
                        bar: {
                            borderRadius: 5,
                            colorByPoint: true, // Warna per bar
                            colors: ['#2d2f92', '#f39c12'], // Aktif, Produktif
                            dataLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '11px',
                                    color: '#333'
                                }
                            },
                            groupPadding: 0.1
                        }
                    },

                    legend: {
                        enabled: false
                    }, // Legend tidak dibutuhkan karena kategori sudah jelas
                    credits: {
                        enabled: false
                    },

                    series: [{
                        name: 'Jumlah',
                        data: values
                    }]
                });
            }
        </script>

    </body>

    </html>
<?php /**PATH /var/www/resources/views/login.blade.php ENDPATH**/ ?>