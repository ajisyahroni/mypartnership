$(document).ready(function () {
    showSurveyNotifikasi();

    $("#formInput").on("submit", function (e) {
        e.preventDefault(); // Mencegah form untuk submit secara default

        // Ambil data dari form
        var formData = new FormData(this);

        showLoading("Menyimpan data..."); // Menampilkan indikator loading (opsional)

        // Menyembunyikan modal pengisian survei
        $("#surveyFillModal").modal("hide");

        // Mengirim data dengan AJAX
        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message,
                    timer: 1000,
                    showConfirmButton: false,
                }).then(() => showSurveyNotifikasi());
            },
            error: function () {
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

function fillSurvey(id_mou, status_kerma) {
    // Ketika tombol "Isi Survei" diklik
    $("#surveiModal").modal("hide");
    $("#surveyFillModal").modal("show");
    $("#surveyFormContent").html('<span class="text-center">Loading...</span>');
    $.ajax({
        url: "/survei/" + id_mou + "/" + status_kerma, // Ganti dengan route yang sesuai untuk mengambil form
        method: "GET",
        success: function (response) {
            // Isi konten modal dengan response (form pengisian survei)
            $("#surveyFormContent").html(response.form);

            // Tampilkan modal pengisian survei
            $("#surveyFillModal").modal("show");
        },
        error: function () {
            alert("Terjadi kesalahan saat mengambil data survei.");
        },
    });
}

function showSurveyNotifikasi() {
    $.ajax({
        url: "/survei/getSurvei",
        method: "GET",
        success: function (response) {
            // Isi konten modal dengan response (form pengisian survei)
            $("#surveiNotifikasi").html(response.form);
            if (response.surveiNotComplete.length > 0) {
                $("#surveiModal").modal("show");
            }
        },
        error: function () {
            alert("Terjadi kesalahan saat mengambil data survei.");
        },
    });
}
