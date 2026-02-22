<link rel="stylesheet" href="{{ asset('css/footer.css') }}">
@php
    use App\Models\SettingBobot;
    $dataSetting = SettingBobot::first();
@endphp
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
                <li><a href="{{ @$dataSetting->website_ums }}" target="_blank" class="text-decoration-none"
                        style="color: #291F71">&rsaquo;
                        UMS</a></li>
                <li><a href="{{ @$dataSetting->website_bkui }}" target="_blank" class="text-decoration-none"
                        style="color: #291F71">&rsaquo;
                        BKUI UMS</a></li>
            </ul>
        </div>

        <!-- Kontak Kami -->
        <div class="mt-3">
            <h6 class="fw-bold" style="color: #291F71">Kontak Kami</h6>
            <div class="d-flex gap-3">
                <a href="{{ @$dataSetting->email }}" target="_blank" style="color: #291F71"><i
                        class="bi bi-envelope-fill fs-5"></i></a>
                <a href="{{ @$dataSetting->instagram }}" target="_blank" style="color: #291F71"><i
                        class="bi bi-instagram fs-5"></i></a>
                <a href="{{ @$dataSetting->facebook }}" target="_blank" style="color: #291F71"><i
                        class="bi bi-facebook fs-5"></i></a>
                <a href="{{ @$dataSetting->twitter }}" target="_blank" style="color: #291F71"><i
                        class="bi bi-twitter-x fs-5"></i></a>
                <a href="{{ @$dataSetting->tiktok }}" target="_blank" style="color: #291F71"><i
                        class="bi bi-tiktok fs-5"></i></a>
            </div>
        </div>
    </div>
</footer>

{{-- <div class="px-6 py-6 text-center">
    <p class="mb-0 fs-4">
    <div class="mb-2 mb-md-0">
        ©
        <script>
            document.write(new Date().getFullYear());
        </script>
        <span>Copyright &copy; MyPartnership</span>
    </div>
    </p>
</div> --}}
