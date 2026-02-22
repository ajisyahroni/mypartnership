<!doctype html>
<html lang="en" dir="ltr">


<head>
    <meta charset="utf-8" />
    <title>Content Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Website Content Management System" />
    <meta name="keywords" content="CMS, Content Management System" />
    <meta name="author" content="Burberian" />

    @include('layouts_front.components.css')
    @stack('styles')


</head>

<body>
    <div class="index-page">
        @include('layouts_front.components.header')
        <!-- Navbar Start -->

        <main class="main">
            @yield('contents')
        </main>

        @include('layouts_front.components.footer')
        <!-- Scroll Top -->
        <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
                class="bi bi-arrow-up-short"></i></a>
    </div>


    @include('layouts_front.components.js')

    @stack('scripts')
</body>

</html>
