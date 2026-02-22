$(document).ready(function () {
    const formInput = $("#formInput");

    $("#mulai, #selesai").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        yearRange: "2000:2050",
    });

    // Dokumen MoU/MoA
    $("#pilih_mou").on("change", function () {
        var value = $("#pilih_mou").val();
        var dokumenMoU = "";
        var Tanggal = $("#pilih_mou option:selected").data("tanggal");
        var NamaInstitusi = $("#pilih_mou option:selected").data(
            "nama_institusi"
        );
        var jenisInstitusi = $("#pilih_mou option:selected").data(
            "jenis_institusi"
        );
        var kontribusi = $("#pilih_mou option:selected").data("kontribusi");
        var status_mou = $("#pilih_mou option:selected").data("status_mou");
        var periode_kerma = $("#pilih_mou option:selected").data(
            "periode_kerma"
        );

        dokumenMoU += "<b>Nama Institusi</b> :" + NamaInstitusi;
        dokumenMoU += "<br><b>Jenis Institusi</b> :" + jenisInstitusi;
        dokumenMoU += "<br><b>Kontribusi</b> :" + kontribusi;

        if (periode_kerma == "notknown") {
            dokumenMoU += "<br><b>Status MoU</b> :" + status_mou;
        }
        dokumenMoU += "<br><b>Tanggal Kerja Sama</b> :" + Tanggal;

        $("#fill_pilih_mou_wrapper").addClass("d-none").hide();
        $(".fill_pilih_mou").html("Pilih Dokumen MoU");
        if (value != "") {
            $("#fill_pilih_mou_wrapper").removeClass("d-none").fadeIn();
            $(".fill_pilih_mou").html(dokumenMoU);
        }
    });

    // Auto-set minDate untuk 'Tanggal Selesai' agar tidak bisa sebelum 'Tanggal Mulai'
    $("#mulai").on("change", function () {
        var startDate = $(this).datepicker("getDate");
        $("#selesai").datepicker("option", "minDate", startDate);
    });

    $("#jenis_kerjasama").change(function () {
        const selected = $(this).find(":selected");
        const alias = selected.data("alias");
        const lingkupUnit = (selected.data("lingkup_unit") || "")
            .split(",")
            .map((u) => u.trim())
            .filter((u) => u.length > 0);

        const $pilihMou = $("#pilih_mou");
        const $mouWrapper = $("#pilih_mou_wrapper");
        $pilihMou.html(originalMoUOptions);
        const allMoUOptions = $pilihMou.find("option");

        $("#tingkat_kerjasama .form-check").addClass("d-none").hide();

        lingkupUnit.forEach((unit) => {
            const className = `tingkat-${unit
                .toLowerCase()
                .replace(/[^a-z0-9]/g, "")}`;
            const safeClass = $.escapeSelector(className);

            $(`#tingkat_kerjasama .${safeClass}`)
                .removeClass("d-none")
                .fadeIn();
        });

        const filterMoUOptions = (conditionFn) => {
            let html =
                '<option value="">Pilih Jenis MoU (Masukkan Nama Institusi)</option>';

            allMoUOptions.each(function () {
                const $opt = $(this);
                const jenis = $opt.data("jenis_kerjasama");

                if (!conditionFn(jenis)) return;

                const nama = $opt.data("nama_institusi");
                const jenisInstitusi = $opt.data("jenis_institusi");
                const tanggal = $opt.data("tanggal");
                const periode_kerma = $opt.data("periode_kerma");
                const status_tempat = $opt.data("status_tempat");
                const status_mou = $opt.data("status_mou");
                const kontribusi = $opt.data("kontribusi");

                html += `
                <option value="${$opt.val()}"
                    data-nama_institusi="${nama}"
                    data-jenis_kerjasama="${jenis}"
                    data-jenis_institusi="${jenisInstitusi}"
                    data-status_tempat="${status_tempat}"
                    data-periode_kerma="${periode_kerma}"
                    data-status_mou="${status_mou}"
                    data-tanggal="${tanggal}"
                    data-kontribusi="${kontribusi}">
                    ${nama} |
                    ${status_tempat} |
                    ${jenisInstitusi} |
                    ${jenis} | 
                    ${tanggal}
                </option>`;
            });

            $pilihMou.html(html);
        };

        if (alias === "MoA") {
            filterMoUOptions(
                (jenis) =>
                    jenis ===
                        "Memorandum of Understanding (MoU) / Perjanjian Kerja Sama (PKS)" ||
                    jenis === "Memorandum of Understanding (MoU)" ||
                    jenis ===
                        "Memorandum of Understanding (MoU)/ Nota Kesepahaman"
            );
            $mouWrapper.removeClass("d-none").fadeIn();
        } else if (alias === "IA") {
            filterMoUOptions(
                (jenis) =>
                    jenis ===
                        "Memorandum of Understanding (MoU) / Perjanjian Kerja Sama (PKS)" ||
                    jenis === "Memorandum of Understanding (MoU)" ||
                    jenis ===
                        "Memorandum of Understanding (MoU)/ Nota Kesepahaman" ||
                    jenis === "Memorandum of Agreement (MoA)"
            );
            $mouWrapper.removeClass("d-none").fadeIn();
        } else {
            $mouWrapper.addClass("d-none").hide();
        }

        $pilihMou.val("").trigger("change");
        $('input[name="prodi_unit"]').prop("checked", false);
        $(".dropdown-prodi_unit").addClass("d-none").hide();
    });

    $("#select_institusi_mitra").change(function () {
        var selectedOption = $(this).find(":selected");
        var alias = selectedOption.data("alias");

        $(
            "#mitra_universitas_wrapper, #jenis_institusi_wrapper, #select_perusahaan_wrapper"
        )
            .addClass("d-none")
            .hide();
        // $("#jenis_institusi_wrapper").addClass("d-none").hide();

        if (alias === "universitas") {
            $("#mitra_universitas_wrapper").removeClass("d-none").fadeIn();
            // $("#jenis_institusi_wrapper").addClass("d-none").hide();
        } else if (alias == "perusahaan") {
            $("#select_perusahaan_wrapper").removeClass("d-none").fadeIn();
        } else if (alias == "Lain-lain") {
            $("#jenis_institusi_wrapper").removeClass("d-none").fadeIn();
            // $("#mitra_universitas_wrapper").addClass("d-none").fadeIn();
        }
    });

    // Ketika radio Fakultas, Prodi, atau Unit dipilih
    $('input[name="prodi_unit"]').change(function () {
        var selectedValue = $(this).val();

        // Sembunyikan semua dropdown dulu
        $(".dropdown-prodi_unit").addClass("d-none").hide();
        // $("#select_fakultas").val("").trigger("change");
        // $("#select_prodi, #select_unit").val("").trigger("change");

        function setSelectIfExists($select, value) {
            if ($select.find(`option[value="${value}"]`).length > 0) {
                $select.val(value).trigger("change");
            } else {
                $select.val("").trigger("change");
            }
        }

        // panggil fungsi
        setSelectIfExists(
            $("#select_fakultas"),
            $fakultasUser == "lmbg1001" ? $prodiUser : $fakultasUser
        );
        setSelectIfExists($("#select_prodi"), $prodiUser ?? "");
        setSelectIfExists($("#select_unit"), $prodiUser ?? "");

        // $("#select_fakultas")
        //     .val($fakultasUser ?? "")
        //     .trigger("change");

        // $("#select_prodi, #select_unit")
        //     .val($prodiUser ?? "")
        //     .trigger("change");

        // Tampilkan dropdown sesuai pilihan radio
        if (selectedValue === "Fakultas") {
            $("#select_fakultas_wrapper").removeClass("d-none").fadeIn();
        } else if (selectedValue === "Program Studi") {
            $("#select_prodi_wrapper").removeClass("d-none").fadeIn();
        } else if (selectedValue === "Unit (Biro/Lembaga)") {
            $("#select_unit_wrapper").removeClass("d-none").fadeIn();
        }
    });

    // Periode Kerja Sama
    $('input[name="periode_kerma"]').change(function () {
        var selectedValue = $(this).val();

        // Sembunyikan semua dropdown dulu
        $(".select_periode_kerma_wrapper").addClass("d-none").hide();
        $("#status_mou, #tanggal_mulai, #tanggal_selesai")
            .val("")
            .trigger("change");
        // Tampilkan dropdown sesuai pilihan radio
        $("#tgl_mulai_wrapper").removeClass("d-none").fadeIn();
        if (selectedValue === "bydoc") {
            $("#tgl_selesai_wrapper").removeClass("d-none").fadeIn();
        } else if (selectedValue === "notknown") {
            $("#status_mou_wrapper").removeClass("d-none").fadeIn();
        }
    });

    $('input[name="dn_ln"]').change(function () {
        var selectedValue = $(this).val();
        // Sembunyikan semua dropdown dulu
        $("#wilayah_mitra_wrapper").addClass("d-none").hide();
        $("#negara_mitra_wrapper").addClass("d-none").hide();

        $("input[name='wilayah_mitra']").prop("checked", false);
        $("input[name='negara_mitra']").trigger("change");

        // Tampilkan dropdown sesuai pilihan radio
        if (selectedValue === "Dalam Negeri") {
            $("#wilayah_mitra_wrapper").removeClass("d-none").fadeIn();
        } else if (selectedValue === "Luar Negeri") {
            $("#negara_mitra_wrapper").removeClass("d-none").fadeIn();
        }
    });

    $('input[name="status_kerma"]').change(function () {
        var selectedValue = $(this).val();

        $("#mou_perpanjangan_wrapper").addClass("d-none").hide();
        $("input[name='mou_perpanjangan']").trigger("change");

        if (selectedValue === "Dalam Perpanjangan") {
            $("#mou_perpanjangan_wrapper").removeClass("d-none").fadeIn();
        }
    });

    $("#mou_perpanjangan").on("change", function () {
        var value = $("#mou_perpanjangan").val();
        var dokumenMoU = "";
        var Tanggal = $("#mou_perpanjangan option:selected").data("tanggal");
        var NamaInstitusi = $("#mou_perpanjangan option:selected").data(
            "nama_institusi"
        );
        var jenisInstitusi = $("#mou_perpanjangan option:selected").data(
            "jenis_institusi"
        );
        var kontribusi = $("#mou_perpanjangan option:selected").data(
            "kontribusi"
        );
        var status_mou = $("#mou_perpanjangan option:selected").data(
            "status_mou"
        );
        var periode_kerma = $("#mou_perpanjangan option:selected").data(
            "periode_kerma"
        );

        dokumenMoU += "<b>Nama Institusi</b> :" + NamaInstitusi;
        dokumenMoU += "<br><b>Jenis Institusi</b> :" + jenisInstitusi;
        dokumenMoU += "<br><b>Kontribusi</b> :" + kontribusi;

        if (periode_kerma == "notknown") {
            dokumenMoU += "<br><b>Status MoU</b> :" + status_mou;
        }
        dokumenMoU += "<br><b>Tanggal Kerja Sama</b> :" + Tanggal;

        $("#fill_mou_perpanjangan_wrapper").addClass("d-none").hide();
        $(".fill_mou_perpanjangan").html(
            "Pilih dokumen MoU yang akan diperpanjang"
        );
        if (value != "") {
            $("#fill_mou_perpanjangan_wrapper").removeClass("d-none").fadeIn();
            $(".fill_mou_perpanjangan").html(dokumenMoU);
        }
    });

    $('input[name="kontribusi[]"]').change(function () {
        let isLainLainChecked =
            $('input[name="kontribusi[]"]:checked').filter(function () {
                return $(this).val().toLowerCase() === "lain-lain";
            }).length > 0;

        if (isLainLainChecked) {
            $(".kontribusi_lain_wrapper").removeClass("d-none").hide().fadeIn();
        } else {
            $(".kontribusi_lain_wrapper").fadeOut(function () {
                $(this).addClass("d-none");
            });
            $("#kontribusi_lain").val("");
        }
    });

    $(
        ".dropdown-menu .dropdown-item:contains('Penanggung Jawab Internal')"
    ).click(function (e) {
        e.preventDefault();
        let internalTemplate = `
            <div class="pihak_internal_wrapper">
                <div class="card-header bg-light d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bx bx-user-circle me-2 text-primary fs-4"></i>
                        <strong>Pihak Internal</strong>
                    </div>
                    <button class="btn btn-danger btn-sm hapus_internal" type="button">X Hapus</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="ttd_internal[]" class="form-control" placeholder="Tulis disini">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="lvl_internal[]" class="form-control" placeholder="Tulis disini">
                    </div>
                </div>
            </div>`;
        $(".pihak-internal").append(internalTemplate);
    });

    // Fungsi untuk menambah pihak eksternal
    $(".dropdown-menu .dropdown-item:contains('Penanggung Jawab Mitra')").click(
        function (e) {
            e.preventDefault();
            let mitraTemplate = `
            <div class="pihak_mitra_wrapper">
                <div class="card-header bg-light d-flex align-items-center justify-content-between">
                    <div>
                        <i class="bx bx-user-circle me-2 text-primary fs-4"></i>
                        <strong>Pihak Mitra</strong>
                    </div>
                    <button class="btn btn-danger btn-sm hapus_eksternal" type="button">X Hapus</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="ttd_eksternal[]" class="form-control" placeholder="Tulis disini">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="lvl_eksternal[]" class="form-control" placeholder="Tulis disini">
                    </div>
                </div>
            </div>`;
            $(".pihak-mitra").append(mitraTemplate);
        }
    );

    // Fungsi untuk menghapus pihak internal
    $(document).on("click", ".hapus_internal", function () {
        $(this)
            .closest(".pihak_internal_wrapper")
            .fadeOut(300, function () {
                $(this).remove();
            });
    });

    // Fungsi untuk menghapus pihak eksternal
    $(document).on("click", ".hapus_eksternal", function () {
        $(this)
            .closest(".pihak_mitra_wrapper")
            .fadeOut(300, function () {
                $(this).remove();
            });
    });

    formInput.on("submit", function (e) {
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
                    html:
                        errorMessages ||
                        xhr.responseJSON?.error ||
                        xhr.responseText ||
                        "Terjadi kesalahan.",
                });
            },
        });
    });
});
