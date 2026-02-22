$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    let urlSendReminder = "/reminder/send-reminder";
    let urlSendReminderBroadcast = "/reminder/send-broadcast";

    $("#dataTableKerma").DataTable();
    $("#dataTableProduktif").DataTable();
    $("#dataTableExpired").DataTable();

    // var columns = table
    //     .columns()
    //     .header()
    //     .toArray()
    //     .map((th) => $(th).text());

    // columns.forEach((colName, i) => {
    //     $("#columnToggleList").append(`
    // <li class="dropdown-item toggle-item" data-column="${i}" style="cursor: pointer;background: linear-gradient(135deg, #007bff, #0056b3);color:white;">
    //    <b>${colName}</b>
    // </li>
    //     `);
    // });

    // Event untuk Show/Hide Kolom & Toggle Warna
    // $(document).on("click", ".toggle-item", function () {
    //     var columnIndex = $(this).data("column");
    //     var column = table.column(columnIndex);

    //     // Toggle visibility kolom
    //     column.visible(!column.visible());

    //     // Toggle warna latar belakang
    //     $(this).toggleClass("active-toggle");
    // });

    // Tambahkan tombol export ke dalam div yang benar
    // table.buttons().container().appendTo(".btn-group");

    // Event handler untuk tombol export manual
    // $("#btnExcel").on("click", function () {
    //     table.button(".buttons-excel").trigger();
    // });

    // $("#btnCSV").on("click", function () {
    //     table.button(".buttons-csv").trigger();
    // });

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
    //             setTimeout(function () {
    //                 // $(".konten-detail").html(response.html);
    //                 const fileExt = srcPdf.split(".").pop().toLowerCase();
    //                 let embedContent = "";

    //                 if (fileExt === "pdf") {
    //                     embedContent = `<iframe src="${encodedSrcPdf}" width="100%" height="500px" style="border: none;"></iframe>`;
    //                 } else if (
    //                     ["png", "jpg", "jpeg", "webp"].includes(fileExt)
    //                 ) {
    //                     embedContent = `<div class="text-center">
    //                                         <img src="${encodedSrcPdf}" class="img-fluid" alt="Preview File" style="max-height: 500px;" />
    //                                     </div>`;
    //                 } else {
    //                     embedContent = `<div class="alert alert-warning text-center mt-3">
    //                                         Format file tidak didukung untuk preview.
    //                                     </div>`;
    //                 }
    //                 $(".konten-detail").html(response.html + embedContent);
    //             }, 300);
    //         },
    //     });
    // });

    // $("#dataTable").on("click", ".btn-hapus", function () {
    //     hapus($(this).data("id_mou"));
    // });

    $("#dataTableExpired, #dataTableProduktif, #dataTableKerma").on(
        "click",
        ".btn-reminder",
        function () {
            reminder($(this).data("id_mou"), $(this).data("tipe"));
        }
    );

    $(".btn-broadcast").on("click", function () {
        broadcast($(this).data("tipe"));
    });

    function reminder(id_mou, tipe) {
        let title = "Kirim Pengingat";

        Swal.fire({
            text: title,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya Kirim",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading("Mengirim data...");
                $.ajax({
                    url: urlSendReminder,
                    type: "POST",
                    data: { id_mou: id_mou, tipe: tipe, _token: csrfToken },
                    dataType: "json",
                    success: (res) => {
                        if (res.status) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: res.message,
                                timer: 1000,
                                showConfirmButton: false,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Gagal!", res.message, "error");
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

    function broadcast(tipe) {
        let title = "Kirim Pengingat";

        Swal.fire({
            text: title,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya Kirim",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading("Mengirim data...");
                $.ajax({
                    url: urlSendReminderBroadcast,
                    type: "POST",
                    data: { tipe: tipe, _token: csrfToken },
                    dataType: "json",
                    success: (res) => {
                        if (res.status) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: response.message,
                                timer: 1000,
                                showConfirmButton: false,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Info!", res.message, "info");
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
