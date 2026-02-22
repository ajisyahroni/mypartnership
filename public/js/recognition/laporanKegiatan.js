$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");

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
                d.id_fakultas = IdFakultas;
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
                data: "acceptance_form",
            },
            {
                data: "cv_prof",
            },
            {
                data: "file_sk",
            },
            {
                data: "bukti_pelaksanaan",
            },
            {
                data: "add_by",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "faculty",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "nama_prof",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "univ_asal",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "bidang_kepakaran",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "timestamp_ajuan",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "timestamp_selesai",
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
            const fileExt = srcPdf.split(".").pop().toLowerCase();
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
});
