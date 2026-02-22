$(document).ready(function () {
    const modalDetail = $("#modal-detail");
    let csrfToken = $('meta[name="csrf-token"]').attr("content");
    // let getDetailPengajuan = "/dokumen/getDetailPengajuan";
    DataInternal();
    DataEksternal();

    function DataInternal() {
        $(".konten-internal").html(`
                <div id="loading" class="d-flex flex-column justify-content-center align-items-center py-5">
                    <i class="bx bx-loader bx-spin text-primary mb-3" style="font-size: 3rem;"></i>
                    <p class="text-muted fs-5">Sedang memuat data, mohon tunggu...</p>
                </div>
            `);

        $.ajax({
            url: getDataInternal,
            type: "GET",
            success: function (response) {
                $(".konten-internal").html(response.internal);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching internal data:", error);
            },
        });
    }
    function DataEksternal() {
        $(".konten-eksternal").html(`
            <div id="loading" class="d-flex flex-column justify-content-center align-items-center py-5">
                <i class="bx bx-loader bx-spin text-primary mb-3" style="font-size: 3rem;"></i>
                <p class="text-muted fs-5">Sedang memuat data, mohon tunggu...</p>
            </div>
        `);

        $.ajax({
            url: getDataEksternal,
            type: "GET",
            success: function (response) {
                $(".konten-eksternal").html(response.eksternal);
            },
            error: function (xhr, status, error) {
                console.error("Error fetching eksternal data:", error);
            },
        });
    }
});
