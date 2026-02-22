$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    const formInput = $("#formInput");
    const urlHapus = "/potential_partner/destroy";
    let urlVerifikasi = "/potential_partner/verifikasi";

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
            data: function (d) {},
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            }, // Tambahkan nomor urut
            {
                data: "name",
                name: "tbl_prospect_partner.name",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "email",
                name: "tbl_prospect_partner.email",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "phonenumber",
                name: "tbl_prospect_partner.phonenumber",
                orderable: true,
                visible: role === "admin" ? true : false,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "researchint",
                name: "tbl_prospect_partner.researchint",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "occupation",
                name: "tbl_prospect_partner.occupation",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "institution",
                name: "tbl_prospect_partner.institution",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "country_name",
                name: "ref_countries.name",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "website",
                name: "tbl_prospect_partner.website",
                orderable: true,
                render: (data, type, row) =>
                    `<span class="text-dark fw-semibold mb-0">${data}</span>`,
            },
            {
                data: "status_label",
                name: "tbl_prospect_partner.status",
                orderable: true,
            },

            { data: "action", orderable: false, searchable: false },
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

    // $("#dataTable").on("click", ".btn-lihat-file", function () {
    //     let id_rec = $(this).data("id_rec");
    //     let title = $(this).data("title");
    //     let srcPdf = $(this).data("url");
    //     let encodedSrcPdf = encodeURI(srcPdf);

    //     $("#modal-lihat-file #DetailLabel").html(title);
    //     $("#modal-lihat-file").modal("show");

    //     $("#konten-detail").html(`
    //         <div class="d-flex justify-content-center my-3">
    //             <div class="spinner-border text-primary" role="status">
    //                 <span class="visually-hidden">Loading...</span>
    //             </div>
    //         </div>
    //     `);

    //     setTimeout(function () {
    //         const fileExt = srcPdf.split(".").pop().toLowerCase();
    //         let embedContent = "";

    //         if (fileExt === "pdf") {
    //             embedContent = `<iframe src="${encodedSrcPdf}" width="100%" height="500px" style="border: none;"></iframe>`;
    //         } else if (["png", "jpg", "jpeg", "webp"].includes(fileExt)) {
    //             embedContent = `<div class="text-center">
    //                                 <img src="${encodedSrcPdf}" class="img-fluid" alt="Preview File" style="max-height: 500px;" />
    //                             </div>`;
    //         } else {
    //             embedContent = `<div class="alert alert-warning text-center mt-3">
    //                                 Format file tidak didukung untuk preview.
    //                             </div>`;
    //         }

    //         $("#konten-detail").html(embedContent);
    //     }, 300);
    // });

    $("#dataTable").on("click", ".btn-view-partner", function () {
        let name = $(this).data("name");
        let email = $(this).data("email");
        let occupation = $(this).data("occupation");
        let phonenumber = $(this).data("phonenumber");
        let socmed = $(this).data("socmed");
        let researchint = $(this).data("researchint");
        let institution = $(this).data("institution");
        let country_name = $(this).data("country_name");
        let website = $(this).data("website");
        let address = $(this).data("address");
        let cardname1 = $(this).data("cardname1");
        let urlcardname1 = $(this).data("url-cardname1");
        let cardname2 = $(this).data("cardname2");
        let urlcardname2 = $(this).data("url-cardname2");
        console.log(urlcardname1);
        console.log(urlcardname2);

        $("#modal-view-partner").modal("show");

        $("#modal-view-partner #loading").show();
        $("#modal-view-partner #content-view-partner").hide();

        $("#modal-view-partner #name").text(name);
        $("#modal-view-partner #email").text(email);
        $("#modal-view-partner #occupation").text(occupation);
        $("#modal-view-partner #phonenumber").text(phonenumber);
        $("#modal-view-partner #socmed").text(socmed);
        $("#modal-view-partner #researchint").text(researchint);
        $("#modal-view-partner #institution").text(institution);
        $("#modal-view-partner #country_name").text(country_name);
        $("#modal-view-partner #website").text(website);
        $("#modal-view-partner #address").text(address);

        $("#modal-view-partner #cardname1show").attr("src", urlcardname1);
        $("#modal-view-partner #cardname2show").attr("src", urlcardname2);

        if (cardname1) {
            $("#modal-view-partner #cardname1show").show();
            $("#modal-view-partner #cardname1").hide();
        } else {
            $("#modal-view-partner #cardname1show").hide();
            $("#modal-view-partner #cardname1").show();
        }

        if (cardname2) {
            $("#modal-view-partner #cardname2show").show();
            $("#modal-view-partner #cardname2").hide();
        } else {
            $("#modal-view-partner #cardname2show").hide();
            $("#modal-view-partner #cardname2").show();
        }

        $("#modal-view-partner #loading").hide();
        $("#modal-view-partner #content-view-partner").show();
    });

    $("#dataTable").on("click", ".btn-hapus", function () {
        hapus($(this).data("id"));
    });

    $("#dataTable").on("click", ".btn-verify", function () {
        verifikasi($(this).data("status"), $(this).data("id"));
    });

    function hapus(id) {
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
                    data: { id, _token: csrfToken },
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

    function verifikasi(status, id) {
        let title = "";
        let button = "";
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
                    // Jika klik Verifikasi → Minta input point
                    showLoading("Menyimpan data...");

                    $.ajax({
                        url: urlVerifikasi,
                        type: "POST",
                        data: {
                            status: "verifikasi",
                            id,
                            _token: csrfToken,
                        },
                        dataType: "json",
                        success: (res) => {
                            Swal.fire("Berhasil!", res.message, "success");
                            table.ajax.reload(null, false);
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
                                    status: "revisi",
                                    id,
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
                                    table.ajax.reload(null, false);
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
                                    status: "revisi",
                                    id,
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
                                    table.ajax.reload(null, false);
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

        // Swal.fire({
        //     title: title,
        //     text: "Masukkan nilai verifikasi:",
        //     icon: "warning",
        //     input: "number",
        //     inputAttributes: {
        //         min: 1,
        //         step: 1,
        //     },
        //     inputPlaceholder: "Masukkan point...",
        //     showCancelButton: true,
        //     confirmButtonColor: "#3085d6",
        //     cancelButtonColor: "#d33",
        //     confirmButtonText: "Ya, " + button,
        //     cancelButtonText: "Batal",
        //     preConfirm: (inputValue) => {
        //         if (!inputValue) {
        //             Swal.showValidationMessage("Input tidak boleh kosong");
        //         }
        //         return inputValue;
        //     },
        // }).then((result) => {
        //     if (result.isConfirmed) {
        //         showLoading(button + " data...");

        //         $.ajax({
        //             url: urlVerifikasi,
        //             type: "POST",
        //             data: {
        //                 status,
        //                 id,
        //                 nilai_input: result.value, // nilai dari input number
        //                 _token: csrfToken,
        //             },
        //             dataType: "json",
        //             success: (res) => {
        //                 Swal.fire("Berhasil!", res.message, "success");
        //                 table.ajax.reload(null, false);
        //             },
        //             error: (xhr) => {
        //                 let errorMessages = "";
        //                 if (xhr.responseJSON?.errors) {
        //                     Object.values(xhr.responseJSON.errors).forEach(
        //                         (messages) => {
        //                             errorMessages +=
        //                                 messages.join("<br>") + "<br>";
        //                         }
        //                     );
        //                 }

        //                 Swal.fire({
        //                     icon: "error",
        //                     title: "Gagal Menyimpan",
        //                     html: errorMessages || "Terjadi Kesalahan.",
        //                 });
        //             },
        //         });
        //     }
        // });
    }

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
                // Swal.fire({
                //     icon: "success",
                //     title: "Berhasil!",
                //     text: response.message,
                //     timer: 1000,
                //     showConfirmButton: false,
                // }).then(() => (window.location.href = response.route));
                if (response.status) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: response.message,
                        timer: 1000,
                        showConfirmButton: false,
                    }).then(() => {
                        if (response.route) {
                            window.location.href = response.route;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: response.message,
                        timer: 1000,
                        showConfirmButton: false,
                    });
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

    $(".btn-download-excel").click(function () {
        // const params = {
        //     id_fakultas: IdFakultas,
        // };

        // const queryString = new URLSearchParams(params).toString();
        // const urlDownload = "download_excel?" + queryString;
        const urlDownload = "download_excel?";
        // const urlDownload = "download_excel_detail?";

        window.open(urlDownload, "_blank");
    });
});
