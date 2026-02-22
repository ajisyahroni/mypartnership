<header class="app-header">
    <nav class="navbar navbar-expand-lg bg-primary navbar-light shadow-sm"
        style="max-height: 60px!important; min-height: 0px!important;" id="NavWrap">
        <div class="container-fluid" style="padding: 0 0;">
            <!-- Sidebar Toggle & Logo -->
            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <div class="brand-logo d-flex align-items-center px-3">
                        <img src="{{ asset('images/favicon.png') }}" alt="Logo" style="width: 35px; height: auto;">
                        <span class="title-sidebar ms-2 fw-bold" style="font-size: 14px;">
                            @if (session('menu') == 'mypartnership')
                                Kerja Sama
                            @elseif(session('menu') == 'recognition')
                                Rekognisi Dosen
                            @elseif(session('menu') == 'partner')
                                Mitra Potensial
                            @elseif(session('menu') == 'hibah')
                                Hibah Kerja Sama
                            @endif
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
                    @php
                        $roles = Auth::user()->roles->pluck('name')->toArray();
                        $currentRole = session('current_role');
                        $availableRoles = array_diff($roles, [$currentRole]); // Hilangkan role yang sedang aktif
                    @endphp

                    <li class="nav-item dropdown pb-0">
                        <button type="button" class="btn btn-sm gradient-green me-2" style="font-size:12px;">
                            <span class="text-light d-none d-md-inline">Lembaga:
                                {{ auth()->user()->status_tempat ?? 'Belum ada lembaga' }}</span>
                        </button>
                    </li>

                    @if (count($roles) > 1)
                        <li class="nav-item dropdown pb-0">
                            <button type="button" class="btn btn-sm gradient-warning me-2 dropdown-toggle"
                                style="padding: 6px 10px;font-size:12px;" id="roleButton" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Role {{ ucwords($currentRole) }} : Ganti Akses
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end keep-inside-screen" aria-labelledby="roleButton">
                                @foreach ($availableRoles as $role)
                                    <li>
                                        <a class="dropdown-item role-switch" href="#"
                                            data-role="{{ $role }}">
                                            {{ ucwords($role) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    <li class="nav-item dropdown pb-0">
                        <button type="button" class="btn btn-sm gradient-green me-2 dropdown-toggle"
                            style="font-size:12px;" id="roleButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->avatar_google ?: asset('/assets/images/profile/user-1.jpg') }}"
                                onerror="this.onerror=null;this.src='{{ asset('/assets/images/profile/user-1.jpg') }}';"
                                alt="avatar" width="32" height="32" class="rounded-circle">

                            <span class="text-light ms-2 d-none d-md-inline">Hai,
                                {{ ucwords(Auth::user()->name) }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end keep-inside-screen" aria-labelledby="roleButton">
                            <li class="text-center">
                                <span class="dropdown-item">Hai, {{ ucwords(Auth::user()->name) }}!</span>
                            </li>
                            @if (session('menu') == 'mypartnership' && session('current_role') != 'superadmin')
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
                            @elseif(session('menu') == 'recognition' && session('current_role') != 'superadmin')
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
                            @elseif(session('menu') == 'partner' && session('current_role') != 'superadmin')
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
                            @elseif(session('menu') == 'hibah' && session('current_role') != 'superadmin')
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
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="get">
                                    @csrf
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
                    @if (session('menu') == 'mypartnership')
                        <li class="nav-item">
                            <a class="nav-link small-text {{ @$li_active == 'dashboard' ? 'active' : '' }}"
                                href="{{ route('home.dashboard') }}"> <i class="fa fa-chart-line me-2"></i>
                                Dashboard</a>
                        </li>
                        @if (session('current_role') == 'superadmin')
                            @include('layouts.components.menu_header.superadmin')
                        @endif
                        @if (session('current_role') == 'admin')
                            @include('layouts.components.menu_header.admin')
                        @endif
                        @if (session('current_role') == 'verifikator')
                            @include('layouts.components.menu_header.verifikator')
                        @endif
                        @if (session('current_role') == 'user')
                            @include('layouts.components.menu_header.user')
                        @endif
                        @if (session('current_role') == 'eksekutif')
                            @include('layouts.components.menu_header.eksekutif')
                        @endif
                    @elseif(session('menu') == 'recognition')
                        <li class="nav-item">
                            <a class="nav-link small-text {{ @$li_active == 'dashboard' ? 'active' : '' }}"
                                href="{{ route('recognition.home') }}"> <i class="fa fa-chart-line me-2"></i>
                                Dashboard</a>
                        </li>
                        @include('layouts.components.menu_header.recognition')
                    @elseif(session('menu') == 'partner')
                        <li class="nav-item">
                            <a class="nav-link small-text {{ @$li_active == 'dashboard' ? 'active' : '' }}"
                                href="{{ route('potential_partner.home') }}"> <i class="fa fa-chart-line me-2"></i>
                                Dashboard</a>
                        </li>
                        @include('layouts.components.menu_header.partner')
                    @elseif(session('menu') == 'hibah')
                        <li class="nav-item">
                            <a class="nav-link small-text {{ @$li_active == 'dashboard' ? 'active' : '' }}"
                                href="{{ route('hibah.home') }}"> <i class="fa fa-chart-line me-2"></i>
                                Dashboard</a>
                        </li>
                        @include('layouts.components.menu_header.hibah')
                    @endif
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
            @if (count($roles) > 1)
                <li class="sidebar-item pb-0">
                    <a class="sidebar-link has-arrow btn btn-sm bg-primary text-light dropdown-toggle" href="#"
                        aria-expanded="false" style="padding: 6px 10px;font-size:12px;">
                        Role {{ ucwords($currentRole) }} : Ganti Akses
                    </a>
                    <ul class="collapse first-level bg-primary rounded-bottom">
                        @foreach ($availableRoles as $role)
                            <li class="sidebar-item" style="padding: 5px 20px">
                                <a class="sidebar-link role-switch" href="#" data-role="{{ $role }}"
                                    style="font-size: 0.85rem; padding: 5px 10px;color: #fff;">
                                    {{ ucwords($role) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endif
            <li class="sidebar-item pb-0">
                <button type="button" class="btn btn-sm gradient-green me-2" style="font-size:12px;">
                    <span class="text-light">Lembaga:
                        {{ auth()->user()->status_tempat ?? 'Belum ada lembaga' }}</span>
                </button>
            </li>
            <li class="sidebar-item pb-0">
                <a class="sidebar-link has-arrow btn btn-sm btn-primary dropdown-toggle" style="gap: 0px;"
                    href="#" aria-expanded="false">
                    <img src="{{ Auth::user()->avatar_google ? Auth::user()->avatar_google : asset('/assets/images/profile/user-1.jpg') }}"
                        alt="" width="32" height="32" class="rounded-circle">
                    <span class="text-light ms-2 username-truncate">Hai,
                        {{ ucwords(Auth::user()->name) }}</span>
                </a>
                <ul class="collapse first-level rounded-bottom">
                    @if (session('menu') == 'mypartnership' && session('current_role') != 'superadmin')
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
                    @elseif(session('menu') == 'recognition' && session('current_role') != 'superadmin')
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
                    @elseif(session('menu') == 'partner' && session('current_role') != 'superadmin')
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
                    @elseif(session('menu') == 'hibah' && session('current_role') != 'superadmin')
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
                    @endif
                    <li class="sidebar-item">
                        <form action="{{ route('logout') }}" method="get">
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
                @if (session('menu') == 'mypartnership')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="sidebar-icon">Home</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link text-dark" href="{{ route('home.dashboard') }}"
                            aria-expanded="false">
                            <span class="sidebar-icon">
                                <i class="fa-solid fa-house me-2"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    @if (session('current_role') == 'superadmin')
                        @include('layouts.components.mobile.superadmin')
                    @endif
                    @if (session('current_role') == 'admin')
                        @include('layouts.components.mobile.admin')
                    @endif
                    @if (session('current_role') == 'verifikator')
                        @include('layouts.components.mobile.verifikator')
                    @endif
                    @if (session('current_role') == 'user')
                        @include('layouts.components.mobile.user')
                    @endif
                @elseif(session('menu') == 'recognition')
                    @include('layouts.components.mobile.recognition')
                @elseif(session('menu') == 'partner')
                    @include('layouts.components.mobile.partner')
                @elseif(session('menu') == 'hibah')
                    @include('layouts.components.mobile.hibah')
                @endif
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
