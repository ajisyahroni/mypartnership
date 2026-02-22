$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    const formFilter = $("#formFilterRecognition");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");

    let urlVerifikasi = "/recognition/verifikasi";

    window.table = $("#dataTable").DataTable({
        paging: true,
        lengthChange: true,
        lengthMenu: [10, 25, 50, 75, 100],
        searching: true,
        ordering: true,
        info: true,
        stateSave: true,
        autoWidth: false,
        scrollX: true,
        responsive: true,
        language: {
            search: "Pencarian:",
            searchPlaceholder: "Cari Data...",
        },
        buttons: [
            {
                extend: "excelHtml5",
                text: "EXCEL",
                className: "btn btn-sm btn-primary",
            },
            {
                extend: "csvHtml5",
                text: "CSV",
                className: "btn btn-sm btn-success",
            },
        ],
        serverSide: true,
        processing: true,
        ajax: {
            url: getData, // URL endpoint untuk mengambil data
            type: "GET",
            dataType: "json",
            data: function (d) {
                d.id_fakultas = IdFakultas;
                d.tahun = $("#filterRecognition #tahun").val();
            },
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            }, // Tambahkan nomor urut
            { data: "action", orderable: false, searchable: false },
            {
                data: "status_label",
                name: "tbl_recognition.status_verify_kaprodi",
                orderable: true,
            },

            {
                data: "acceptance_form",
                name: "tbl_recognition.acceptance_form",
                orderable: true,
            },

            {
                data: "cv_prof",
                name: "tbl_recognition.cv_prof",
                orderable: true,
            },

            {
                data: "file_sk",
                name: "tbl_recognition.file_sk",
                orderable: true,
            },

            {
                data: "bukti_pelaksanaan",
                name: "tbl_recognition.bukti_pelaksanaan",
                orderable: true,
            },

            {
                data: "add_by",
                name: "tbl_recognition.add_by",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "department",
                name: "tbl_recognition.department",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "nama_prof",
                name: "tbl_recognition.nama_prof",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "univ_asal",
                name: "tbl_recognition.univ_asal",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "bidang_kepakaran",
                name: "tbl_recognition.bidang_kepakaran",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "timestamp_ajuan",
                name: "tbl_recognition.timestamp_ajuan",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "timestamp_selesai",
                name: "tbl_recognition.timestamp_selesai",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
        ],
        createdRow: function (row, data, dataIndex) {
            $("td", row).addClass("border-bottom-0");
            if (dataIndex % 2 === 0) {
                $(row).css("background-color", "#f8f9fa");
            } else {
                $(row).css("background-color", "#ffffff");
            }
        },
    });

    window.table.on("preXhr.dt", function () {
        Swal.fire({
            title: "Mohon tunggu...",
            text: "Sedang memuat data",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
    });

    // Tutup Swal saat data sudah dimuat
    window.table.on("xhr.dt", function () {
        Swal.close();
    });
    // Dropdown Toggle Kolom
    var columns = table
        .columns()
        .header()
        .toArray()
        .map((th) => $(th).text());

    columns.forEach((colName, i) => {
        $("#columnToggleList").append(`
    <li class="dropdown-item toggle-item" data-column="${i}" style="cursor: pointer;background: linear-gradient(135deg, #007bff, #0056b3);color:white;">
       <b>${colName}</b>
    </li>
`);
    });

    // Event untuk Show/Hide Kolom & Toggle Warna
    $(document).on("click", ".toggle-item", function () {
        var columnIndex = $(this).data("column");
        var column = window.table.column(columnIndex);

        // Toggle visibility kolom
        column.visible(!column.visible());

        // Toggle warna latar belakang
        $(this).toggleClass("active-toggle");
    });

    // Tambahkan tombol export ke dalam div yang benar
    window.table.buttons().container().appendTo(".btn-group");

    // Event handler untuk tombol export manual
    $("#btnExcel").on("click", function () {
        window.table.button(".buttons-excel").trigger();
    });

    $("#btnCSV").on("click", function () {
        window.table.button(".buttons-csv").trigger();
    });

    $("#dataTable").on("click", ".btn-verify", function () {
        verifikasi(
            $(this).data("status"),
            $(this).data("id_rec"),
            $(this).data("tipe")
        );
    });

    function verifikasi(status, id_rec, tipe) {
        let title = "";
        let button = "";
        if (status == "1") {
            title += "Anda yakin ingin verifikasi data ini?";
            button += "Verifikasi";

            Swal.fire({
                title: title,
                icon: "warning",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Ya, Verifikasi",
                denyButtonText: "Tolak / Revisi",
                cancelButtonText: "Batal",
                confirmButtonColor: "#3085d6",
                denyButtonColor: "#d33",
                cancelButtonColor: "#f39c12",
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading("Menyimpan data...");
                    $.ajax({
                        url: urlVerifikasi,
                        type: "POST",
                        data: {
                            status: "1",
                            id_rec: id_rec,
                            tipe: tipe,
                            _token: csrfToken,
                        },
                        dataType: "json",
                        success: (res) => {
                            Swal.fire("Berhasil!", res.message, "success");
                            window.table.ajax.reload(null, false);
                        },
                        error: (xhr) => {
                            let errorMessages = "";
                            if (xhr.responseJSON?.errors) {
                                Object.values(xhr.responseJSON.errors).forEach(
                                    (messages) => {
                                        errorMessages +=
                                            messages.join("<br>") + "<br>";
                                    }
                                );
                            }

                            Swal.fire({
                                icon: "error",
                                title: "Gagal Menyimpan",
                                html: errorMessages || "Terjadi Kesalahan.",
                            });
                        },
                    });
                } else if (result.isDenied) {
                    // Jika klik Revisi
                    Swal.fire({
                        title: "Tulis alasan revisi",
                        input: "textarea",
                        inputPlaceholder:
                            "Tuliskan alasan penolakan atau revisi...",
                        showCancelButton: true,
                        confirmButtonText: "Kirim",
                        cancelButtonText: "Batal",
                        preConfirm: (value) => {
                            if (!value) {
                                Swal.showValidationMessage(
                                    "Alasan tidak boleh kosong"
                                );
                            }
                            return value;
                        },
                    }).then((revResult) => {
                        if (revResult.isConfirmed) {
                            showLoading("Mengirim revisi...");

                            $.ajax({
                                url: urlVerifikasi,
                                type: "POST",
                                data: {
                                    status: "0",
                                    id_rec: id_rec,
                                    tipe: tipe,
                                    revisi: revResult.value,
                                    _token: csrfToken,
                                },
                                dataType: "json",
                                success: (res) => {
                                    Swal.fire(
                                        "Revisi Terkirim!",
                                        res.message,
                                        "success"
                                    );
                                    window.table.ajax.reload(null, false);
                                },
                                error: (xhr) => {
                                    let errorMessages = "";
                                    if (xhr.responseJSON?.errors) {
                                        Object.values(
                                            xhr.responseJSON.errors
                                        ).forEach((messages) => {
                                            errorMessages +=
                                                messages.join("<br>") + "<br>";
                                        });
                                    }

                                    Swal.fire({
                                        icon: "error",
                                        title: "Gagal Mengirim",
                                        html:
                                            errorMessages ||
                                            "Terjadi Kesalahan.",
                                    });
                                },
                            });
                        }
                    });
                }
                // Jika Batal → tidak perlu aksi tambahan, Swal otomatis tertutup
            });
        } else {
            button += "Batalkan";
            title += "Anda yakin ingin membatalkan verifikasi data ini?";

            Swal.fire({
                title: title,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Batalkan",
                cancelButtonText: "Batal",
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#f39c12",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika klik Revisi
                    Swal.fire({
                        title: "Tulis alasan revisi",
                        input: "textarea",
                        inputPlaceholder:
                            "Tuliskan alasan penolakan atau revisi...",
                        showCancelButton: true,
                        confirmButtonText: "Kirim",
                        cancelButtonText: "Batal",
                        preConfirm: (value) => {
                            if (!value) {
                                Swal.showValidationMessage(
                                    "Alasan tidak boleh kosong"
                                );
                            }
                            return value;
                        },
                    }).then((revResult) => {
                        if (revResult.isConfirmed) {
                            showLoading("Mengirim revisi...");

                            $.ajax({
                                url: urlVerifikasi,
                                type: "POST",
                                data: {
                                    status: "0",
                                    id_rec: id_rec,
                                    tipe: tipe,
                                    revisi: revResult.value,
                                    _token: csrfToken,
                                },
                                dataType: "json",
                                success: (res) => {
                                    Swal.fire(
                                        "Revisi Terkirim!",
                                        res.message,
                                        "success"
                                    );
                                    window.table.ajax.reload(null, false);
                                },
                                error: (xhr) => {
                                    let errorMessages = "";
                                    if (xhr.responseJSON?.errors) {
                                        Object.values(
                                            xhr.responseJSON.errors
                                        ).forEach((messages) => {
                                            errorMessages +=
                                                messages.join("<br>") + "<br>";
                                        });
                                    }

                                    Swal.fire({
                                        icon: "error",
                                        title: "Gagal Mengirim",
                                        html:
                                            errorMessages ||
                                            "Terjadi Kesalahan.",
                                    });
                                },
                            });
                        }
                    });
                }
                // Jika Batal → tidak perlu aksi tambahan, Swal otomatis tertutup
            });
        }
    }

    $("#dataTable").on("click", ".btn-lihat-file", function () {
        let id_rec = $(this).data("id_rec");
        let title = $(this).data("title");
        let srcPdf = $(this).data("url");
        let encodedSrcPdf = encodeURI(srcPdf);

        $("#modal-lihat-file #DetailLabel").html(title);
        $("#modal-lihat-file").modal("show");

        $("#konten-detail").html(`
            <div class="d-flex justify-content-center my-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);

        setTimeout(function () {
            const fileExt = getFileExtension(srcPdf);
            let embedContent = "";
            if (fileExt === "pdf") {
                embedContent = `<iframe src="${encodedSrcPdf}" width="100%" height="500px" style="border: none;"></iframe>`;
            } else if (["png", "jpg", "jpeg", "webp"].includes(fileExt)) {
                embedContent = `<div class="text-center">
                                    <img src="${encodedSrcPdf}" class="img-fluid" alt="Preview File" style="max-height: 500px;" />
                                </div>`;
            } else {
                embedContent = `<div class="alert alert-warning text-center mt-3">
                                    Format file tidak didukung untuk preview.
                                </div>`;
            }

            $("#konten-detail").html(embedContent);
        }, 300);
    });

    function getFileExtension(url) {
        try {
            if (url.includes("docs.google.com/gview")) {
                const u = new URL(url);
                const originalUrl = u.searchParams.get("url");
                if (!originalUrl) return null;

                return originalUrl.split(".").pop().toLowerCase();
            }

            return url.split(".").pop().toLowerCase();
        } catch (e) {
            return null;
        }
    }

    $("#dataTable").on("click", ".btn-detail-recognition", function () {
        let id_rec = $(this).data("id_rec");
        let title = "Detail Rekognisi";
        $("#modal-lihat-file #DetailLabel").html(title);
        $("#modal-lihat-file").modal("show");

        $("#konten-detail").html(`
            <div class="d-flex justify-content-center my-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        $.ajax({
            url: getDetailRecognition,
            method: "GET",
            data: {
                id_rec: id_rec,
            },
            success: (response) => {
                $("#konten-detail").html(response.html);
            },
        });
    });

    // $("#dataTable").on("click", ".btn-detail", function () {
    //     let id_mou = $(this).data("id_mou");
    //     let srcPdf = $(this).data("srcpdf");
    //     let encodedSrcPdf = encodeURI(srcPdf);

    //     modalDetail.modal("show");
    //     $(".konten-detail").html(`
    //         <div class="d-flex justify-content-center my-3">
    //             <div class="spinner-border text-primary" role="status">
    //                 <span class="visually-hidden">Loading...</span>
    //             </div>
    //         </div>
    //     `);
    //     $.ajax({
    //         url: getDetailPengajuan,
    //         method: "GET",
    //         data: {
    //             id_mou: id_mou,
    //         },
    //         success: (response) => {
    //             $(".konten-detail").html(response.html);
    //         },
    //     });
    // });

    $("#dataTable").on("click", ".btn-hapus", function () {
        hapus($(this).data("id_mou"));
    });

    $(".btn-download-rekognisi").click(function () {
        const params = {
            id_fakultas: IdFakultas,
        };

        const queryString = new URLSearchParams(params).toString();

        const urlDownload = "download_excel_detail?" + queryString;
        // const urlDownload = "download_excel_detail?";

        window.open(urlDownload, "_blank");
    });

    $(".btn-download-rekognisi-user").click(function () {
        const params = {
            id_fakultas: IdFakultas,
            tahun: $("#filterRecognition #tahun").val(),
        };

        const queryString = new URLSearchParams(params).toString();
        const urlDownload = "download_excel?" + queryString;
        // const urlDownload = "download_excel_detail?";

        window.open(urlDownload, "_blank");
    });

    formFilter.on("submit", function (e) {
        e.preventDefault();
        showLoading("Menerapkan Filter...");
        window.table.ajax.reload(function () {
            closeLoading();
        }, false);
    });
});
