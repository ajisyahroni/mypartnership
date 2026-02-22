$(document).ready(function () {
    let csrfToken = $('meta[name="csrf-token"]').attr("content");

    const modalEdit = $("#modal-edit");
    const formInput = $("#formInput");

    function openModal(data = null) {
        formInput.get(0).reset();
        $("input[name='id_setting']").val(data?.id_setting || "");
        $("input[name='host']").val(data?.host || "");
        $("input[name='port']").val(data?.port || "");
        $("input[name='user']").val(data?.user || "");
        $("input[name='pass']").val(data?.pass || "");
        $("input[name='email']").val(data?.email || "");
        $("input[name='nama']").val(data?.nama || "");
        $("input[name='reply_to']").val(data?.reply_to || "");
        $("input[name='subjek_reply_to']").val(data?.subjek_reply_to || "");
        $("textarea[name='email_receiver']").val(data?.email_receiver || "");
        modalEdit.modal("show");
    }

    window.switchStatus = function (id_setting, status) {
        $.ajax({
            url: urlSwitchStatus,
            type: "post",
            data: {
                id_setting: id_setting,
                _token: csrfToken,
                status: status,
            },
            dataType: "json",
            success: function (res) {
                if (res) {
                    toastr.success(res.message);
                    table.ajax.reload(null, false);
                } else {
                    toastr.error(res.message);
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal Menyimpan",
                    text: xhr.responseJSON?.message || "Terjadi Kesalahan.",
                });
            },
        });
    };

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
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            {
                data: "host",
                render: (data, type, row) =>
                    `<span class="text-dark mb-0">${data}</span>`,
            },
            {
                data: "port",
                render: (data, type, row) =>
                    `<span class="text-dark mb-0">${data}</span>`,
            },
            {
                data: "user",
                render: (data, type, row) =>
                    `<span class="text-dark mb-0">${data}</span>`,
            },
            {
                data: "nama",
                render: (data, type, row) =>
                    `<span class="text-dark mb-0">${data}</span>`,
            },
            {
                data: "email_receiver",
                render: (data, type, row) =>
                    `<span class="text-dark mb-0">${data}</span>`,
            },
            {
                data: "subjek_reply_to",
                render: (data, type, row) =>
                    `<span class="text-dark mb-0">${data}</span>`,
            },
            {
                data: "id_setting",
                render: (data, type, row) => {
                    let result = "";
                    result += `<div class="toggle-button-cover">
                                    <div id="button-3" class="button r">
                                        <input class="checkbox" onclick="switchStatus('${data}','${
                        row.is_active == "1" ? "0" : "1"
                    }')" type="checkbox" ${
                        row.is_active == "1" ? "checked" : "0"
                    }>
                                        <div class="knobs"></div>
                                        <div class="layer"></div>
                                    </div>
                                </div>`;

                    return result;
                },
            },
            {
                data: "action",
            },
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

    $("#dataTable").on("click", ".btn-edit", function () {
        let data = {
            id_setting: $(this).data("id_setting"),
            host: $(this).data("host"),
            port: $(this).data("port"),
            user: $(this).data("user"),
            pass: $(this).data("pass"),
            email: $(this).data("email"),
            nama: $(this).data("nama"),
            reply_to: $(this).data("reply_to"),
            subjek_reply_to: $(this).data("subjek_reply_to"),
            email_receiver: $(this).data("email_receiver"),
        };
        openModal(data);
    });

    $("#dataTable").on("change", ".switch-status", function () {
        switchStatus(
            $(this).closest(".toggle-status").data("uuid"),
            $(this).prop("checked") ? 1 : 0
        );
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
                modalEdit.modal("hide");
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
});
