$(document).ready(function () {
    const formInput = $("#formInput");

    $("#id_mou")
        .select2({
            theme: "bootstrap-5",
            allowClear: true,
            placeholder: "Pilih Dokumen Kerja Sama (Masukkan Nama Institusi)",
            minimumInputLength: 3,
            language: {
                inputTooShort: function (args) {
                    return "Masukkan minimal 3 karakter";
                },
            },
        })
        .on("select2:select select2:clear", function () {
            $(this).trigger("change");
        });

    // Jenis Hibah
    $("#jenis_hibah").on("change", function () {
        var value = $("#jenis_hibah").val();
        var TextHtml = "";
        var maksimum = $("#jenis_hibah option:selected").data("maksimum");
        var jenis_hibah = $("#jenis_hibah option:selected").data("jenis_hibah");
        var dl_proposal = $("#jenis_hibah option:selected").data("dl_proposal");
        var dl_laporan = $("#jenis_hibah option:selected").data("dl_laporan");

        TextHtml += "<b>Jenis Hibah</b> :" + jenis_hibah + "<br>";
        TextHtml += "<b>Maksimum Dana</b> : Rp. " + maksimum + "<br>";
        TextHtml += "<b>Deadline Proposal</b> :" + dl_proposal + "<br>";
        TextHtml += "<b>Deadline Laporan</b> :" + dl_laporan + "<br>";

        $("#fill_jenis_hibah_wrapper").addClass("d-none").hide();
        $(".fill_jenis_hibah").html("Pilih Jenis Hibah");
        if (value != "") {
            $("#fill_jenis_hibah_wrapper").removeClass("d-none").fadeIn();
            $(".fill_jenis_hibah").html(TextHtml);
        }
    });

    // Dokumen MoU/MoA
    $("#id_mou").on("change", function () {
        var value = $("#id_mou").val();
        var dokumenMoU = "";
        var Tanggal = $("#id_mou option:selected").data("tanggal");
        var NamaInstitusi = $("#id_mou option:selected").data("nama_institusi");
        var jenisInstitusi = $("#id_mou option:selected").data(
            "jenis_institusi"
        );
        var jenisKerjaSama = $("#id_mou option:selected").data(
            "jenis_kerjasama"
        );
        var kontribusi = $("#id_mou option:selected").data("kontribusi");
        var status_mou = $("#id_mou option:selected").data("status_mou");
        var periode_kerma = $("#id_mou option:selected").data("periode_kerma");

        dokumenMoU += "<b>Nama Institusi</b> :" + NamaInstitusi;
        dokumenMoU += "<br><b>Jenis Kerja Sama</b> :" + jenisKerjaSama;
        dokumenMoU += "<br><b>Jenis Institusi</b> :" + jenisInstitusi;
        dokumenMoU += "<br><b>Kontribusi</b> :" + kontribusi;
        if (periode_kerma == "notknown") {
            dokumenMoU += "<br><b>Status MoU</b> :" + status_mou;
        }
        dokumenMoU += "<br><b>Tanggal Kerja Sama</b> :" + Tanggal;

        $("#fill_id_mou_wrapper").addClass("d-none").hide();
        $(".fill_id_mou").html("Pilih Dokumen MoU");
        if (value != "") {
            $("#fill_id_mou_wrapper").removeClass("d-none").fadeIn();
            $(".fill_id_mou").html(dokumenMoU);
        }
    });

    // Ketika radio Fakultas, Prodi, atau Unit dipilih
    $('input[name="pelaksana_prodi_unit"]').change(function () {
        var selectedValue = $(this).val();

        // Sembunyikan semua dropdown dulu
        $(".dropdown-prodi_unit").addClass("d-none").hide();
        $("#select_fakultas, #select_prodi, #select_unit")
            .val("")
            .trigger("change");
        // Tampilkan dropdown sesuai pilihan radio
        if (selectedValue === "Fakultas") {
            $("#select_fakultas_wrapper").removeClass("d-none").fadeIn();
        } else if (selectedValue === "Program Studi") {
            $("#select_prodi_wrapper").removeClass("d-none").fadeIn();
        } else if (selectedValue === "Unit (Biro/Lembaga)") {
            $("#select_unit_wrapper").removeClass("d-none").fadeIn();
        }
    });

    $('input[name="bentuk_kegiatan[]"]').change(function () {
        if ($(this).val() === "Lain-lain" && $(this).is(":checked")) {
            $(".bentuk_kegiatan_lain_wrapper")
                .removeClass("d-none")
                .hide()
                .fadeIn();
        } else if ($(this).val() === "Lain-lain" && !$(this).is(":checked")) {
            $(".bentuk_kegiatan_lain_wrapper").fadeOut(function () {
                $(this).addClass("d-none");
            });
            $("#bentuk_kegiatan_lain").val(""); // Reset input jika tidak dicentang
        }
    });

    let urlActionSimpan = null;
    let isDraft = false;
    $("#btnDraft, #btnSubmit").on("click", function () {
        urlActionSimpan = $(this).data("action");
        isDraft = $(this).attr("id") === "btnDraft";
    });

    formInput.on("submit", function (e) {
        e.preventDefault();

        if (!validateAll()) {
            return false;
        }

        let formData = new FormData(this);
        showLoading("Menyimpan data...");

        $.ajax({
            // url: formInput.attr("action"),
            url: urlActionSimpan,
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
                        "Terjadi kesalahan tak terduga.",
                });
            },
        });
    });

    function validateWordCount(fieldId, minWords, fieldName) {
        let html = $(fieldId).summernote("code");
        let text = $("<div>").html(html).text().trim();

        // Hitung kata
        let words = text.split(/\s+/).filter((w) => w.length > 0);
        let wordCount = words.length;

        if (wordCount < minWords) {
            Swal.fire({
                icon: "warning",
                title: fieldName + " tidak valid",
                html: `
                    <b>${fieldName}</b> harus berisi minimal <b>${minWords}</b> kata.<br>
                    Jumlah kata saat ini: <b>${wordCount}</b>
                `,
            });
            $(fieldId).summernote("focus");
            return false;
        }

        return true;
    }

    const H = window.hibahSettings || {
        minLatarBelakang: 0,
        minTujuan: 0,
        minDetailInstitusiMitra: 0,
        minDetailKerma: 0,
        minTarget: 0,
        minIndikatorKeberhasilan: 0,
        minRencana: 0,
    };

    function validateAll() {
        if (isDraft) {
            return true;
        }

        if (
            !validateWordCount(
                "#latar_belakang",
                H.minLatarBelakang,
                "Latar Belakang"
            )
        )
            return false;
        if (!validateWordCount("#tujuan", H.minTujuan, "Tujuan Proposal"))
            return false;
        if (
            !validateWordCount(
                "#detail_institusi_mitra",
                H.minDetailInstitusiMitra,
                "Detail Institusi Mitra"
            )
        )
            return false;
        if (
            !validateWordCount(
                "#detail_kerma",
                H.minDetailKerma,
                "Detail Kerjasama"
            )
        )
            return false;
        if (!validateWordCount("#target", H.minTarget, "Target Proposal"))
            return false;
        if (
            !validateWordCount(
                "#indikator_keberhasilan",
                H.minIndikatorKeberhasilan,
                "Indikator Keberhasilan"
            )
        )
            return false;
        if (!validateWordCount("#rencana", H.minRencana, "Rencana Proposal"))
            return false;

        return true;
    }
});
