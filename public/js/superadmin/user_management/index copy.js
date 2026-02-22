$(document).ready(function () {
    $("#lembaga").select2({
        theme: "bootstrap-5",
        dropdownParent: $("#modal-tambah"),
    });

    $("#jabatan").select2({
        theme: "bootstrap-5",
        dropdownParent: $("#modal-tambah"),
    });

    show_data();

    function show_data() {
        $(".show-data").html(
            `<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`
        );
        $.ajax({
            url: "/user-management/show",
            type: "GET",
            success: (response) => {
                $(".show-data").html(response.html);
            },
        });
    }

    const modalTambah = $("#modal-tambah");
    const modalAssign = $("#modal-assign");
    const formInput = $("#formUser");
    const formAssign = $("#formAssign");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    let urlHapus = "/user-management/destroy";
    let urlSwitchStatus = "/user-management/switch_status";

    function openModal(data = null) {
        formInput.get(0).reset();
        $("input[name='uuid']").val(data?.uuid || "");
        $("input[name='nama']").val(data?.nama || "");
        $("input[name='username']").val(data?.username || "");
        $("input[name='email']").val(data?.email || "");
        $("input[name='password']").val(data?.password || "");
        $("select[name='lembaga']")
            .val(data?.lembaga || "")
            .trigger("change");
        $("select[name='jabatan']")
            .val(data?.jabatan || "")
            .trigger("change");
        modalTambah.modal("show");
    }

    var table = $("#dataTable").DataTable({
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        scrollX: true,
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
                data: "uuid",
                render: function (data, type, row) {
                    return `<div class="btn-group" role="group">
                                <button data-title-tooltip="Pilih Role"
                                 class="btn btn-primary btn-assign" data-id_roles="${row.id_roles}" data-uuid="${data}"><i class='bx bx-user-circle'></i></button>
                                <button data-title-tooltip="Edit User" class="btn btn-warning btn-edit" data-uuid="${data}" data-jabatan="${row.uuid_jabatan}" data-lembaga="${row.id_lembaga}" data-email="${row.email}" data-username="${row.username}" data-nama="${row.name}"><i class='bx bx-edit-alt'></i></button>
                                <button data-title-tooltip="Hapus User" class="btn btn-danger btn-hapus" data-uuid="${data}"><i class='bx bx-trash-alt'></i></button>
                            </div>`;
                },
            },
            {
                data: "name",
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "username",
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "email",
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "roles",
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "jabatan",
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${
                        data ? data : ""
                    }</span>`,
            },
            {
                data: "status_tempat",
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${
                        data ? data : ""
                    }</span>`,
            },
            {
                data: "id_lembaga",
                render: (data) =>
                    `<span class="text-dark fw-semibold mb-0">${
                        data ? data : ""
                    }</span>`,
            },
            // {
            //     data: 'delegate_by',
            //     render: (data) => `<span class="text-dark fw-semibold mb-0">${data ? data :''}</span>`
            // },
            // {
            //     data: 'delegate_by_user',
            //     render: (data) => `<span class="text-dark fw-semibold mb-0">${data ? data :''}</span>`
            // },
        ],
        createdRow: function (row, data, dataIndex) {
            $("td", row).addClass("border-bottom-0");
            $("td", row).addClass("align-top");
            if (dataIndex % 2 === 0) {
                $(row).css("background-color", "#f8f9fa");
            } else {
                $(row).css("background-color", "#ffffff");
            }
        },
    });

    $("#btn-tambah").click(() => openModal());

    $("#dataTable").on("click", ".btn-edit", function () {
        let data = {
            uuid: $(this).data("uuid"),
            nama: $(this).data("nama"),
            username: $(this).data("username"),
            email: $(this).data("email"),
            lembaga: $(this).data("lembaga"),
            jabatan: $(this).data("jabatan"),
        };
        openModal(data);
    });

    $("#dataTable").on("click", ".btn-hapus", function () {
        hapus($(this).data("uuid"));
    });

    $("#dataTable").on("change", ".switch-status", function () {
        switch_status(
            $(this).closest(".toggle-status").data("uuid"),
            $(this).prop("checked") ? 1 : 0
        );
    });

    $("#dataTable").on("click", ".btn-assign", function () {
        let uuid = $(this).data("uuid");
        let id_roles = $(this).data("id_roles");

        $("#user_uuid").val(uuid);
        $(".role-checkbox").prop("checked", false);
        id_roles.forEach((roleId) => {
            $(`#role_${roleId}`).prop("checked", true);
        });

        modalAssign.modal("show");
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
                    html:
                        errorMessages ||
                        xhr.responseJSON?.error ||
                        xhr.responseText ||
                        "Terjadi kesalahan tak terduga.",
                });
            },
        });
    });

    formAssign.on("submit", function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        showLoading("Menyimpan data...");

        $.ajax({
            url: formAssign.attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                modalAssign.modal("hide");
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message,
                    timer: 1000,
                    showConfirmButton: false,
                }).then(() => table.ajax.reload(null, false));
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

    function switch_status(uuid, status) {
        $.ajax({
            url: urlSwitchStatus,
            type: "POST",
            data: { uuid, _token: csrfToken, status },
            dataType: "json",
            success: (res) =>
                res ? toastr.success(res.message) : toastr.error(res.message),
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
    }
});
