<style>
    .scroll-sidebar {
    max-height: calc(100vh - 150px);
    overflow-y: auto;
}

</style>
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between px-3 pt-2 pb-4">
            <!-- Logo & Judul -->
            <div class="d-flex align-items-center justify-content-center gap-2 flex-grow-1">
                <img src="{{ asset('images/favicon.png') }}" alt="Logo" style="width: 50px; height: auto;">
                <span class="title-sidebar" style="color:black; font-size: 20px; font-weight: bold; white-space: nowrap;">
                    MyPartnership
                </span>
            </div>
            <!-- Tombol Close -->
            <div class="cursor-pointer close-btn d-xl-none d-block sidebartoggler" id="sidebarCollapse">
                <i class="ti ti-x fs-5"></i>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center mb-2">
            <!-- Search Bar -->
            <div id="search-template">
                <div class="InputContainer">
                    <input type="text" name="text" class="input" id="sidebarSearch" placeholder="Cari Menu...">
                    <label for="input" class="labelforsearch">
                        <svg viewBox="0 0 512 512" class="searchIcon">
                            <path
                                d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z">
                            </path>
                        </svg>
                    </label>
                </div>
            </div>
        </div>

        <div class="sidebar-user d-flex align-items-center justify-content-center">
            <div class="card-profile">
                <div class="profile-img">
                    <img src="{{ Auth::user()->avatar_google ? Auth::user()->avatar_google : asset('/assets/images/profile/user-1.jpg') }}"
                        alt="Profile Picture">
                </div>
                <div class="profile-info">
                    <span class="profile-name hide-menu">{{ Auth::user()->name }}</span>
                    <p class="profile-job hide-menu">{{ ucwords(session('current_role')) }}</p>
                </div>
            </div>
        </div>

        <div class="scroll-bar-custom mt-2 mb-3">

            @if (session('current_role') == 'superadmin')
                @include('layouts.components.sidebars.superadmin')
            @endif
            @if (session('current_role') == 'admin')
                @include('layouts.components.sidebars.admin')
            @endif
            @if (session('current_role') == 'verifikator')
                @include('layouts.components.sidebars.verifikator')
            @endif
            @if (session('current_role') == 'user')
                @include('layouts.components.sidebars.user')
            @endif
        </div>
    </div>
    <!-- End Sidebar scroll-->
</aside>
