$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    const formFilter = $("#formFilterDokumen");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    let urlHapus = "/dokumen/destroy";
    let getDetailPengajuan = "/dokumen/getDetailPengajuan";

    var table = $("#dataTable").DataTable({
        paging: true,
        lengthChange: true,
        // OVERIDE:dev7777
        lengthMenu: [10, 25, 50, 75, 500, 50_000, 1e5],
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
            url: getData,
            type: "GET",
            dataType: "json",
            data: function (d) {
                d.nama_institusi = $("#filterDokumen #nama_institusi").val();
                d.dn_ln = $("#filterDokumen #dn_ln").val();
                d.tingkat_kerjasama = $(
                    "#filterDokumen #tingkat_kerjasama"
                ).val();
                d.negara_mitra = $("#filterDokumen #negara_mitra").val();
                d.wilayah_mitra = $("#filterDokumen #wilayah_mitra").val();
                d.status = $("#filterDokumen #status").val();
                d.jenis_dokumen = $("#filterDokumen #jenis_dokumen").val();
                d.jenis_institusi_mitra = $(
                    "#filterDokumen #jenis_institusi_mitra"
                ).val();
                d.lembaga_ums = $("#filterDokumen #select2_lembaga").val();
                d.tahun = $("#filterDokumen #tahun").val();
                d.status_dokumen = $("#filterDokumen #status_dokumen").val();
            },
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false, // Tetap false karena ini hanya nomor urut
                searchable: false,
            },
            {
                data: "action",
                orderable: false, // Tetap false karena ini biasanya berisi tombol aksi
                searchable: false,
            },
            {
                data: "nama_institusi",
                name: "kerma_db.nama_institusi",
                orderable: true,
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "dn_ln",
                name: "kerma_db.dn_ln",
                orderable: true,
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "wilayah_mitra",
                name: "kerma_db.wilayah_mitra",
                orderable: true,
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "status_pengajuan",
                name: "status_pengajuan",
                orderable: true,
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "jenis_kerjasama",
                name: "kerma_db.jenis_kerjasama",
                orderable: true,
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "jenis_institusi_mitra",
                name: "kerma_db.jenis_institusi",
                orderable: true,
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "prodi_unit",
                name: "kerma_db.prodi_unit",
                orderable: true,
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "lembaga",
                name: "kerma_db.status_tempat",
                orderable: true,
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "mulai",
                name: "kerma_db.mulai",
                orderable: true,
                render: (data) =>
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

    table.on("preXhr.dt", function () {
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
    table.on("xhr.dt", function () {
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
        var column = table.column(columnIndex);

        // Toggle visibility kolom
        column.visible(!column.visible());

        // Toggle warna latar belakang
        $(this).toggleClass("active-toggle");
    });

    // Tambahkan tombol export ke dalam div yang benar
    table.buttons().container().appendTo(".btn-group");

    // Event handler untuk tombol export manual
    $("#btnExcel").on("click", function () {
        table.button(".buttons-excel").trigger();
    });

    $("#btnCSV").on("click", function () {
        table.button(".buttons-csv").trigger();
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

                // $(".konten-detail").html(response.html);
                // setTimeout(function () {
                //     // $(".konten-detail").html(response.html);
                //     const fileExt = srcPdf.split(".").pop().toLowerCase();
                //     let embedContent = "";

                //     if (fileExt === "pdf") {
                //         embedContent = `<iframe src="${encodedSrcPdf}" width="100%" height="500px" style="border: none;"></iframe>`;
                //     } else if (
                //         ["png", "jpg", "jpeg", "webp"].includes(fileExt)
                //     ) {
                //         embedContent = `<div class="text-center">
                //                             <img src="${encodedSrcPdf}" class="img-fluid" alt="Preview File" style="max-height: 500px;" />
                //                         </div>`;
                //     } else {
                //         embedContent = `<div class="alert alert-warning text-center mt-3">
                //                             Format file tidak didukung untuk preview.
                //                         </div>`;
                //     }
                //     $(".konten-detail").html(response.html + embedContent);
                // }, 300);
            },
        });
    });

    $("#dataTable").on("click", ".btn-hapus", function () {
        hapus($(this).data("id_mou"));
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
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: res.message,
                                timer: 1000,
                                showConfirmButton: false,
                            }).then(() => table.ajax.reload(null, false));
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: res.message,
                                timer: 1000,
                                showConfirmButton: false,
                            }).then(() => table.ajax.reload(null, false));
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
        table.ajax.reload(function () {
            closeLoading();
        }, false);
    });

    $(".btn-download-dokumen").click(function () {
        const params = {
            nama_institusi: $("#filterDokumen #nama_institusi").val(),
            dn_ln: $("#filterDokumen #dn_ln").val(),
            tingkat_kerjasama: $("#filterDokumen #tingkat_kerjasama").val(),
            negara_mitra: $("#filterDokumen #negara_mitra").val(),
            wilayah_mitra: $("#filterDokumen #wilayah_mitra").val(),
            status: $("#filterDokumen #status").val(),
            status_verifikasi: $("#filterDokumen #status_verifikasi").val(),
            jenis_dokumen: $("#filterDokumen #jenis_dokumen").val(),
            jenis_institusi_mitra: $(
                "#filterDokumen #jenis_institusi_mitra"
            ).val(),
            lembaga_ums: $("#filterDokumen #select2_lembaga").val(),
            tahun: $("#filterDokumen #tahun").val(),
            status_dokumen: $("#filterDokumen #status_dokumen").val(),
        };

        const queryString = new URLSearchParams(params).toString();

        const url = "dokumen/download_excel?" + queryString;

        window.open(url, "_blank");
    });
});
