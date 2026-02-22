<header class="app-header">
    <nav class="navbar navbar-expand-lg bg-primary navbar-light shadow-sm"
        style="max-height: 60px!important; min-height: 0px!important;" id="NavWrap">
        <div class="container-fluid" style="padding: 0 0;">
            <!-- Sidebar Toggle & Logo -->
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <div class="brand-logo d-flex align-items-center px-3">
                        <img src="<?php echo e(asset('images/favicon.png')); ?>" alt="Logo" style="width: 35px; height: auto;">
                        <span class="title-sidebar ms-2 fw-bold" style="font-size: 14px;">
                            <?php if(session('menu') == 'mypartnership'): ?>
                                Kerja Sama
                            <?php elseif(session('menu') == 'recognition'): ?>
                                Rekognisi Dosen
                            <?php elseif(session('menu') == 'partner'): ?>
                                Mitra Potensial
                            <?php elseif(session('menu') == 'hibah'): ?>
                                Hibah Kerja Sama
                            <?php endif; ?>
                        </span>
                    </div>
                </li>
            </ul>

            <!-- Mobile Sidebar Toggle -->
            <li class="nav-item d-block d-lg-none px-4">
                <a class="nav-link nav-icon-hover" id="toggle-mobile" href="javascript:void(0)">
                    <i class="ti ti-menu-2 text-white"></i>
                </a>
            </li>

            <div class="px-0 collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Role Selector -->
                    <?php
                        $roles = Auth::user()->roles->pluck('name')->toArray();
                        $currentRole = session('current_role');
                        $availableRoles = array_diff($roles, [$currentRole]); // Hilangkan role yang sedang aktif
                    ?>

                    <li class="nav-item dropdown pb-0">
                        <button type="button" class="btn btn-sm gradient-green me-2" style="font-size:12px;">
                            <span class="text-light d-none d-md-inline">Lembaga:
                                <?php echo e(auth()->user()->status_tempat ?? 'Belum ada lembaga'); ?></span>
                        </button>
                    </li>

                    <?php if(count($roles) > 1): ?>
                        <li class="nav-item dropdown pb-0">
                            <button type="button" class="btn btn-sm gradient-warning me-2 dropdown-toggle"
                                style="padding: 6px 10px;font-size:12px;" id="roleButton" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Role <?php echo e(ucwords($currentRole)); ?> : Ganti Akses
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end keep-inside-screen" aria-labelledby="roleButton">
                                <?php $__currentLoopData = $availableRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a class="dropdown-item role-switch" href="#"
                                            data-role="<?php echo e($role); ?>">
                                            <?php echo e(ucwords($role)); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown pb-0">
                        <button type="button" class="btn btn-sm gradient-green me-2 dropdown-toggle"
                            style="font-size:12px;" id="roleButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo e(Auth::user()->avatar_google ?: asset('/assets/images/profile/user-1.jpg')); ?>"
                                onerror="this.onerror=null;this.src='<?php echo e(asset('/assets/images/profile/user-1.jpg')); ?>';"
                                alt="avatar" width="32" height="32" class="rounded-circle">

                            <span class="text-light ms-2 d-none d-md-inline">Hai,
                                <?php echo e(ucwords(Auth::user()->name)); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end keep-inside-screen" aria-labelledby="roleButton">
                            <li class="text-center">
                                <span class="dropdown-item">Hai, <?php echo e(ucwords(Auth::user()->name)); ?>!</span>
                            </li>
                            <?php if(session('menu') == 'mypartnership' && session('current_role') != 'superadmin'): ?>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="recognition">
                                        <i class="bx bx-trophy me-2"></i> Rekognisi Dosen
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="partner">
                                        <i class="bx bx-network-chart me-2"></i> Mitra Potensial
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="hibah">
                                        <i class="bx bx-gift me-2"></i> Hibah Kerja Sama
                                    </a>
                                </li>
                            <?php elseif(session('menu') == 'recognition' && session('current_role') != 'superadmin'): ?>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="mypartnership">
                                        <i class="bx bx-link-alt me-2"></i> Kerja Sama
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="partner">
                                        <i class="bx bx-network-chart me-2"></i> Mitra Potensial
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="hibah">
                                        <i class="bx bx-gift me-2"></i> Hibah Kerja Sama
                                    </a>
                                </li>
                            <?php elseif(session('menu') == 'partner' && session('current_role') != 'superadmin'): ?>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="mypartnership">
                                        <i class="bx bx-link-alt me-2"></i> Kerja Sama
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="recognition">
                                        <i class="bx bx-trophy me-2"></i> Rekognisi Dosen
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="hibah">
                                        <i class="bx bx-gift me-2"></i> Hibah Kerja Sama
                                    </a>
                                </li>
                            <?php elseif(session('menu') == 'hibah' && session('current_role') != 'superadmin'): ?>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="mypartnership">
                                        <i class="bx bx-link-alt me-2"></i> Kerja Sama
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="recognition">
                                        <i class="bx bx-trophy me-2"></i> Rekognisi Dosen
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item menu-switch" href="#" data-menu="partner">
                                        <i class="bx bx-network-chart me-2"></i> Mitra Potensial
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <form action="<?php echo e(route('logout')); ?>" method="get">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                        class="btn btn-outline-primary d-block w-100 mt-2">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <nav class="navbar navbar-expand-lg navbar-light shadow-sm d-none d-lg-block" id="menuNavWrap">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="menuNav">
                <ul class="navbar-nav align-items-center" style="flex-wrap: wrap;">
                    <?php if(session('menu') == 'mypartnership'): ?>
                        <li class="nav-item">
                            <a class="nav-link small-text <?php echo e(@$li_active == 'dashboard' ? 'active' : ''); ?>"
                                href="<?php echo e(route('home.dashboard')); ?>"> <i class="fa fa-chart-line me-2"></i>
                                Dashboard</a>
                        </li>
                        <?php if(session('current_role') == 'superadmin'): ?>
                            <?php echo $__env->make('layouts.components.menu_header.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>
                        <?php if(session('current_role') == 'admin'): ?>
                            <?php echo $__env->make('layouts.components.menu_header.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>
                        <?php if(session('current_role') == 'verifikator'): ?>
                            <?php echo $__env->make('layouts.components.menu_header.verifikator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>
                        <?php if(session('current_role') == 'user'): ?>
                            <?php echo $__env->make('layouts.components.menu_header.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>
                        <?php if(session('current_role') == 'eksekutif'): ?>
                            <?php echo $__env->make('layouts.components.menu_header.eksekutif', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?>
                    <?php elseif(session('menu') == 'recognition'): ?>
                        <li class="nav-item">
                            <a class="nav-link small-text <?php echo e(@$li_active == 'dashboard' ? 'active' : ''); ?>"
                                href="<?php echo e(route('recognition.home')); ?>"> <i class="fa fa-chart-line me-2"></i>
                                Dashboard</a>
                        </li>
                        <?php echo $__env->make('layouts.components.menu_header.recognition', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php elseif(session('menu') == 'partner'): ?>
                        <li class="nav-item">
                            <a class="nav-link small-text <?php echo e(@$li_active == 'dashboard' ? 'active' : ''); ?>"
                                href="<?php echo e(route('potential_partner.home')); ?>"> <i class="fa fa-chart-line me-2"></i>
                                Dashboard</a>
                        </li>
                        <?php echo $__env->make('layouts.components.menu_header.partner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php elseif(session('menu') == 'hibah'): ?>
                        <li class="nav-item">
                            <a class="nav-link small-text <?php echo e(@$li_active == 'dashboard' ? 'active' : ''); ?>"
                                href="<?php echo e(route('hibah.home')); ?>"> <i class="fa fa-chart-line me-2"></i>
                                Dashboard</a>
                        </li>
                        <?php echo $__env->make('layouts.components.menu_header.hibah', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Mobile Menu -->
    <div id="mobileSidebar" class="mobile-sidebar">
        <ul class="list-unstyled">
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                <span class="sidebar-icon">Akun</span>
            </li>
            <?php if(count($roles) > 1): ?>
                <li class="sidebar-item pb-0">
                    <a class="sidebar-link has-arrow btn btn-sm bg-primary text-light dropdown-toggle" href="#"
                        aria-expanded="false" style="padding: 6px 10px;font-size:12px;">
                        Role <?php echo e(ucwords($currentRole)); ?> : Ganti Akses
                    </a>
                    <ul class="collapse first-level bg-primary rounded-bottom">
                        <?php $__currentLoopData = $availableRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="sidebar-item" style="padding: 5px 20px">
                                <a class="sidebar-link role-switch" href="#" data-role="<?php echo e($role); ?>"
                                    style="font-size: 0.85rem; padding: 5px 10px;color: #fff;">
                                    <?php echo e(ucwords($role)); ?>

                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>
            <?php endif; ?>
            <li class="sidebar-item pb-0">
                <button type="button" class="btn btn-sm gradient-green me-2" style="font-size:12px;">
                    <span class="text-light">Lembaga:
                        <?php echo e(auth()->user()->status_tempat ?? 'Belum ada lembaga'); ?></span>
                </button>
            </li>
            <li class="sidebar-item pb-0">
                <a class="sidebar-link has-arrow btn btn-sm btn-primary dropdown-toggle" style="gap: 0px;"
                    href="#" aria-expanded="false">
                    <img src="<?php echo e(Auth::user()->avatar_google ? Auth::user()->avatar_google : asset('/assets/images/profile/user-1.jpg')); ?>"
                        alt="" width="32" height="32" class="rounded-circle">
                    <span class="text-light ms-2 username-truncate">Hai,
                        <?php echo e(ucwords(Auth::user()->name)); ?></span>
                </a>
                <ul class="collapse first-level rounded-bottom">
                    <?php if(session('menu') == 'mypartnership' && session('current_role') != 'superadmin'): ?>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="recognition">
                                <i class="bx bx-trophy me-2"></i> Rekognisi Dosen
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="partner">
                                <i class="bx bx-network-chart me-2"></i> Mitra Potensial
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="hibah">
                                <i class="bx bx-gift me-2"></i> Hibah Kerja Sama
                            </a>
                        </li>
                    <?php elseif(session('menu') == 'recognition' && session('current_role') != 'superadmin'): ?>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="mypartnership">
                                <i class="bx bx-link-alt me-2"></i> Kerja Sama
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="partner">
                                <i class="bx bx-network-chart me-2"></i> Mitra Potensial
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="hibah">
                                <i class="bx bx-gift me-2"></i> Hibah Kerja Sama
                            </a>
                        </li>
                    <?php elseif(session('menu') == 'partner' && session('current_role') != 'superadmin'): ?>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="mypartnership">
                                <i class="bx bx-link-alt me-2"></i> Kerja Sama
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="recognition">
                                <i class="bx bx-trophy me-2"></i> Rekognisi Dosen
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="hibah">
                                <i class="bx bx-gift me-2"></i> Hibah Kerja Sama
                            </a>
                        </li>
                    <?php elseif(session('menu') == 'hibah' && session('current_role') != 'superadmin'): ?>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="mypartnership">
                                <i class="bx bx-link-alt me-2"></i> Kerja Sama
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="recognition">
                                <i class="bx bx-trophy me-2"></i> Rekognisi Dosen
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="dropdown-item menu-switch" style="padding: 0px;" href="#"
                                data-menu="partner">
                                <i class="bx bx-network-chart me-2"></i> Mitra Potensial
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="sidebar-item">
                        <form action="<?php echo e(route('logout')); ?>" method="get">
                            <button type="submit" class="btn btn-outline-primary d-block w-100 mt-2">Logout</button>
                        </form>
                    </li>
                </ul>
            </li>
            <style>
                #search-template .input {
                    width: 100px;
                }

                #search-template .input:focus {
                    width: 130px;
                }
            </style>
            <li class="sidebar-item">
                <!-- Search Bar -->
                <div id="search-template">
                    <div class="InputContainer">
                        <input type="text" name="text" class="input" id="sidebarSearch"
                            placeholder="Cari Menu...">
                        <label for="input" class="labelforsearch">
                            <svg viewBox="0 0 512 512" class="searchIcon">
                                <path
                                    d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z">
                                </path>
                            </svg>
                        </label>
                    </div>
                </div>
            </li>
            <div class="scroll-bar-custom mt-2 mb-3" id="MenuNavMobile">
                <?php if(session('menu') == 'mypartnership'): ?>
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="sidebar-icon">Home</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link text-dark" href="<?php echo e(route('home.dashboard')); ?>"
                            aria-expanded="false">
                            <span class="sidebar-icon">
                                <i class="fa-solid fa-house me-2"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    <?php if(session('current_role') == 'superadmin'): ?>
                        <?php echo $__env->make('layouts.components.mobile.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>
                    <?php if(session('current_role') == 'admin'): ?>
                        <?php echo $__env->make('layouts.components.mobile.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>
                    <?php if(session('current_role') == 'verifikator'): ?>
                        <?php echo $__env->make('layouts.components.mobile.verifikator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>
                    <?php if(session('current_role') == 'user'): ?>
                        <?php echo $__env->make('layouts.components.mobile.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endif; ?>
                <?php elseif(session('menu') == 'recognition'): ?>
                    <?php echo $__env->make('layouts.components.mobile.recognition', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php elseif(session('menu') == 'partner'): ?>
                    <?php echo $__env->make('layouts.components.mobile.partner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php elseif(session('menu') == 'hibah'): ?>
                    <?php echo $__env->make('layouts.components.mobile.hibah', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            </div>
        </ul>
    </div>
</header>
<style>
    div#mobileSidebar ul .sidebar-item {
        padding: 10px 20px;
    }

    div#mobileSidebar ul .sidebar-item a {
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById("toggle-mobile");
        const sidebar = document.getElementById("mobileSidebar");

        toggleBtn.addEventListener("click", function() {
            sidebar.classList.toggle("show");
        });

        document.addEventListener("click", function(event) {
            if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                sidebar.classList.remove("show");
            }
        });
    });
</script>
<?php /**PATH /var/www/resources/views/layouts/components/header.blade.php ENDPATH**/ ?>