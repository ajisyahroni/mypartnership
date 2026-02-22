$(document).ready(function () {
    const formInput = $("#formInput");

    $("#id_mou")
        .select2({
            theme: "bootstrap-5",
            allowClear: true,
            minimumInputLength: 3,
            language: {
                inputTooShort: function (args) {
                    return "Masukkan minimal 3 karakter";
                },
            },
            placeholder: "Pilih Jenis Dokumen (Masukkan Nama Institusi)",
        })
        .on("select2:select select2:clear", function () {
            $(this).trigger("change");
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

        function setSelectIfExists($select, value) {
            if ($select.find(`option[value="${value}"]`).length > 0) {
                $select.val(value).trigger("change");
            } else {
                $select.val("").trigger("change");
            }
        }

        // Sembunyikan semua dropdown dulu
        $(".dropdown-prodi_unit").addClass("d-none").hide();
        // $("#select_fakultas, #select_prodi, #select_unit")
        //     .val("")
        //     .trigger("change");

        setSelectIfExists($("#select_fakultas"), $fakultasUser ?? "");
        setSelectIfExists($("#select_prodi"), $prodiUser ?? "");
        setSelectIfExists($("#select_unit"), $prodiUser ?? "");

        // Tampilkan dropdown sesuai pilihan radio
        if (selectedValue === "Fakultas") {
            $("#select_fakultas_wrapper").removeClass("d-none").fadeIn();
        } else if (selectedValue === "Program Studi") {
            $("#select_prodi_wrapper").removeClass("d-none").fadeIn();
        } else if (selectedValue === "Unit (Biro/Lembaga)") {
            $("#select_unit_wrapper").removeClass("d-none").fadeIn();
        }
    });

    $('input[name="kontribusi[]"]').change(function () {
        if ($("#checkbox-lain").is(":checked")) {
            $(".kontribusi_lain_wrapper").removeClass("d-none").hide().fadeIn();
        } else {
            $(".kontribusi_lain_wrapper").fadeOut(function () {
                $(this).addClass("d-none");
            });
            $("#kontribusi_lain").val("");
        }
    });

    formInput.on("submit", function (e) {
        e.preventDefault();

        let html = $("#deskripsi_singkat").summernote("code");
        let text = $("<div>").html(html).text().trim();
        let length = text.length;

        if (length < 100 || length > 500) {
            Swal.fire({
                icon: "warning",
                title: "Jumlah karakter tidak valid",
                html: `
                    Deskripsi Singkat Kegiatan harus diisi minimal <b>100</b> dan maksimal <b>500</b> karakter.<br><br>
                    Jumlah karakter saat ini: <b>${length}</b>
                `,
            });

            $("#deskripsi_singkat").summernote("focus");
            return false;
        }

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
                        "Terjadi kesalahan tak terduga.",
                });
            },
        });
    });
});
