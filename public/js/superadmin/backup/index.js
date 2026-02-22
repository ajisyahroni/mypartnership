$(document).ready(function () {
    const modalTambah = $("#modal-tambah");
    const formInput = $("#formInput");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    let urlBackup = "/backup/backup";
    let urlDownload = "/backup/downloadBackup";
    let urlHapus = "/backup/destroy";

    var table = $("#dataTable").DataTable({
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        responsive: true,
        language: {
            search: "Pencarian:",
            searchPlaceholder: "Cari Data...",
        },
        serverSide: true,
        processing: true,
        ajax: {
            url: getData,
            type: "GET",
            dataType: "json",
        },
        columns: [
            {
                data: "DT_RowIndex",
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "filename",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "type",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "size",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "tanggal",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "status",
                render: function (data, type, row) {
                    let result = ``;
                    if (data == "1") {
                        result += `<span class="badge badge-sm bg-success">Berhasil</span>`;
                    } else {
                        result += `<span class="badge badge-sm bg-danger">Gagal</span><br>`;
                        result += `<span class="badge badge-sm bg-warning" style="font-size:10px;">${row.message}</span>`;
                    }

                    return result;
                },
            },
            {
                data: "uuid",
                render: function (data, type, row) {
                    let result = `<div class="btn-group" role="group">`;

                    result += `<button class="btn btn-success btn-download" data-uuid="${data}"><i class='bx bx-download'></i></button>`;
                    result += `<button class="btn btn-danger btn-hapus" data-uuid="${data}"><i class='bx bx-trash-alt'></i></button>`;

                    result += `</div>`;

                    return result;
                },
            },
        ],
        createdRow: function (row, data, dataIndex) {
            $("td", row).addClass("border-bottom-0");
            $("td", row).addClass("align-top");
            // Menambahkan efek zebra striping dengan warna yang lebih soft
            if (dataIndex % 2 === 0) {
                $(row).css("background-color", "#f8f9fa"); // Warna lebih soft untuk baris genap
            } else {
                $(row).css("background-color", "#ffffff"); // Warna putih untuk baris ganjil
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

    $("#btn-backupDatabase").click(function () {
        backup("Database");
    });

    $("#btn-backupFiles").click(function () {
        backup("Files");
    });

    $("#dataTable").on("click", ".btn-download", function () {
        download($(this).data("uuid"));
    });

    $("#dataTable").on("click", ".btn-hapus", function () {
        hapus($(this).data("uuid"));
    });

    formInput.on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        showLoading("Menyimpan data...");

        $.ajax({
            url: formInput.attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                modalTambah.modal("hide");
                if (response.status) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: response.message,
                        timer: 1000,
                        showConfirmButton: false,
                    }).then(() => table.ajax.reload(null, false));
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: response.message,
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
                            errorMessages += messages.join("<br>") + "<br>";
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
    });

    function backup(tipe) {
        Swal.fire({
            text: `Anda yakin ingin memulai Back Up ${tipe}?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Backup",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading("Membackup data...");
                $.ajax({
                    url: urlBackup + tipe,
                    type: "GET",
                    data: { _token: csrfToken },
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

    function download(uuid) {
        Swal.fire({
            text: "Anda yakin ingin mendownload data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Dowload",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading("Mendownload data...");
                $.ajax({
                    url: urlDownload,
                    type: "GET",
                    data: { uuid, _token: csrfToken },
                    dataType: "json",
                    success: (res) => {
                        window.location.href = `${res.urlDownload}`;
                        // Swal.fire("Berhasil!", res.message, "success");
                        Swal.close();
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

    function hapus(uuid) {
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
                    data: { uuid, _token: csrfToken },
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
