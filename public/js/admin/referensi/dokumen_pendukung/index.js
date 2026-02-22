$(document).ready(function () {
    const modalForm = $("#modal-form");
    const formInput = $("#formInput");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    let urlHapus = "/dokumenPendukung/destroy";

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
                modalForm.modal("hide");
                if (response.status) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: response.message,
                        timer: 1000,
                        showConfirmButton: false,
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal Menyimpan",
                        text: response.message,
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

    function openModal(data = null) {
        formInput.get(0).reset();

        $("input[name='uuid']").val(data?.uuid || "");
        $("input[name='nama_dokumen']").val(data?.nama_dokumen || "");
        $("input[name='link_dokumen']").val(data?.link_dokumen || "");
        let fileInput = $("#fileInput");
        let fileNamesDiv = $("#fileNames");

        fileInput.val("");
        fileNamesDiv.empty().hide();

        modalForm.modal("show");
    }

    $("#btnTambah").click(() => openModal());

    $("#customAccordion").on("click", ".btn-edit", function () {
        let data = {
            uuid: $(this).data("uuid"),
            nama_dokumen: $(this).data("nama_dokumen"),
            link_dokumen: $(this).data("link_dokumen"),
        };
        openModal(data);
    });

    $("#customAccordion").on("click", ".btn-hapus", function () {
        hapus($(this).data("uuid"));
    });

    $(document).on("change", ".check-status", function () {
        var checkbox = $(this);
        var id = checkbox.data("id");
        var isActive = checkbox.is(":checked") ? 1 : 0;

        $.ajax({
            url: UrlsetDokumen,
            method: "POST",
            data: {
                id: id,
                is_active: isActive,
                _token: $('meta[name="csrf-token"]').attr("content"), // jika butuh token
            },
            success: function (response) {
                toastr.success(response.message);
            },
            error: function (xhr) {
                let errorMessages = "";
                if (xhr.responseJSON?.errors) {
                    Object.values(xhr.responseJSON.errors).forEach(function (
                        messages
                    ) {
                        errorMessages += messages.join("<br>") + "<br>";
                    });
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
                        Swal.fire("Berhasil!", res.message, "success");
                        window.location.reload();
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

$(document).ready(function () {
    loadAllDokumenIframe();

    function loadAllDokumenIframe() {
        $(".iframe-container").each(function () {
            $(this).find(".loading-spinner").show();
        });

        $.ajax({
            url: "/dokumenPendukung/dokumen-pendukung/load-all-iframe",
            method: "GET",
            dataType: "json",
            beforeSend: function () {
                console.log("Memuat semua dokumen...");
            },
            success: function (response) {
                if (response.success && response.data) {
                    response.data.forEach(function (item) {
                        const container = $(
                            `.iframe-container[data-uuid="${item.uuid}"]`
                        );

                        if (container.length) {
                            const loadingSpinner =
                                container.find(".loading-spinner");

                            loadingSpinner.fadeOut(300, function () {
                                container.html(item.html);
                                container.data("loaded", true);

                                const iframe = container.find("iframe");
                                if (iframe.length) {
                                    iframe.css("opacity", "0");

                                    iframe.on("load", function () {
                                        $(this).animate({ opacity: 1 }, 400);
                                    });

                                    iframe.on("error", function () {
                                        handleIframeError($(this));
                                    });
                                }
                            });
                        }
                    });

                    console.log(`Berhasil memuat ${response.total} dokumen`);
                } else {
                    showGlobalError("Gagal memuat dokumen");
                }
            },
            error: function (xhr, status, error) {
                let errorMessage = "Gagal memuat dokumen. Silakan coba lagi.";

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                showGlobalError(errorMessage);
                console.error("Error:", errorMessage);
            },
            timeout: 60000,
        });
    }

    function showGlobalError(message) {
        $(".iframe-container").each(function () {
            const container = $(this);
            const errorHtml = `
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bx bx-error-circle me-2" style="font-size: 24px;"></i>
                    <div>
                        <strong>Error!</strong> ${message}
                    </div>
                </div>
            `;

            container.find(".loading-spinner").fadeOut(200, function () {
                container.html(errorHtml);
            });
        });

        const retryButton = `
            <div class="text-center my-3">
                <button class="btn btn-primary retry-load-all">
                    <i class="bx bx-refresh me-1"></i> Muat Ulang Semua Dokumen
                </button>
            </div>
        `;

        if (!$(".retry-load-all").length) {
            $(".accordion").before(retryButton);
        }
    }

    $(document).on("click", ".retry-load-all", function () {
        $(this).closest("div").remove();

        $(".iframe-container").each(function () {
            $(this).data("loaded", false);
            $(this).html(`
                <div class="loading-spinner text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted fw-medium">Memuat dokumen...</p>
                </div>
            `);
        });

        loadAllDokumenIframe();
    });

    window.handleIframeError = function (iframeElement) {
        const container = iframeElement.closest(".iframe-container");
        const uuid = container.data("uuid");

        const errorHtml = `
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bx bx-error-circle me-2" style="font-size: 20px;"></i>
                <div>
                    <strong>Gagal memuat preview dokumen.</strong><br>
                    <small>Silakan download file untuk melihat dokumen.</small>
                </div>
            </div>
        `;

        container.html(errorHtml);
    };
});
