$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    const formFilter = $("#formFilterImplementasi");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    let urlHapus = "/implementasi/destroy";
    let getDetailImplementasi = "/implementasi/getDetailImplementasi";
    let urlVerifikasi = "/implementasi/verifikasi";

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
                d.category = $("#filterImplementasi #category").val();
                d.nama_institusi = $(
                    "#filterImplementasi #nama_institusi"
                ).val();
                d.pelaksana = $("#filterImplementasi #pelaksana").val();
                d.judul = $("#filterImplementasi #judul").val();
                d.postby = $("#filterImplementasi #postby").val();
                d.tahun = $("#filterImplementasi #tahun").val();
                d.status = $("#filterImplementasi #status").val();
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
                data: "category",
                name: "category",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "nama_institusi",
                name: "kerma_db.nama_institusi",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "tingkat_kerjasama",
                name: "tingkat_kerjasama",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "pelaksana_prodi_unit",
                name: "pelaksana_prodi_unit",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "judul",
                name: "judul",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "bentuk_kegiatan",
                name: "bentuk_kegiatan",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "bukti_pelaksanaan",
                name: "file_imp",
                class: "text-center",
                orderable: true,
            },
            {
                data: "dokumen_kerjasama",
                name: "kerma_db.file_mou",
                class: "text-center",
                orderable: true,
            },
            {
                data: "lapor_kerma",
                name: "file_ikuenam",
                class: "text-center",
                orderable: true,
            },
            {
                data: "tahun_berakhir",
                name: "kerma_db.mulai",
                orderable: true,
            },
            {
                data: "status_verifikasi",
                name: "status_verifikasi",
                orderable: true,
            },
            {
                data: "pelapor",
                name: "pelapor",
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

    window.tableGroup = $("#dataTable-group").DataTable({
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
            url: getDataGroup, // URL endpoint untuk mengambil data
            type: "GET",
            dataType: "json",
            data: function (d) {
                d.category = $("#filterImplementasi #category").val();
                d.nama_institusi = $(
                    "#filterImplementasi #nama_institusi"
                ).val();
                d.pelaksana = $("#filterImplementasi #pelaksana").val();
                d.judul = $("#filterImplementasi #judul").val();
                d.postby = $("#filterImplementasi #postby").val();
                d.tahun = $("#filterImplementasi #tahun").val();
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
                data: "nama_institusi",
                orderable: true,
                searchable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "tingkat_kerjasama",
                orderable: true,
                searchable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "jenis_institusi_mitra",
                orderable: true,
                searchable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "total_laporan",
                orderable: true,
                searchable: false,
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
        let id_ev = $(this).data("id_ev");
        let srcPdf = $(this).data("srcpdf");
        let encodedSrcPdf = encodeURI(srcPdf);

        modalDetail.modal("show");
        $(".konten-detail").html(`
            <div class="d-flex justify-content-center my-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        $.ajax({
            url: getDetailImplementasi,
            method: "GET",
            data: {
                id_ev: id_ev,
            },
            success: (response) => {
                $(".konten-detail").html(response.html);
            },
        });
    });

    $("#dataTable").on("click", ".btn-hapus", function () {
        hapus($(this).data("id_ev"));
    });

    $("#dataTable").on("click", ".btn-verifikasi", function () {
        verifikasi(
            $(this).data("status"),
            $(this).data("tipe"),
            $(this).data("id_ev")
        );
    });

    $("#dataTable-group").on("click", ".btn-show-detail", function () {
        var tr = $(this).closest("tr");
        var row = window.tableGroup.row(tr);
        var nama_institusi = $(this).data("nama_institusi");

        if (row.child.isShown()) {
            // Sudah terbuka, tutup
            row.child.hide();
            tr.removeClass("shown");
        } else {
            tr.addClass("shown");

            // Tampilkan sementara loading
            row.child(
                '<div style="padding:10px;text-align:center;">Loading...</div>'
            ).show();

            // Lalu ambil datanya
            $.get(
                "implementasi/lapor-implementasi/detail/" + nama_institusi,
                function (data) {
                    var html = data;
                    row.child(html).show();
                }
            );
        }
    });

    function hapus(id_ev) {
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
                    data: { id_ev, _token: csrfToken },
                    dataType: "json",
                    success: (res) => {
                        if (res.status) {
                            Swal.fire("Berhasil!", res.message, "success");
                        } else {
                            Swal.fire("Gagal!", res.message, "error");
                        }
                        setTimeout(() => {
                            window.table.ajax.reload(null, false);
                            window.tableGroup.ajax.reload(null, false);
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

    function verifikasi(status, tipe, id_ev) {
        let title = "";
        let button = "";
        if (status == "1") {
            title += "Anda yakin ingin verifikasi data ini?";
            button += "Verifikasi";
        } else {
            button += "Batalkan";
            title += "Anda yakin ingin membatalkan verifikasi data ini?";
        }

        Swal.fire({
            text: title,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, " + button,
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading(button + " data...");
                $.ajax({
                    url: urlVerifikasi,
                    type: "POST",
                    data: { status, id_ev, tipe, _token: csrfToken },
                    dataType: "json",
                    success: (res) => {
                        if (res.status) {
                            Swal.fire("Berhasil!", res.message, "success");
                            setTimeout(() => {
                                window.table.ajax.reload(null, false);
                                window.tableGroup.ajax.reload(null, false);
                                $.ajax({
                                    url: implementasiSendEmail,
                                    method: "POST",
                                    data: {
                                        _token: csrfToken,
                                    },
                                    success: function () {
                                        console.log("Email berhasil dikirim.");
                                    },
                                    error: function () {
                                        console.log("Email gagal dikirim.");
                                    },
                                });
                            }, 800);
                        } else {
                            Swal.fire("Gagal!", res.message, "error");
                            setTimeout(() => {
                                window.table.ajax.reload(null, false);
                                window.tableGroup.ajax.reload(null, false);
                            }, 800);
                        }
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

    formFilter.on("submit", function (e) {
        e.preventDefault();
        showLoading("Menerapkan Filter...");
        window.table.ajax.reload(function () {
            closeLoading();
        }, false);
        window.tableGroup.ajax.reload(function () {
            closeLoading();
        }, false);
    });

    $(".btn-download-implementasi").click(function () {
        const params = {
            category: $("#filterImplementasi #category").val(),
            nama_institusi: $("#filterImplementasi #nama_institusi").val(),
            pelaksana: $("#filterImplementasi #pelaksana").val(),
            judul: $("#filterImplementasi #judul").val(),
            postby: $("#filterImplementasi #postby").val(),
            tahun: $("#filterImplementasi #tahun").val(),
            status: $("#filterImplementasi #status").val(),
        };

        const queryString = new URLSearchParams(params).toString();

        const url = "implementasi/download_implementasi_excel?" + queryString;

        window.open(url, "_blank");
    });
});
