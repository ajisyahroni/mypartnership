<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Satisfaction Survey</title>

    <!-- Google Font: Poppins -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: url('{{ asset('images/bg-survei.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            color: #2f3185;
            margin: 0;
            padding: 0;
        }

        .topbar {
            background-color: #2f3185;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
        }

        .main-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header-image img {
            width: 100%;
            height: auto;
        }

        /* @media (min-width: 360px) and (max-width: 767px) {} */

        @media (min-width: 360px) and (max-width: 767px) {
            .content-section {
                padding: 30px 30px;
            }

            .main-wrapper {
                margin: 0px 30px;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .content-section {
                padding: 30px 60px;
            }

            .main-wrapper {
                margin: 0px 30px;
            }
        }

        @media (min-width: 992px) {
            .content-section {
                padding: 30px 90px;
            }
        }

        h4 {
            font-size: 1.1rem;
            margin-top: 20px;
        }

        .required {
            color: red;
        }

        .likert-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
            margin-top: 10px;
        }

        .likert-option input[type="radio"] {
            display: none;
        }

        .likert-option label {
            background-color: #f8f9fa;
            color: #2f3185;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #2f3185;
            min-width: 100px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            font-size: 14px;
        }

        .likert-option input[type="radio"]:checked+label {
            background-color: #2f3185;
            color: #fff;
            border-color: #2f3185;
        }

        .submit-btn {
            background-color: #2f3185;
            color: white;
            border: none;
        }

        .submit-btn:hover {
            background-color: #1f2265;
        }

        @media (max-width: 576px) {
            .likert-group {
                flex-direction: column;
            }
        }

        .title-highlight {
            width: 30px;
            height: 4px;
            background-color: #FFCD00;
        }

        .form-label {
            font-size: 18px;
        }

        .radio-inputs {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            border-radius: 1rem;
            /* background: linear-gradient(145deg, #e6e6e6, #ffffff); */
            background: #e6e6e6;
            box-sizing: border-box;
            /* box-shadow:
                5px 5px 15px rgba(0, 0, 0, 0.15),
                -5px -5px 15px rgba(255, 255, 255, 0.8); */
            padding: 0.5rem;
            width: 100%;
            font-size: 14px;
            gap: 0.5rem;
        }

        .radio-inputs .radio {
            flex: 1 1 auto;
            text-align: center;
            position: relative;
        }

        .radio-inputs .radio input {
            display: none;
        }

        .radio-inputs .radio .name {
            display: flex;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            border-radius: 0.7rem;
            border: none;
            padding: 0.7rem 0;
            color: #2d3748;
            font-weight: 500;
            font-family: inherit;
            /* background: linear-gradient(145deg, #ffffff, #e6e6e6); */
            background: #ffffff;
            box-shadow:
                3px 3px 6px rgba(0, 0, 0, 0.1),
                -3px -3px 6px rgba(255, 255, 255, 0.7);
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .radio-inputs .radio input:checked+.name {
            background: linear-gradient(145deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            box-shadow:
                inset 2px 2px 5px rgba(0, 0, 0, 0.2),
                inset -2px -2px 5px rgba(255, 255, 255, 0.1),
                3px 3px 8px rgba(59, 130, 246, 0.3);
            transform: translateY(2px);
        }

        /* Hover effect */
        .radio-inputs .radio:hover .name {
            background: linear-gradient(145deg, #f0f0f0, #ffffff);
            transform: translateY(-1px);
            box-shadow:
                4px 4px 8px rgba(0, 0, 0, 0.1),
                -4px -4px 8px rgba(255, 255, 255, 0.8);
        }

        .radio-inputs .radio:hover input:checked+.name {
            transform: translateY(1px);
        }

        /* Animation */
        .radio-inputs .radio input:checked+.name {
            animation: select 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Particles */
        .radio-inputs .radio .name::before,
        .radio-inputs .radio .name::after {
            content: "";
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            opacity: 0;
            pointer-events: none;
        }

        .radio-inputs .radio input:checked+.name::before,
        .radio-inputs .radio input:checked+.name::after {
            animation: particles 0.8s ease-out forwards;
        }

        .radio-inputs .radio .name::before {
            background: #60a5fa;
            box-shadow: 0 0 6px #60a5fa;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .radio-inputs .radio .name::after {
            background: #93c5fd;
            box-shadow: 0 0 8px #93c5fd;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Sparkles */
        .radio-inputs .radio .name::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%),
                    rgba(59, 130, 246, 0.3) 0%,
                    transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .radio-inputs .radio input:checked+.name::after {
            opacity: 1;
            animation: sparkle-bg 1s ease-out forwards;
        }

        /* Multiple particles */
        .radio-inputs .radio input:checked+.name {
            overflow: visible;
        }

        .radio-inputs .radio input:checked+.name::before {
            box-shadow:
                0 0 6px #60a5fa,
                10px -10px 0 #60a5fa,
                -10px -10px 0 #60a5fa;
            animation: multi-particles-top 0.8s ease-out forwards;
        }

        .radio-inputs .radio input:checked+.name::after {
            box-shadow:
                0 0 8px #93c5fd,
                10px 10px 0 #93c5fd,
                -10px 10px 0 #93c5fd;
            animation: multi-particles-bottom 0.8s ease-out forwards;
        }

        @keyframes select {
            0% {
                transform: scale(0.95) translateY(2px);
            }

            50% {
                transform: scale(1.05) translateY(-1px);
            }

            100% {
                transform: scale(1) translateY(2px);
            }
        }

        @keyframes multi-particles-top {
            0% {
                opacity: 1;
                transform: translateX(-50%) translateY(0) scale(1);
            }

            40% {
                opacity: 0.8;
            }

            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px) scale(0);
                box-shadow:
                    0 0 6px transparent,
                    20px -20px 0 transparent,
                    -20px -20px 0 transparent;
            }
        }

        @keyframes multi-particles-bottom {
            0% {
                opacity: 1;
                transform: translateX(-50%) translateY(0) scale(1);
            }

            40% {
                opacity: 0.8;
            }

            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(20px) scale(0);
                box-shadow:
                    0 0 8px transparent,
                    20px 20px 0 transparent,
                    -20px 20px 0 transparent;
            }
        }

        @keyframes sparkle-bg {
            0% {
                opacity: 0;
                transform: scale(0.2);
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: scale(2);
            }
        }

        /* Ripple effect */
        .radio-inputs .radio .name::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%),
                    rgba(255, 255, 255, 0.5) 0%,
                    transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .radio-inputs .radio input:checked+.name::before {
            animation: ripple 0.8s ease-out;
        }

        @keyframes ripple {
            0% {
                opacity: 1;
                transform: scale(0.2);
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 0;
                transform: scale(2.5);
            }
        }

        /* Glowing border */
        .radio-inputs .radio input:checked+.name {
            position: relative;
        }

        .radio-inputs .radio input:checked+.name::after {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: inherit;
            background: linear-gradient(45deg,
                    rgba(59, 130, 246, 0.5),
                    rgba(37, 99, 235, 0.5));
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: border-glow 1.5s ease-in-out infinite alternate;
        }

        @keyframes border-glow {
            0% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            position: relative;
            padding-left: 40px;
            margin-bottom: 12px;
            font-size: 16px;
            cursor: pointer;
            user-select: none;
            color: #2a3547;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .checkbox-container input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 24px;
            width: 24px;
            background-color: #eee;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        .checkbox-container input:checked~.checkmark {
            background-color: #3b82f6;
            box-shadow: 0 3px 7px rgba(33, 150, 243, 0.3);
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 8px;
            top: 4px;
            width: 6px;
            height: 12px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }

        .checkbox-container input:checked~.checkmark:after {
            display: block;
            animation: checkAnim 0.2s forwards;
        }

        @keyframes checkAnim {
            0% {
                height: 0;
            }

            100% {
                height: 12px;
            }
        }
    </style>

    @include('layouts.components.css')
</head>


<body>

    <!-- Top bar -->
    <div class="topbar">
        Partner Satisfaction Survey
    </div>

    <div class="main-wrapper mt-5 mb-5">
        <!-- Header -->
        <div class="header-image">
            <img src="{{ asset('images/coverkuesioner.jpg') }}" alt="Survey Cover">
        </div>

        <!-- Content -->
        <div class="content-section">
            <div class="text-center mb-3">
                <div class="title-highlight mb-2 mx-auto"></div>
                <h3 style="font-weight: 700;">Partnership Evaluation Form</h3>
                <p class="text-muted">(Form Evaluasi Kerja Sama)</p>
            </div>
            <div class="alert alert-primary d-flex align-items-center p-3" role="alert"
                style="background-color: #b8d5fc;">
                <div class="text-dark text-center">
                    <p>
                        We are very grateful for the mutual collaboration that we have established so far. To improve
                        our
                        service,
                        we would like you to give feedback on our service. We would greatly appreciate if you could
                        spend a few
                        minutes to complete our questionnaire. Thank you for your kind assistance.
                    </p>
                    <p>
                        (Kami sangat berterima kasih atas kerja sama yang terjalin hingga saat ini. Untuk meningkatkan
                        layanan,
                        kami mengharapkan masukan atas kinerja kami. Kami ucapkan banyak terima kasih atas kerja sama
                        Bapak/Ibu.)
                    </p>
                </div>
            </div>


            <p class="required"><b>* Required</b></p>

            <form method="POST" action="{{ route('kuesioner.submitFormSurvey') }}" id="formInput"
                enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-semibold mb-2">
                        Institution Name <br>
                        <span class="text-muted fst-italic">Nama Institusi</span>
                        <span class="required">*</span>
                    </label>
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control" id="qinstitution" name="qinstitution"
                            value="{{ @$nama_institusi }}" placeholder="Institution Name">
                        <label for="qinstitution">Institution Name (Nama Institusi)</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold mb-2">
                        Period of partnership <br>
                        <span class="text-muted fst-italic">Jangka waktu kemitraan</span>
                        <span class="required">*</span>
                    </label>
                    <div class="mb-3">
                        <div class="radio-inputs mt-2">
                            @foreach ($arrTahun as $val => $tahun)
                                <label class="radio" for="period_{{ $loop->index }}">
                                    <input type="radio" name="qperiod" id="period_{{ $loop->index }}"
                                        value="{{ $val }}"
                                        {{ isset($qpartner->qperiod) && $qpartner->qperiod == $val ? 'checked' : '' }} />
                                    <span
                                        class="name">{{ $val }}<br><small>{{ $tahun }}</small></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold mb-2">
                        Activity of collaboration <br>
                        <span class="text-muted fst-italic">Aktivitas kerja sama</span>
                        <span class="required">*</span>
                    </label>
                    <div class="mb-3">
                        @foreach ($arrKategori as $val => $label)
                            <label class="checkbox-container">
                                {{ $val }} <span class="ms-1 fst-italic">{{ $label }}</span>
                                <input type="checkbox" class="custom-checkbox" name="qtype[]"
                                    value="{{ $val }}" id="qtype_{{ $loop->index }}"
                                    {{ in_array($val, $AnswerType) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                            </label>
                        @endforeach
                    </div>
                </div>

                @foreach ($questions as $key => $question)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            {{ $loop->iteration }}. {{ $question['en'] }}<br>
                            <span class="text-muted fst-italic">{{ $question['id'] }}</span>
                            <span class="required">*</span>
                        </label>

                        <div class="radio-inputs mt-2">
                            @foreach ($choices as $val => $label)
                                <label class="radio">
                                    <input type="radio" name="{{ $key }}" value="{{ $val }}"
                                        {{ isset($AnswerChoices[$key]) && $AnswerChoices[$key] == $val ? 'checked' : '' }} />
                                    <span
                                        class="name">{{ $val }}<br><small>{{ $label }}</small></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach


                <input type="hidden" name="id_kuesioner" value="{{ $id_kuesioner }}">
                <input type="hidden" name="id_mou" value="{{ $id_mou }}">

                <div class="text-end">
                    <button type="submit" class="btn w-100 btn-primary submit-btn px-4 py-2">Submit Form <i
                            class="bx bx-send ms-2"></i></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const formInput = $("#formInput");

        $(document).ready(function() {
            formInput.on("submit", function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                showLoading("Menyimpan data...");

                $.ajax({
                    url: formInput.attr("action"),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        // Swal.fire({
                        //     icon: "success",
                        //     title: "Berhasil!",
                        //     text: response.message,
                        //     timer: 1000,
                        //     showConfirmButton: false,
                        // }).then(() => (window.location.href = response.route));
                        if (response.status) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: response.message,
                                timer: 1000,
                                showConfirmButton: false,
                            }).then(() => {
                                if (response.route) {
                                    window.location.href = response.route;
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: response.message,
                                timer: 1000,
                                showConfirmButton: false,
                            });
                        }
                    },
                    error: (xhr) => {
                        let errorMessages = "";
                        if (xhr.responseJSON?.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(
                                (messages) => {
                                    errorMessages += messages.join("<br>") + "<br>";
                                }
                            );
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Gagal Menyimpan",
                            html: errorMessages ||
                                xhr.responseJSON?.error ||
                                "Terjadi kesalahan tak terduga.",
                        });
                    },
                });
            });
        });
    </script>

    @include('layouts.components.button-whatsapp')
    @include('layouts.components.footer')


    @include('layouts.components.js')
    @include('layouts.components.main')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/dataTables.fixedColumns.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/fixedColumns.dataTables.js"></script>

    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.bootstrap5.min.js"></script>

    <!-- Plugins Export (Pastikan ini ada!) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>

    @stack('scripts')

</body>

</html>
