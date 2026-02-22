<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ @$title ?? 'MyPartnership UMS' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}" />

    <meta name="description" content="{{ $meta_description ?? 'Sistem MyPartnership Universitas Muhammadiyah Surakarta untuk pengelolaan kerja sama, rekognisi dosen, mitra potensial, dan hibah kerja sama.' }}">
    <meta name="keywords" content="MyPartnership, UMS, Kerja Sama, Rekognisi Dosen, Hibah, Mitra Potensial, Universitas Muhammadiyah Surakarta">
    <meta name="author" content="Universitas Muhammadiyah Surakarta">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <meta property="og:title" content="{{ @$title ?? 'MyPartnership UMS' }}">
    <meta property="og:description" content="{{ $meta_description ?? 'Portal kerja sama dan rekognisi UMS.' }}">
    <meta property="og:image" content="{{ asset('images/favicon.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ @$title ?? 'MyPartnership UMS' }}">
    <meta name="twitter:description" content="{{ $meta_description ?? 'Portal kerja sama dan rekognisi UMS.' }}">
    <meta name="twitter:image" content="{{ asset('images/favicon.png') }}">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0d47a1">

    <link rel="canonical" href="{{ url()->current() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/pilihmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pilih_menu/responsive.css') }}">
</head>
<body>
    <!-- Background Shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="bg-shape shape-3"></div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Memuat...</div>
            <div class="loading-subtext" id="loadingSubtext">Mohon tunggu sebentar</div>
        </div>
    </div>

    <!-- User Info & Logout -->
    <div class="top-bar">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span>{{ ucwords(session('current_role')) }}</span>
        </div>
        <form action="{{ route('logout') }}" method="get">
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

    <div class="container-fluid">
        <!-- Header Section -->
        <div class="header-section">
            <div class="logo-container">
                <img src="{{ asset('images/logo_ums_blue.png') }}" alt="Logo UMS">
            </div>
            <h1>MyPartnership</h1>
            <p>Universitas Muhammadiyah Surakarta</p>
        </div>

        <!-- Menu Grid -->
        <div class="menu-grid">
            <!-- Menu 1: Kerja Sama -->
            <a href="#" class="menu-card menu-switch" data-menu="mypartnership" data-title="Kerja Sama">
                <div class="menu-icon-wrapper">
                    <div class="menu-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    @if(($notif_kerjasama ?? 0) > 0)
                        <span class="notification-badge high">{{ $notif_kerjasama }}</span>
                    @endif
                </div>
                <h3>Kerja Sama</h3>
                <p>Kelola dan monitor semua aktivitas kerja sama institusi</p>
            </a>

            <!-- Menu 2: Rekognisi Dosen -->
            <a href="#" class="menu-card menu-switch" data-menu="recognition" data-title="Rekognisi Dosen">
                <div class="menu-icon-wrapper">
                    <div class="menu-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    @if(($notif_rekognisi ?? 0) > 0)
                        <span class="notification-badge high">{{ $notif_rekognisi }}</span>
                    @endif
                </div>
                <h3>Rekognisi Dosen</h3>
                <p>Kelola rekognisi dan pencapaian dosen</p>
            </a>

            <!-- Menu 3: Mitra Potensial -->
            <a href="#" class="menu-card menu-switch" data-menu="partner" data-title="Mitra Potensial">
                <div class="menu-icon-wrapper">
                    <div class="menu-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    @if(($notif_partner ?? 0) > 0)
                        <span class="notification-badge high">{{ $notif_partner }}</span>
                    @endif
                </div>
                <h3>Mitra Potensial</h3>
                <p>Identifikasi dan kelola mitra potensial</p>
            </a>

            <!-- Menu 4: Hibah Kerja Sama -->
            <a href="#" class="menu-card menu-switch" data-menu="hibah" data-title="Hibah Kerja Sama">
                 <div class="menu-icon-wrapper">
                    <div class="menu-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    @if(($notif_hibah ?? 0) > 0)
                        <span class="notification-badge high">{{ $notif_hibah }}</span>
                    @endif
                </div>
                <h3>Hibah Kerja Sama</h3>
                <p>Kelola proposal dan pendanaan hibah</p>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const csrfToken = '{{ csrf_token() }}';
        const loadingOverlay = $('#loadingOverlay');
        const loadingSubtext = $('#loadingSubtext');
        let isLoading = false;
        let loadingTimeout = null;

        window.addEventListener("pageshow", function (event) {
            $('.menu-card').removeClass('selected active');
            forceHideLoading();
            if (event.persisted) {
                window.location.reload();
            }
        });


        function forceHideLoading() {
            loadingOverlay.removeClass('active');
            isLoading = false;
            if (loadingTimeout) {
                clearTimeout(loadingTimeout);
                loadingTimeout = null;
            }
        }

        function showLoading(menuTitle) {
            if (isLoading) {
                console.warn('Loading already active, skipping');
                return false;
            }
            
            isLoading = true;
            loadingSubtext.text(`Membuka ${menuTitle}...`);
            loadingOverlay.addClass('active');
            
            return true;
        }

        $('.menu-switch').on('click', function(e) {
            e.preventDefault();
            
            const $clickedCard = $(this);
            const selectedMenu = $clickedCard.data('menu');
            const menuTitle = $clickedCard.data('title');

            $clickedCard.addClass('selected');
            $('.menu-card').not($clickedCard).addClass('active');

                loadingSubtext.text(`Membuka ${menuTitle}...`);
                loadingOverlay.addClass('active');
            
            $.ajax({
                url: '/set-menu',
                type: 'POST',
                data: { 
                    menu: selectedMenu, 
                    _token: csrfToken 
                },
                success: function(response) {
                    if (response.status === 'success') {
                            window.location.href = response.redirect_url || window.location.href;
                    } else {
                        handleError('Terjadi kesalahan saat memproses permintaan.');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Terdapat kesalahan jaringan.';
                    handleError(errorMsg);
                },
                 complete: function() {
                }
            });
        });

        function handleError(message) {
            loadingOverlay.removeClass('active');
            $('.menu-card').removeClass('selected active');
            
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: message,
                confirmButtonColor: '#1976d2'
            });
        }

        let isProcessing = false;
        $('.menu-switch').on('click', function(e) {
            if (isProcessing) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>