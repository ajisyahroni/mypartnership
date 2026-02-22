<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ @$title ?? 'Content Management System' }}</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets') }}/css/styles.min.css" />
    <link rel="stylesheet" href="{{ asset('assets_admin') }}/css/custom.css" />
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <style>
        body {
            background: linear-gradient(135deg, #f3f4f6, #dfe6ed);
            position: relative;
            overflow: hidden;
        }

        .card-custom {
            background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.9), rgba(173, 216, 230, 0.1));
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .login-card {
            background: linear-gradient(145deg, #ffffff, #eef2f7);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .form-control {
            background-color: white !important;
        }

        .dot {
            position: absolute;
            background-color: rgba(173, 216, 230, 0.4);
            border-radius: 50%;
            opacity: 0.5;
        }

        .dot1 {
            width: 100px;
            height: 100px;
            top: 10%;
            left: 15%;
        }

        .dot2 {
            width: 70px;
            height: 70px;
            bottom: 20%;
            right: 10%;
        }

        .dot3 {
            width: 50px;
            height: 50px;
            top: 50%;
            left: 50%;
        }
    </style>

    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-75 card-custom rounded overflow-hidden shadow py-2 px-2">
            <div class="col-md-6 d-none d-md-block p-0">
                <img src="{{ asset('images/login.jpg') }}" class="w-100 h-100 rounded" style="object-fit: cover;"
                    alt="Login Image">
            </div>
            <div class="col-12 col-md-6 py-0 px-3">
                <div class="row p-3" style="margin-bottom:15%;">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-end">
                        <p class="mb-0">Sudah Punya Akun?</p>
                        <a class="text-primary fw-bold ms-2" href="{{ route('login') }}">Masuk</a>
                    </div>
                    <h3 class="text-center mb-4" style="font-weight: bold;margin-top:15%;">Daftar Akun</h3>
                    <div class="d-flex align-items-center justify-content-center">
                        <p>Content Management System</p>
                    </div>
                    <form action="{{ route('login') }}" method="post" id="formRegister"
                        style="padding-left:10%;padding-right:10%;">
                        @csrf
                        <div class="mb-3">
                            <label for="exampleInputUsername" class="form-label">Username</label>
                            <input type="text" placeholder="Masukkan Username" class="form-control"
                                id="exampleInputUsername" name="username" aria-describedby="usernameHelp">
                        </div>
                        <div class="mb-3 position-relative">
                            <label for="exampleInputPassword1" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" placeholder="Masukkan Password"
                                    class="form-control" id="exampleInputPassword1">
                                <span class="input-group-text bg-white border-0">
                                    <i class="fa fa-eye-slash toggle-password" style="cursor: pointer;"
                                        id="togglePassword"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="exampleInputKonfirmasiPassword1" class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="konfirmasipassword"
                                    placeholder="Masukkan Konfirmasi Password" class="form-control"
                                    id="exampleInputKonfirmasiPassword1">
                                <span class="input-group-text bg-white border-0">
                                    <i class="fa fa-eye-slash toggle-password" style="cursor: pointer;"
                                        id="toggleKonfirmasiPassword"></i>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-biru w-100 text-white shadow-lg">Daftar</button>
                    </form>
                    <hr>
                    <a href="{{ route('redirectToGoogle', ['intent' => 'signup']) }}"
                        class="btn btn-biru w-100 text-white shadow-lg">SignUp
                        With Google</a>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('assets') }}/libs/jquery/dist/jquery.min.js"></script>
    <script src="{{ asset('assets') }}/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets') }}/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets') }}/vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
    {{-- <script src="{{ asset('assets') }}/vendor/daterangepicker/moment.min.js"></script> --}}
    <script src="{{ asset('assets') }}/vendor/daterangepicker/daterangepicker.js"></script>
    <!--===============================================================================================-->
    {{-- <script src="{{ asset('assets') }}/vendor/countdowntime/countdowntime.js"></script> --}}
    <!--===============================================================================================-->
    {{-- <script src="{{ asset('assets') }}/js/main.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll(".toggle-password").forEach(item => {
            item.addEventListener("click", function() {
                let input = this.parentElement.previousElementSibling; // Ambil input sebelum span
                let icon = this;

                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                } else {
                    input.type = "password";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                }
            });
        });
        $(document).ready(function() {
            $("#formRegister").on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Proses...',
                    text: 'Tolong menunggu proses login.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                })

                $.ajax({
                    url: $(this).attr("action"),
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Login',
                            text: 'Kamu akan diarahkan ke dashboard.',
                            timer: 1000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect_url || "/";
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: xhr.responseJSON?.message ||
                                'Email atau Password tidak cocok. coba lagi.'
                        });
                    }
                });
            })
        })
    </script>

</body>

</html>

</html>
