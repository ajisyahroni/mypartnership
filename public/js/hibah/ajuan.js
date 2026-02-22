$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    const formInput = $("#formInput");
    const formFilter = $("#formFilterPengajuan");
    const formVerifikasiTahap = $("#formVerifikasiTahap");
    const urlHapus = "/hibah-kerjasama/destroy";
    const urlDetailHibah = "/hibah-kerjasama/detailHibah";
    const urlDetailLaporanHibah = "/hibah-kerjasama/detailLaporanHibah";
    const urlShowVerifikasiTahap = "/hibah-kerjasama/showVerifikasiTahap";
    let urlVerifikasi = "/hibah-kerjasama/verifikasi";

    const placeholdersHibah = {
        nama_institusi: "Pilih Nama Institusi",
        judul_proposal: "Pilih Judul Proposal",
        jenis_hibah: "Pilih Jenis Hibah",
        fakultas: "Pilih Fakultas",
        program_studi: "Pilih Program Studi",
        // status: "Pilih Status Ajuan",
    };

    $.each(placeholdersHibah, function (id, placeholderText) {
        $("#" + id).select2({
            theme: "bootstrap-5",
            placeholder: placeholderText,
            allowClear: true,
            minimumInputLength: 3,
            language: {
                inputTooShort: function () {
                    return "Masukkan minimal 3 karakter";
                },
            },
        });
    });

    window.table = $("#dataTable").DataTable({
        paging: true,
        lengthChange: true,
        lengthMenu: [10, 25, 50, 75, 100],
        searching: true,
        ordering: true,
        info: true,
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
                d.judul_proposal = $("#filterContent #judul_proposal").val();
                d.nama_institusi = $("#filterContent #nama_institusi").val();
                d.hibah = $("#filterContent #hibah").val();
                d.jenis_hibah = $("#filterContent #jenis_hibah").val();
                d.program_studi = $("#filterContent #program_studi").val();
                d.fakultas = $("#filterContent #fakultas").val();
                d.status = $("#filterContent #status").val();
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
                data: "judul_proposal",
                name: "judul_proposal",
                orderable: true,
                searchable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "institusi_mitra",
                name: "institusi_mitra",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "jenis_hibah",
                name: "jenis_hibah",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "ketua_pelaksana",
                name: "tbl_ajuan_hibah.ketua_pelaksana",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "nama_prodi",
                name: "nama_prodi",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${
                        data ?? ""
                    }</span>`,
            },
            {
                data: "nama_fakultas",
                name: "nama_fakultas",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${
                        data ?? ""
                    }</span>`,
            },
            {
                data: "tanggal_pelaksanaan",
                name: "tbl_ajuan_hibah.tgl_mulai",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "file_kontrak",
                name: "tbl_ajuan_hibah.file_kontrak",
                orderable: true,
            },
            {
                data: "status",
                name: "status_selesai",
                orderable: true,
            },
            {
                data: "pengusul",
                name: "nama_pengusul",
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

    $("#dataTable").on("click", ".btn-detail", function () {
        $("#modal-detail").modal("show");
        $("#content-detail-hibah").html(`<div id="loading">
                        <div class="d-flex justify-content-center my-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>`);
        $("#modal-detail .modal-title").html(
            '<i class="bx bx-detail"></i> Detail Ajuan Hibah'
        );
        $.ajax({
            url: urlDetailHibah,
            type: "GET",
            data: { id_hibah: $(this).data("id_hibah") },
            dataType: "json",
            success: (res) => {
                $("#content-detail-hibah").html(res);
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
                    title: "Gagal Memuat",
                    html: errorMessages || "Terjadi Kesalahan.",
                });
            },
        });
    });

    $("#dataTable").on("click", ".verifikasi-pencairan", function () {
        $("#modal-verifikasi-pencairan").modal("show");
        $("#modal-verifikasi-pencairan .modal-title").html(
            $(this).data("title")
        );
        $("#content-detail-pencairan").html(`<div id="loading">
                        <div class="d-flex justify-content-center my-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>`);
        $.ajax({
            url: urlShowVerifikasiTahap,
            type: "GET",
            data: {
                id_hibah: $(this).data("id_hibah"),
                tahap: $(this).data("tahap"),
            },
            dataType: "json",
            success: (res) => {
                $("#content-detail-pencairan").html(res);
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
                    title: "Gagal Memuat",
                    html: errorMessages || "Terjadi Kesalahan.",
                });
            },
        });
    });

    $("#dataTable").on("click", ".btn-detail-laporan", function () {
        $("#modal-detail").modal("show");
        $("#content-detail-hibah").html(`<div id="loading">
                        <div class="d-flex justify-content-center my-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>`);
        $("#modal-detail .modal-title").html(
            '<i class="bx bx-detail"></i> Detail Laporan Hibah'
        );
        $.ajax({
            url: urlDetailLaporanHibah,
            type: "GET",
            data: { id_laporan_hibah: $(this).data("id_laporan_hibah") },
            dataType: "json",
            success: (res) => {
                $("#content-detail-hibah").html(res);
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
                    title: "Gagal Memuat",
                    html: errorMessages || "Terjadi Kesalahan.",
                });
            },
        });
    });

    $("#dataTable").on("click", ".btn-lihat-file", function () {
        let id_hibah = $(this).data("id_hibah");
        let title = $(this).data("title");
        let srcPdf = $(this).data("url");
        let encodedSrcPdf = encodeURI(srcPdf);
        let flag = "file_kontrak";

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
            const fileExt = srcPdf.split(".").pop().toLowerCase();
            let embedContent = "";

            if (currentRole == "admin") {
                embedContent += `
                <form id="uploadForm-${id_hibah}-${flag}" enctype="multipart/form-data" 
                      style="display: inline;" onsubmit="return false;">
                    
                    <input type="file" name="file" style="display:none;"
                           onchange="uploadFile('${id_hibah}', '${flag}')"
                           id="fileInput-${id_hibah}-${flag}" accept=".pdf">
                    
                    <button type="button" 
                            onclick="$('#fileInput-${id_hibah}-${flag}').click();" 
                            class="btn mb-2 w-100 btn-sm btn-warning" 
                            data-title-tooltip="Upload File Kontrak">
                        <i class="bx bx-upload"></i>
                    </button>

                    <div class="spinner-border text-primary ms-2 d-none" 
                         role="status" 
                         id="loader-${id_hibah}-${flag}" 
                         style="width: 1rem; height: 1rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </form>
            `;
            }

            if (fileExt === "pdf") {
                embedContent += `<iframe src="${encodedSrcPdf}" width="100%" height="500px" style="border: none;"></iframe>`;
            } else if (["png", "jpg", "jpeg", "webp"].includes(fileExt)) {
                embedContent += `<div class="text-center">
                                    <img src="${encodedSrcPdf}" class="img-fluid" alt="Preview File" style="max-height: 500px;" />
                                </div>`;
            } else {
                embedContent += `<div class="alert alert-warning text-center mt-3">
                                    Format file tidak didukung untuk preview.
                                </div>`;
            }

            $("#konten-detail").html(embedContent);
        }, 300);
    });

    $("#dataTable").on("click", ".btn-hapus", function () {
        hapus($(this).data("id_hibah"));
    });

    $("#dataTable").on("click", ".btn-verify", function () {
        verifikasi(
            $(this).data("tipe"),
            $(this).data("status"),
            $(this).data("id_hibah")
        );
    });

    $("#dataTable").on("click", ".btn-export", function () {
        let id = $(this).data("id_hibah");
        window.open("/hibah-kerjasama/export_proposal/" + id, "_blank");
    });
    $("#dataTable").on("click", ".btn-export-proposal", function () {
        let id = $(this).data("id_hibah");
        window.open("/hibah-kerjasama/export_proposal/" + id, "_blank");
    });

    $("#dataTable").on("click", ".btn-export-laporan", function () {
        let id = $(this).data("id_hibah");
        window.open("/hibah-kerjasama/export_laporan/" + id, "_blank");
    });

    function hapus(id_hibah) {
        Swal.fire({
            text: "Anda yakin ingin menghapus data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading("Menghapus data...");
                $.ajax({
                    url: urlHapus,
                    type: "POST",
                    data: { id_hibah, _token: csrfToken },
                    dataType: "json",
                    success: (res) => {
                        if (res.status) {
                            Swal.fire("Berhasil!", res.message, "success");
                        } else {
                            Swal.fire("Gagal!", res.message, "error");
                        }

                        setTimeout(() => {
                            window.table.ajax.reload(null, false);
                        }, 800);
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
            }
        });
    }

    function verifikasi(tipe, status, id_hibah) {
        let title = "";
        let button = "";
        if (tipe == "selesai") {
            if (status == "1") {
                title += "Anda yakin ingin menyelesaikan ajuan ini?";
                button += "Selesaikan";

                Swal.fire({
                    title: title,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Selesaikan",
                    cancelButtonText: "Batal",
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#f39c12",
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika klik Verifikasi → Minta input point
                        showLoading("Menyimpan data...");

                        $.ajax({
                            url: urlVerifikasi,
                            type: "POST",
                            data: {
                                status: "1",
                                tipe: tipe,
                                id_hibah: id_hibah,
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
                                    Object.values(
                                        xhr.responseJSON.errors
                                    ).forEach((messages) => {
                                        errorMessages +=
                                            messages.join("<br>") + "<br>";
                                    });
                                } else if (xhr.responseJSON?.message) {
                                    // Tangkap error dari 'message' jika tidak ada 'errors'
                                    errorMessages = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal Menyimpan",
                                    html: errorMessages || "Terjadi Kesalahan.",
                                });
                            },
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
                                        tipe: tipe,
                                        id_hibah: id_hibah,
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
                                                    messages.join("<br>") +
                                                    "<br>";
                                            });
                                        } else if (xhr.responseJSON?.message) {
                                            // Tangkap error dari 'message' jika tidak ada 'errors'
                                            errorMessages =
                                                xhr.responseJSON.message;
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
        } else {
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
                        if (tipe == "admin") {
                            Swal.fire({
                                title: "Masukkan Dana yang Disetujui",
                                input: "number",
                                inputPlaceholder:
                                    "Masukkan Dana yang Disetujui...",
                                showCancelButton: true,
                                confirmButtonText: "Kirim",
                                cancelButtonText: "Batal",
                                preConfirm: (value) => {
                                    if (!value) {
                                        Swal.showValidationMessage(
                                            "Isian tidak boleh kosong"
                                        );
                                    }
                                    return value;
                                },
                            }).then((danaResult) => {
                                if (danaResult.isConfirmed) {
                                    showLoading("Menyimpan Data...");

                                    $.ajax({
                                        url: urlVerifikasi,
                                        type: "POST",
                                        data: {
                                            status: "1",
                                            id_hibah: id_hibah,
                                            tipe: tipe,
                                            dana_disetujui_bkui:
                                                danaResult.value,
                                            _token: csrfToken,
                                        },
                                        dataType: "json",
                                        success: (res) => {
                                            Swal.fire(
                                                "Berhasil!",
                                                res.message,
                                                "success"
                                            );
                                            window.table.ajax.reload(
                                                null,
                                                false
                                            );
                                        },
                                        error: (xhr) => {
                                            let errorMessages = "";
                                            if (xhr.responseJSON?.errors) {
                                                Object.values(
                                                    xhr.responseJSON.errors
                                                ).forEach((messages) => {
                                                    errorMessages +=
                                                        messages.join("<br>") +
                                                        "<br>";
                                                });
                                            } else if (
                                                xhr.responseJSON?.message
                                            ) {
                                                // Tangkap error dari 'message' jika tidak ada 'errors'
                                                errorMessages =
                                                    xhr.responseJSON.message;
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
                        } else {
                            // Jika klik Verifikasi → Minta input point
                            showLoading("Menyimpan data...");
                            let status = "1";
                            $.ajax({
                                url: urlVerifikasi,
                                type: "POST",
                                data: {
                                    status: "1",
                                    tipe: tipe,
                                    id_hibah: id_hibah,
                                    _token: csrfToken,
                                },
                                dataType: "json",
                                success: (res) => {
                                    Swal.fire(
                                        "Berhasil!",
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
                                    } else if (xhr.responseJSON?.message) {
                                        // Tangkap error dari 'message' jika tidak ada 'errors'
                                        errorMessages =
                                            xhr.responseJSON.message;
                                    }

                                    Swal.fire({
                                        icon: "error",
                                        title: "Gagal Menyimpan",
                                        html:
                                            errorMessages ||
                                            "Terjadi Kesalahan.",
                                    });
                                },
                            });
                        }
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
                                        id_hibah: id_hibah,
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
                                                    messages.join("<br>") +
                                                    "<br>";
                                            });
                                        } else if (xhr.responseJSON?.message) {
                                            // Tangkap error dari 'message' jika tidak ada 'errors'
                                            errorMessages =
                                                xhr.responseJSON.message;
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
                                        tipe: tipe,
                                        id_hibah: id_hibah,
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
                                                    messages.join("<br>") +
                                                    "<br>";
                                            });
                                        } else if (xhr.responseJSON?.message) {
                                            // Tangkap error dari 'message' jika tidak ada 'errors'
                                            errorMessages =
                                                xhr.responseJSON.message;
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
    }

    formVerifikasiTahap.on("submit", function (e) {
        e.preventDefault();
        let tahap = $("#modal-verifikasi-pencairan input[name='tahap']").val();
        let idLaporanHibah = $(
            "#modal-verifikasi-pencairan input[name='id_laporan_hibah']"
        ).val();
        console.log(idLaporanHibah);

        const lanjutSubmit = () => {
            let formData = new FormData(formVerifikasiTahap[0]);
            showLoading("Menyimpan data...");

            $.ajax({
                url: formVerifikasiTahap.attr("action"),
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    $("#modal-verifikasi-pencairan").modal("hide");
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: response.message,
                        timer: 1000,
                        showConfirmButton: false,
                    }).then(() => window.table.ajax.reload());
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
                            "Terjadi kesalahan tak terduga.",
                    });
                },
            });
        };

        if (tahap == 2 && idLaporanHibah == "") {
            Swal.fire({
                title: "Verifikasi Data",
                icon: "warning",
                text: "Ajuan Hibah ini belum mengisi laporan, apakah Anda ingin menyelesaikan data ini?",
                showCancelButton: true,
                confirmButtonText: "Ya, Selesaikan",
                cancelButtonText: "Batal",
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#f39c12",
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#modal-verifikasi-pencairan").modal("hide");
                    lanjutSubmit(); // lanjut ke proses simpan
                }
            });
        } else {
            lanjutSubmit(); // langsung simpan kalau tidak perlu konfirmasi
        }
    });

    formFilter.on("submit", function (e) {
        e.preventDefault();
        showLoading("Menerapkan Filter...");
        window.table.ajax.reload(function () {
            closeLoading();
        }, false);
    });

    $(".btn-download-proposal").click(function () {
        const params = {
            judul_proposal: $("#filterContent #judul_proposal").val(),
            nama_institusi: $("#filterContent #nama_institusi").val(),
            hibah: $("#filterContent #hibah").val(),
            jenis_hibah: $("#filterContent #jenis_hibah").val(),
            program_studi: $("#filterContent #program_studi").val(),
            fakultas: $("#filterContent #fakultas").val(),
            status: $("#filterContent #status").val(),
        };

        const queryString = new URLSearchParams(params).toString();

        const url = "download_excel?" + queryString;

        window.open(url, "_blank");
    });

    $(".btn-download-laporan").click(function () {
        const params = {
            judul_proposal: $("#filterContent #judul_proposal").val(),
            nama_institusi: $("#filterContent #nama_institusi").val(),
            hibah: $("#filterContent #hibah").val(),
            jenis_hibah: $("#filterContent #jenis_hibah").val(),
            program_studi: $("#filterContent #program_studi").val(),
            fakultas: $("#filterContent #fakultas").val(),
            status: $("#filterContent #status").val(),
        };

        const queryString = new URLSearchParams(params).toString();

        const url = "download_laporan_excel?" + queryString;

        window.open(url, "_blank");
    });
});
