$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");

    const formEditKuesioner = $("#formEditKuesioner");
    const formKirimKuesioner = $("#formKirimKuesioner");
    // let getDetailPengajuan = "/dokumen/getDetailPengajuan";

    var table = $("#dataTable").DataTable({
        paging: true,
        // stateSave: true,
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
            data: function (d) {},
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
                data: "pic_kegiatan",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "bentuk_kegiatan",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "que_title",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "que_for",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "que_create",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "status",
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

    $("#dataTable").on("click", ".btn-link_kuesioner", function () {
        let id_kuesioner = $(this).data("id_kuesioner");
        $("#modal-link").modal("show");
        $(".konten-link").html(viewLoading);

        $.ajax({
            url: getLinkKuesioner,
            type: "GET",
            data: {
                id_kuesioner: id_kuesioner,
            },
            success: function (response) {
                $(".konten-link").html(response.html);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching eksternal data:", error);
            },
        });
    });
    $("#dataTable").on("click", ".btn-kirim_kuesioner", function () {
        let id_kuesioner = $(this).data("id_kuesioner");
        $("#modal-kirim").modal("show");
        $(".konten-kirim").html(viewLoading);

        $.ajax({
            url: getKirimEmail,
            type: "GET",
            data: {
                id_kuesioner: id_kuesioner,
            },
            success: function (response) {
                $(".konten-kirim").html(response.html);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching eksternal data:", error);
            },
        });
    });

    $("#dataTable").on("click", ".btn-hasil_kuesioner", function () {
        let id_kuesioner = $(this).data("id_kuesioner");

        window.location.href = `/kuesioner/hasilKuesioner/${id_kuesioner}`;
        //
    });
    $("#dataTable").on("click", ".btn-edit_kuesioner", function () {
        let id_kuesioner = $(this).data("id_kuesioner");
        $("#modal-edit").modal("show");
        $(".konten-edit").html(viewLoading);

        $.ajax({
            url: getEditKuesioner,
            type: "GET",
            data: {
                id_kuesioner: id_kuesioner,
            },
            success: function (response) {
                $(".konten-edit").html(response.html);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching eksternal data:", error);
            },
        });
    });

    $("#dataTable").on("click", ".btn-lihat_implementasi", function () {
        let id_kuesioner = $(this).data("id_kuesioner");
        $("#modal-detail").modal("show");
        $(".konten-detail").html(viewLoading);

        $.ajax({
            url: getDetail,
            type: "GET",
            data: {
                id_kuesioner: id_kuesioner,
            },
            success: function (response) {
                $(".konten-detail").html(response.html);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching eksternal data:", error);
            },
        });
    });

    formEditKuesioner.on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        showLoading("Menyimpan data...");

        $.ajax({
            url: formEditKuesioner.attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message,
                    timer: 1000,
                    showConfirmButton: false,
                }).then(() => {
                    $("#modal-edit").modal("hide");
                    table.ajax.reload();
                });
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

    formKirimKuesioner.on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        showLoading("Mengirim Email...");

        $.ajax({
            url: formKirimKuesioner.attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message,
                    timer: 1000,
                    showConfirmButton: false,
                }).then(() => {
                    $("#modal-kirim").modal("hide");
                    table.ajax.reload();
                });
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

                // Swal.fire({
                //     icon: "error",
                //     title: "Gagal Menyimpan",
                //     html: errorMessages || "Terjadi Kesalahan.",
                // });
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

    // $("#dataTable").on("click", ".btn-hapus", function () {
    //     hapus($(this).data("id_mou"));
    // });
});
