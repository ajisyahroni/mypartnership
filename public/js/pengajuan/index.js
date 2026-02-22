$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    const modalVerifikasi = $("#modal-verifikasi");
    const formFilter = $("#formFilterPengajuan");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    let urlHapus = "/pengajuan/destroy";
    let urlPilihTTD = "/pengajuan/pilihTTD";
    let getDetailPengajuan = "/pengajuan/getDetailPengajuan";
    let getDetailVerifikasi = "/pengajuan/getDetailVerifikasi";

    window.table = $("#dataTable").DataTable({
        paging: true,
        lengthChange: true,
        // OVERIDE:dev7777
        lengthMenu: [10, 25, 50, 75, 500, 50_000, 1e5],
        searching: true,
        ordering: true,
        info: true,
        // stateSave: true,
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
                d.nama_institusi = $("#filterPengajuan #nama_institusi").val();
                d.dn_ln = $("#filterPengajuan #dn_ln").val();
                d.tingkat_kerjasama = $(
                    "#filterPengajuan #tingkat_kerjasama"
                ).val();
                d.negara_mitra = $("#filterPengajuan #negara_mitra").val();
                d.wilayah_mitra = $("#filterPengajuan #wilayah_mitra").val();
                d.status = $("#filterPengajuan #status").val();
                d.status_verifikasi = $(
                    "#filterPengajuan #status_verifikasi"
                ).val();
                d.jenis_dokumen = $("#filterPengajuan #jenis_dokumen").val();
                d.jenis_institusi_mitra = $(
                    "#filterPengajuan #jenis_institusi_mitra"
                ).val();
                d.lembaga_ums = $("#filterPengajuan #select2_lembaga").val();
                d.tahun = $("#filterPengajuan #tahun").val();
                d.stats_kerma = $("#filterPengajuan #stats_kerma").val();
                d.stats_dokumen = $("#filterPengajuan #stats_dokumen").val();
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
                data: "status_verifikasi",
                name: "kerma_db.tgl_req_ttd",
                orderable: true,
                searchable: true,
            },
            {
                data: "nama_institusi",
                orderable: true,
                searchable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "dn_ln",
                name: "dn_ln",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "wilayah_mitra",
                name: "wilayah_mitra",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "jenis_kerjasama",
                name: "jenis_kerjasama",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "jenis_institusi_mitra",
                name: "jenis_institusi",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "prodi_unit",
                name: "prodi_unit",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "lembaga",
                name: "kerma_db.status_tempat",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "mulai",
                name: "mulai",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${
                        data || ""
                    }</span>`,
            },
            {
                data: "ttd_by",
                name: "ttd_by",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "stats_kerma",
                name: "stats_kerma",
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

    // Tampilkan Swal saat loading dimulai
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
        let id_mou = $(this).data("id_mou");
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
            url: getDetailPengajuan,
            method: "GET",
            data: {
                id_mou: id_mou,
            },
            success: (response) => {
                $(".konten-detail").html(response.html);
            },
            error: (xhr) => {
                let errorMessages = "";
                if (xhr.responseJSON?.errors) {
                    Object.values(xhr.responseJSON.errors).forEach(
                        (messages) => {
                            errorMessages += messages.join("\n");
                        }
                    );
                }

                Swal.fire({
                    icon: "error",
                    title: "Gagal Menyimpan",
                    text: errorMessages || "Terjadi Kesalahan.",
                    confirmButtonColor: "#dc3545", // Warna merah jika gagal
                });
            },
        });
    });

    $("#dataTable").on("click", ".btn-verifikasi", function () {
        let id_mou = $(this).data("id_mou");
        let tipe = $(this).data("tipe");
        let status = $(this).data("status");

        modalVerifikasi.modal("show");
        $(".konten-verifikasi").html(`
            <div class="d-flex justify-content-center my-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        $.ajax({
            url: getDetailVerifikasi,
            method: "GET",
            data: {
                id_mou: id_mou,
                tipe: tipe,
                status: status,
            },
            success: (response) => {
                setTimeout(function () {
                    $(".konten-verifikasi").html(response.html);
                }, 300);
            },
            error: (xhr) => {
                let errorMessages = "";
                if (xhr.responseJSON?.errors) {
                    Object.values(xhr.responseJSON.errors).forEach(
                        (messages) => {
                            errorMessages += messages.join("\n");
                        }
                    );
                }

                Swal.fire({
                    icon: "error",
                    title: "Gagal Menyimpan",
                    text: errorMessages || "Terjadi Kesalahan.",
                    confirmButtonColor: "#dc3545", // Warna merah jika gagal
                });
            },
        });
    });

    $("#dataTable").on("click", ".btn-hapus", function () {
        hapus($(this).data("id_mou"));
    });

    // $("#dataTable").on("click", ".btn-verifikasi", function () {
    //     verifikasi(
    //         $(this).data("status"),
    //         $(this).data("tipe"),
    //         $(this).data("id_mou")
    //     );
    // });

    $("#dataTable").on("click", ".btn-ttd", function () {
        pilih_ttd($(this).data("id_mou"));
    });

    function hapus(id_mou) {
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
                    data: { id_mou, _token: csrfToken },
                    dataType: "json",
                    success: (res) => {
                        if (res.status) {
                            Swal.fire("Berhasil!", res.message, "success");
                        } else {
                            Swal.fire("Gagal!", res.message, "error");
                        }

                        setTimeout(() => {
                            window.table.ajax.reload(null, false);
                        }, 800); // jeda 0.8 detik agar swal muncul dulu
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

    function pilih_ttd(id_mou) {
        Swal.fire({
            title: "Pilih Penandatangan",
            text: "Pilih siapa yang akan menandatangani.",
            icon: "question",
            allowOutsideClick: false, // Tidak bisa ditutup dengan klik di luar
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Pengusul",
            denyButtonText: "BKUI",
            cancelButtonText: "Batal",
            confirmButtonColor: "#28a745", // Hijau untuk Pengusul
            denyButtonColor: "#0d6efd", // Biru untuk BKUI
            cancelButtonColor: "#6c757d", // Abu-abu untuk Batal
            reverseButtons: true, // Urutan tombol dibalik
        }).then((result) => {
            if (result.isConfirmed || result.isDenied) {
                showLoading("Menyimpan data...");
                let ttd = result.isConfirmed ? "Pengusul" : "BKUI";

                $.ajax({
                    url: urlPilihTTD,
                    type: "POST",
                    data: { ttd, id_mou, _token: csrfToken },
                    dataType: "json",
                    success: (res) => {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: res.message,
                            confirmButtonColor: "#28a745", // Warna hijau setelah sukses
                        });
                        window.table.ajax.reload(null, false);
                    },
                    error: (xhr) => {
                        let errorMessages = "";
                        if (xhr.responseJSON?.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(
                                (messages) => {
                                    errorMessages += messages.join("\n");
                                }
                            );
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Gagal Menyimpan",
                            text: errorMessages || "Terjadi Kesalahan.",
                            confirmButtonColor: "#dc3545", // Warna merah jika gagal
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
    });

    $(".btn-download-pengajuan").click(function () {
        const params = {
            nama_institusi: $("#filterPengajuan #nama_institusi").val(),
            dn_ln: $("#filterPengajuan #dn_ln").val(),
            tingkat_kerjasama: $("#filterPengajuan #tingkat_kerjasama").val(),
            negara_mitra: $("#filterPengajuan #negara_mitra").val(),
            wilayah_mitra: $("#filterPengajuan #wilayah_mitra").val(),
            status: $("#filterPengajuan #status").val(),
            status_verifikasi: $("#filterPengajuan #status_verifikasi").val(),
            jenis_dokumen: $("#filterPengajuan #jenis_dokumen").val(),
            jenis_institusi_mitra: $(
                "#filterPengajuan #jenis_institusi_mitra"
            ).val(),
            lembaga_ums: $("#filterPengajuan #select2_lembaga").val(),
            tahun: $("#filterPengajuan #tahun").val(),
            stats_kerma: $("#filterPengajuan #stats_kerma").val(),
        };

        const queryString = new URLSearchParams(params).toString();

        const url = "pengajuan/download_pengajuan_excel?" + queryString;

        window.open(url, "_blank");
    });
});
