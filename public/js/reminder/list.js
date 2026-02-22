$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    // let urlHapus = "/reminder/destroy";
    // let urlVerifikasi = "/reminder/verifikasi";
    // let urlPilihTTD = "/reminder/pilihTTD";
    let getDetailPengajuan = "/pengajuan/getDetailPengajuan";

    var table = $("#dataTable").DataTable({
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
                // d.nama_institusi = $("#filterPengajuan #nama_institusi").val();
                // d.dn_ln = $("#filterPengajuan #dn_ln").val();
                // d.tingkat_kerjasama = $(
                //     "#filterPengajuan #tingkat_kerjasama"
                // ).val();
                // d.negara_mitra = $("#filterPengajuan #negara_mitra").val();
                // d.wilayah_mitra = $("#filterPengajuan #wilayah_mitra").val();
                // d.status = $("#filterPengajuan #status").val();
                // d.status_verifikasi = $(
                //     "#filterPengajuan #status_verifikasi"
                // ).val();
                // d.jenis_dokumen = $("#filterPengajuan #jenis_dokumen").val();
                // d.jenis_institusi_mitra = $(
                //     "#filterPengajuan #jenis_institusi_mitra"
                // ).val();
                // d.lembaga_ums = $("#filterPengajuan #select2_lembaga").val();
                // d.tahun = $("#filterPengajuan #tahun").val();
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
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "wilayah_mitra",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "prodi_unit",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "jenis_kerjasama",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "jenis_institusi_mitra",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            { data: "status_pengajuan", orderable: false, searchable: false },
            {
                data: "masa_berlaku",
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
                setTimeout(function () {
                    // $(".konten-detail").html(response.html);
                    const fileExt = srcPdf.split(".").pop().toLowerCase();
                    let embedContent = "";

                    if (fileExt === "pdf") {
                        embedContent = `<iframe src="${encodedSrcPdf}" width="100%" height="500px" style="border: none;"></iframe>`;
                    } else if (
                        ["png", "jpg", "jpeg", "webp"].includes(fileExt)
                    ) {
                        embedContent = `<div class="text-center">
                                            <img src="${encodedSrcPdf}" class="img-fluid" alt="Preview File" style="max-height: 500px;" />
                                        </div>`;
                    } else {
                        embedContent = `<div class="alert alert-warning text-center mt-3">
                                            Format file tidak didukung untuk preview.
                                        </div>`;
                    }
                    $(".konten-detail").html(response.html + embedContent);
                }, 300);
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
});
