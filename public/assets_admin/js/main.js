/**
 * Main
 */

"use strict";

let menu, animate;

(function () {
    // Initialize menu
    //-----------------

    let layoutMenuEl = document.querySelectorAll("#layout-menu");
    layoutMenuEl.forEach(function (element) {
        menu = new Menu(element, {
            orientation: "vertical",
            closeChildren: false,
        });
        // Change parameter to true if you want scroll animation
        window.Helpers.scrollToActive((animate = false));
        window.Helpers.mainMenu = menu;
    });

    // Initialize menu togglers and bind click on each
    let menuToggler = document.querySelectorAll(".layout-menu-toggle");
    menuToggler.forEach((item) => {
        item.addEventListener("click", (event) => {
            event.preventDefault();
            window.Helpers.toggleCollapsed();
        });
    });

    // Display menu toggle (layout-menu-toggle) on hover with delay
    let delay = function (elem, callback) {
        let timeout = null;
        elem.onmouseenter = function () {
            // Set timeout to be a timer which will invoke callback after 300ms (not for small screen)
            if (!Helpers.isSmallScreen()) {
                timeout = setTimeout(callback, 300);
            } else {
                timeout = setTimeout(callback, 0);
            }
        };

        elem.onmouseleave = function () {
            // Clear any timers set to timeout
            document
                .querySelector(".layout-menu-toggle")
                .classList.remove("d-block");
            clearTimeout(timeout);
        };
    };
    if (document.getElementById("layout-menu")) {
        delay(document.getElementById("layout-menu"), function () {
            // not for small screen
            if (!Helpers.isSmallScreen()) {
                document
                    .querySelector(".layout-menu-toggle")
                    .classList.add("d-block");
            }
        });
    }

    // Display in main menu when menu scrolls
    let menuInnerContainer = document.getElementsByClassName("menu-inner"),
        menuInnerShadow =
            document.getElementsByClassName("menu-inner-shadow")[0];
    if (menuInnerContainer.length > 0 && menuInnerShadow) {
        menuInnerContainer[0].addEventListener("ps-scroll-y", function () {
            if (this.querySelector(".ps__thumb-y").offsetTop) {
                menuInnerShadow.style.display = "block";
            } else {
                menuInnerShadow.style.display = "none";
            }
        });
    }

    // Init helpers & misc
    // --------------------

    // Init BS Tooltip
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Accordion active class
    const accordionActiveFunction = function (e) {
        if (e.type == "show.bs.collapse" || e.type == "show.bs.collapse") {
            e.target.closest(".accordion-item").classList.add("active");
        } else {
            e.target.closest(".accordion-item").classList.remove("active");
        }
    };

    const accordionTriggerList = [].slice.call(
        document.querySelectorAll(".accordion")
    );
    const accordionList = accordionTriggerList.map(function (
        accordionTriggerEl
    ) {
        accordionTriggerEl.addEventListener(
            "show.bs.collapse",
            accordionActiveFunction
        );
        accordionTriggerEl.addEventListener(
            "hide.bs.collapse",
            accordionActiveFunction
        );
    });

    // Auto update layout based on screen size
    window.Helpers.setAutoUpdate(true);

    // Toggle Password Visibility
    window.Helpers.initPasswordToggle();

    // Speech To Text
    window.Helpers.initSpeechToText();

    // Manage menu expanded/collapsed with templateCustomizer & local storage
    //------------------------------------------------------------------

    // If current layout is horizontal OR current window screen is small (overlay menu) than return from here
    if (window.Helpers.isSmallScreen()) {
        return;
    }

    // If current layout is vertical and current window screen is > small

    // Auto update menu collapsed/expanded based on the themeConfig
    window.Helpers.setCollapsed(true, false);
})();

$(document).on("keyup", ".isRupiahs", function () {
    let value = this.value.replace(/[^0-9]/g, "");
    this.value = formatRupiah(value);
});

function formatRupiah(angka, prefix) {
    let number_string = angka.replace(/[^,\d]/g, "").toString(),
        split = number_string.split(","),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }

    rupiah = split[1] !== undefined ? rupiah + "," + split[1] : rupiah;
    return prefix === undefined ? rupiah : rupiah ? "Rp" + rupiah : "";
}

// Initialize value on page load
$(document).ready(function () {
    $(".isRupiah").each(function () {
        let value = this.value.replace(/[^0-9]/g, "");
        this.value = formatRupiah(value);
    });

    $(".select2").select2({
        theme: "bootstrap-5",
    });

    $("#nama_institusi , #select2_lembaga").select2({
        theme: "bootstrap-5",
        minimumInputLength: 3,
        language: {
            inputTooShort: function (args) {
                return "Masukkan minimal 3 karakter";
            },
        },
    });
});

function Rupiah(angka) {
    var reverse = angka.toString().split("").reverse().join("");
    var ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join(".").split("").reverse().join("");
    return ribuan;
}

toastr.options = {
    closeButton: true,
    newestOnTop: false,
    progressBar: true,
    // "positionClass": "toast-bottom-center",
    preventDuplicates: false,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
};

$(document).ready(function () {
    $(".InputContainer").on("click", function () {
        $(".InputContainer .input").focus();
    });
});

$(document).ready(function () {
    // if (menuSession == "mypartnership") {
    //     // getNotifikasi();
    // } else if (menuSession == "hibah") {
    //     getNotifikasiHibah();
    // } else if (menuSession == "recognition") {
    //     getNotifikasiRecognition();
    // } else if (menuSession == "partner") {
    //     getNotifikasiPartner();
    // }
    flatpickr(".datepicker-custom", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        locale: "id",
        allowInput: true,
    });

    $("#sidebarSearch").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#sidebarnav .sidebar-item").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
        $("#MenuNavMobile .sidebar-item").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    $(".isNumber").on("input", function () {
        let value = $(this).val();
        value = value.replace(/[^0-9,]/g, ""); // Hanya angka dan koma yang diperbolehkan
        $(this).val(value);
    });

    $(".role-switch").click(function (e) {
        e.preventDefault();
        let selectedRole = $(this).data("role");

        Swal.fire({
            title: "Berganti Role...",
            html: "Tunggu Sebentar...",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        $.ajax({
            url: "/set-role",
            type: "POST",
            data: { role: selectedRole, _token: csrftoken },
            success: function (response) {
                if (response.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: "Role berganti ke " + selectedRole,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        if (response.redirect_url) {
                            window.location.href = response.redirect_url; // Redirect ke URL yang diberikan
                        } else {
                            location.reload(); // Jika tidak ada redirect, reload halaman
                        }
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Kamu tidak memiliki ijin dalam Role ini.",
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Terdapat Kesalahan.",
                });
            },
        });
    });

    $(".menu-switch").click(function (e) {
        e.preventDefault();
        let selectedMenu = $(this).data("menu");

        Swal.fire({
            title: "Berganti Menu...",
            html: "Tunggu Sebentar...",
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        $.ajax({
            url: "/set-menu",
            type: "POST",
            data: { menu: selectedMenu, _token: csrftoken },
            success: function (response) {
                if (selectedMenu == "mypartnership") {
                    var MenuTerpilih = "Kerja Sama";
                } else if (selectedMenu == "recognition") {
                    var MenuTerpilih = "Rekognisi Dosen";
                } else if (selectedMenu == "partner") {
                    var MenuTerpilih = "Mitra Potensial";
                } else if (selectedMenu == "hibah") {
                    var MenuTerpilih = "Hibah" + " Kerja Sama";
                }

                if (response.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: "Menu berganti ke " + MenuTerpilih,
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        if (response.redirect_url) {
                            window.location.href = response.redirect_url; // Redirect ke URL yang diberikan
                        } else {
                            location.reload(); // Jika tidak ada redirect, reload halaman
                        }
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Kamu tidak memiliki ijin dalam Role ini.",
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Terdapat Kesalahan.",
                });
            },
        });
    });

    $(document).ready(function () {
        $(".btn-detail-log").on("click", function () {
            const dataLog = $(this).data("log");

            $("#modal-detail-log").modal("show");
            $("#content-detail-log").html(`
                        <div id="loading">
                            <div class="d-flex justify-content-center my-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    `);

            let logDraft = "";

            dataLog.forEach((dt) => {
                logDraft += `
                <div class="alert d-flex align-items-start p-3 mb-3"
                    role="alert"
                    style="background-color: #e6faff; border-left: 6px solid #00bcd4;">
                    
                    <div class="me-3 text-white d-flex align-items-center justify-content-center"
                        style="background-color: #00bcd4; width: 30px; height: 30px; border-radius: 4px;">
                        <i class="bx bx-info-circle"></i>
                    </div>

                    <div class="text-dark small">
                        <strong>Diunggah oleh:</strong> ${dt.pengupload}<br>
                        <strong>Tanggal:</strong> ${dateFormatter(
                            dt.created_at
                        )}
                    </div>
                </div>
            `;
            });

            $("#content-detail-log").html(logDraft);
        });
    });
});

function last_seen() {
    fetch("/last-seen")
        .then((response) => response.json())
        .then((data) => {
            data.forEach((user) => {
                let statusElement = document.querySelector(
                    "#user-" + user.id + " .status"
                );
                if (statusElement) {
                    statusElement.innerHTML = user.is_online
                        ? "🟢 Online"
                        : "🔴 Offline";
                }
            });
        })
        .catch((error) =>
            console.error("Error fetching last seen data:", error)
        );
}

// function getNotifikasi() {
//     $.ajax({
//         url: "/getDataNotifikasi",
//         type: "get",
//         dataType: "json",
//         success: function (response) {
//             $("#notifPengajuanKerjaSama").html(response.pengajuan_kerja_sama);
//             $("#notifPengajuanKerjaSamaMobile").html(
//                 response.pengajuan_kerja_sama_mobile
//             );
//             $("#notifImplementasi").html(response.pengajuan_implementasi);
//             $("#notifImplementasiMobile").html(
//                 response.pengajuan_implementasi_mobile
//             );
//             $("#notifMenuKerjaSamaMobile").html(
//                 response.menu_kerja_sama_mobile
//             );
//             $("#notifMenuKerjaSama").html(response.menu_kerja_sama);
//         },
//     });
// }

// function getNotifikasiHibah() {
//     $.ajax({
//         url: "/getDataNotifikasiHibah",
//         type: "get",
//         dataType: "json",
//         success: function (response) {
//             $("#notifHibah").html(response.hibah);
//             $("#notifMenuHibah").html(response.menu_hibah);
//             $("#notifHibahMobile").html(response.hibah_mobile);

//             if (response.dataUser == true) {
//                 $("#modal-detail-notif #content-detail-notif-hibah").html(
//                     response.dataModal
//                 );
//                 $("#modal-detail-notif").modal("show");
//             }
//         },
//     });
// }

// function getNotifikasiRecognition() {
//     $.ajax({
//         url: "/getDataNotifikasiRecognition",
//         type: "get",
//         dataType: "json",
//         success: function (response) {
//             if (RoleGlobal == "admin") {
//                 $("#notifRecognition").html(response.rekognisi_admin);
//             } else if (RoleGlobal == "verifikator") {
//                 $("#notifRecognition").html(response.rekognisi_verifikator);
//             } else if (RoleGlobal == "user") {
//                 $("#notifRecognitionUser").html(response.rekognisi_user);
//             }
//             // $("#notifMenuRecognition").html(response.menu_rekognisi);
//             $("#notifRecognitionMobile").html(response.rekognisi_mobile);

//             if (response.dataUser == true) {
//                 $("#modal-detail-notif #content-detail-notif-rekognisi").html(
//                     response.dataModal
//                 );
//                 $("#modal-detail-notif").modal("show");
//             }
//         },
//     });
// }

// function getNotifikasiPartner() {
//     $.ajax({
//         url: "/getNotifikasiPartner",
//         type: "get",
//         dataType: "json",
//         success: function (response) {
//             if (RoleGlobal == "admin") {
//                 $("#notifPartner").html(response.partner_admin);
//                 $("#notifPartnerMobile").html(response.partner_mobile);
//             } else {
//                 $("#notifPartner").html(response.partner_user);
//                 $("#notifPartnerMobile").html(response.partner_user_mobile);
//             }

//             // if (response.dataUser == true) {
//             //     $("#modal-detail-notif #content-detail-notif-partner").html(
//             //         response.dataModal
//             //     );
//             //     $("#modal-detail-notif").modal("show");
//             // }
//         },
//     });
// }

function showLoading(message) {
    Swal.fire({
        title: "Proses...",
        text: message,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });
}

function closeLoading() {
    Swal.close(); // Menutup SweetAlert2 loading modal
}

function dateFormatter(tanggal) {
    const formatter = new Intl.DateTimeFormat("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
    });

    return formatter.format(new Date(tanggal));
}

var viewLoading = `<div id="loading" class="d-flex flex-column justify-content-center align-items-center py-5">
                        <i class="bx bx-loader bx-spin text-primary mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted fs-5" style="font-size:10px;">Sedang memuat data, mohon tunggu...</p>
                    </div>`;
