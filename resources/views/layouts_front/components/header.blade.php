<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

        <a href="" class="logo d-flex align-items-center me-auto">
            <!-- Uncomment the line below if you also wish to use an image logo -->
            <img src="{{ asset('assets_front') }}/img/logo.png" alt="Logo Website">
            <h1 class="sitename">CMS</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="#beranda" class="active">Beranda<br></a></li>
                <li class="dropdown"><a href="#"><span>Artikel</span> <i
                            class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        <li><a href="#">Artikel 1</a></li>
                        <li class="dropdown"><a href="#"><span>Deep Artikel</span> <i
                                    class="bi bi-chevron-down toggle-dropdown"></i></a>
                            <ul>
                                <li><a href="#">Deep Artikel 1</a></li>
                                <li><a href="#">Deep Artikel 2</a></li>
                                <li><a href="#">Deep Artikel 3</a></li>
                                <li><a href="#">Deep Artikel 4</a></li>
                                <li><a href="#">Deep Artikel 5</a></li>
                            </ul>
                        </li>
                        <li><a href="#">Artikel 2</a></li>
                        <li><a href="#">Artikel 3</a></li>
                        <li><a href="#">Artikel 4</a></li>
                    </ul>
                </li>
                <li><a href="#galeri">Galeri</a></li>
                <li><a href="#faq">FAQ</a></li>
                <li><a href="#testimoni">Testimoni</a></li>
                <li><a href="#kontak">Kontak Kami</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="btn-getstarted flex-md-shrink-0" href="{{ route('login') }}">Login</a>

    </div>
</header>
