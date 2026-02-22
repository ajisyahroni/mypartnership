$(document).ready(function () {
    if (menuSession == "mypartnership") {
        getNotifikasi();
    } else if (menuSession == "hibah") {
        getNotifikasiHibah();
    } else if (menuSession == "recognition") {
        getNotifikasiRecognition();
    } else if (menuSession == "partner") {
        getNotifikasiPartner();
    }
});

function getNotifikasi() {
    $.ajax({
        url: "/getDataNotifikasi",
        type: "get",
        dataType: "json",
        success: function (response) {
            $("#notifPengajuanKerjaSama").html(response.pengajuan_kerja_sama);
            $("#notifPengajuanKerjaSamaMobile").html(
                response.pengajuan_kerja_sama_mobile
            );
            $("#notifImplementasi").html(response.pengajuan_implementasi);
            $("#notifImplementasiMobile").html(
                response.pengajuan_implementasi_mobile
            );
            $("#notifMenuKerjaSamaMobile").html(
                response.menu_kerja_sama_mobile
            );
            $("#notifMenuKerjaSama").html(response.menu_kerja_sama);
        },
    });
}

function getNotifikasiHibah() {
    $.ajax({
        url: "/getDataNotifikasiHibah",
        type: "get",
        dataType: "json",
        success: function (response) {
            $("#notifHibah").html(response.hibah);
            $("#notifMenuHibah").html(response.menu_hibah);
            $("#notifHibahMobile").html(response.hibah_mobile);

            if (response.dataUser == true) {
                $("#modal-detail-notif #content-detail-notif-hibah").html(
                    response.dataModal
                );
                $("#modal-detail-notif").modal("show");
            }
        },
    });
}

function getNotifikasiRecognition() {
    $.ajax({
        url: "/getDataNotifikasiRecognition",
        type: "get",
        dataType: "json",
        success: function (response) {
            if (RoleGlobal == "admin") {
                $("#notifRecognition").html(response.rekognisi_admin);
            } else if (RoleGlobal == "verifikator") {
                $("#notifRecognition").html(response.rekognisi_verifikator);
            } else if (RoleGlobal == "user") {
                $("#notifRecognitionUser").html(response.rekognisi_user);
            }
            // $("#notifMenuRecognition").html(response.menu_rekognisi);
            $("#notifRecognitionMobile").html(response.rekognisi_mobile);

            if (response.dataUser == true) {
                $("#modal-detail-notif #content-detail-notif-rekognisi").html(
                    response.dataModal
                );
                $("#modal-detail-notif").modal("show");
            }
        },
    });
}

function getNotifikasiPartner() {
    $.ajax({
        url: "/getNotifikasiPartner",
        type: "get",
        dataType: "json",
        success: function (response) {
            if (RoleGlobal == "admin") {
                $("#notifPartner").html(response.partner_admin);
                $("#notifPartnerMobile").html(response.partner_mobile);
            } else {
                $("#notifPartner").html(response.partner_user);
                $("#notifPartnerMobile").html(response.partner_user_mobile);
            }

            // if (response.dataUser == true) {
            //     $("#modal-detail-notif #content-detail-notif-partner").html(
            //         response.dataModal
            //     );
            //     $("#modal-detail-notif").modal("show");
            // }
        },
    });
}
