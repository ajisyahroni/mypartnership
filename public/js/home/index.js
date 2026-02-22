function detailSkor($type, judul, tahun) {
    $("#modal-detail #DetailLabel").html(judul);
    $("#modal-detail").modal("show");

    $("#konten-detail").html(`
                    <div class="d-flex justify-content-center my-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);

    $.ajax({
        url: UrlDetailSkor,
        type: "get",
        data: {
            type: $type,
            tahun: tahun,
            _token: "{{ csrf_token() }}",
        },
        success: function (response) {
            $("#konten-detail").html(response.view);
        },
        error: function (xhr, status, error) {},
    });
}

$(document).ready(function () {
    $("#dataTable").DataTable();

    $(".btn-selengkapnya").on("click", function () {
        const tipe = $(this).data("tipe");
        const jenis = $(this).data("jenis");
        const judul = $(this).data("judul");

        $("#modal-selengkapnya #SelengkapnyaLabel").html(judul);
        $("#modal-selengkapnya").modal("show");

        $("#konten-selengkapnya").html(`
                        <div class="d-flex justify-content-center my-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `);

        $.ajax({
            url: UrldetailSelengkapnya,
            type: "get",
            data: {
                tipe: tipe,
                jenis: jenis,
            },
            success: function (response) {
                $("#konten-selengkapnya").html(response.view);
            },
            error: function (xhr, status, error) {},
        });
    });
});

const sidebar = document.querySelector(".nav-sidebar");
const container = document.querySelector(".content-area");

window.addEventListener("scroll", () => {
    // Dapatkan batas bawah dari .body-konten
    const containerBottom = container.getBoundingClientRect().bottom;

    // Jika scroll melewati bagian bawah konten
    if (containerBottom <= 500) {
        // 120px: offset toleransi (bisa sesuaikan)
        sidebar.classList.add("sticky");
    } else {
        sidebar.classList.remove("sticky");
    }
});

const links = document.querySelectorAll(".nav-sidebar .nav-link");

links.forEach((link) => {
    link.addEventListener("click", function (e) {
        e.preventDefault();

        // Hapus active di semua
        links.forEach((l) => l.classList.remove("active"));
        this.classList.add("active");

        const targetId = this.getAttribute("href").substring(1);
        const target = document.getElementById(targetId);

        if (target) {
            const headerOffset = 160; // Ubah ini sesuai tinggi navbar kamu
            const elementPosition =
                target.getBoundingClientRect().top + window.pageYOffset;
            const offsetPosition = elementPosition - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth",
            });
        }
    });
});

const allDataKategori = allDataKategoriData; // ← dari PHP controller
let currentFilter = "Universitas"; // default

document.addEventListener("DOMContentLoaded", function () {
    google.charts.load("current", {
        packages: ["corechart"],
    });
    google.charts.setOnLoadCallback(() => drawKermaCharts(currentFilter));

    // $("#filterBentukKerma").on("change", function () {
    //     currentFilter = $(this).val();
    //     drawKermaCharts(currentFilter);
    // });
});

function drawKermaCharts(filterKey) {
    const dataDN = Object.entries(
        allDataKategori[filterKey]["Dalam Negeri"]
    ).map(([label, value]) => [label, value]);
    const dataLN = Object.entries(
        allDataKategori[filterKey]["Luar Negeri"]
    ).map(([label, value]) => [label, value]);

    drawCustomChart("chart_kerma_dn", dataDN);
    drawCustomChart("chart_kerma_ln", dataLN);
}

function drawCustomChart(elementId, values) {
    const container = document.getElementById(elementId);
    if (!container) return;

    const total = values.reduce((sum, [_, jumlah]) => sum + jumlah, 0);
    const isDN = elementId.includes("dn");
    const overlay = document.querySelector(
        isDN ? ".no-data-dn" : ".no-data-ln"
    );

    if (total === 0) {
        if (overlay) overlay.classList.remove("d-none");
        container.innerHTML = "";
        return;
    } else {
        if (overlay) overlay.classList.add("d-none");
    }

    const chartData = [
        [
            "Kategori",
            "Total",
            {
                role: "style",
            },
        ],
    ];
    values.forEach(([label, jumlah]) => {
        chartData.push([label, jumlah, "#2f3185"]);
    });

    const data = google.visualization.arrayToDataTable(chartData);

    const options = {
        legend: "none",
        vAxis: {
            title: "Total",
            minValue: 0,
            format: "0",
            textStyle: {
                fontSize: 12,
            },
            titleTextStyle: {
                fontSize: 14,
                bold: true,
            },
        },
        hAxis: {
            slantedText: true,
            slantedTextAngle: 45,
            textStyle: {
                fontSize: 11,
            },
        },
        bar: {
            groupWidth: "55%",
        },
        backgroundColor: {
            fill: "transparent",
        },
        chartArea: {
            left: 50,
            right: 30,
            top: 30,
            bottom: 80,
            width: "100%",
            height: "80%",
        },
        tooltip: {
            isHtml: true,
        },
    };

    const chart = new google.visualization.ColumnChart(container);
    chart.draw(data, options);

    google.visualization.events.addListener(chart, "ready", function () {
        const bars = container.querySelectorAll("svg rect");
        bars.forEach((bar) => {
            bar.setAttribute("rx", "6");
            bar.setAttribute("ry", "6");
            bar.addEventListener(
                "mouseover",
                () => (bar.style.fill = "#5558d5")
            );
            bar.addEventListener(
                "mouseout",
                () => (bar.style.fill = "#2f3185")
            );
        });
    });
}

const allDataJenisInstitusi = allDataJenisInstitusiQuery; // ← dari PHP controller
let currentFilterInstitusi = "Universitas"; // default

document.addEventListener("DOMContentLoaded", function () {
    google.charts.load("current", {
        packages: ["corechart"],
    });
    google.charts.setOnLoadCallback(() =>
        drawInstitusiCharts(currentFilterInstitusi)
    );

    // $("#filterJenisInstitusi").on("change", function () {
    //     currentFilterInstitusi = $(this).val();
    //     drawInstitusiCharts(currentFilterInstitusi);
    // });
});

function drawInstitusiCharts(filterKey) {
    const dataDN = Object.entries(
        allDataJenisInstitusi[filterKey]["Dalam Negeri"]
    ).map(([label, value]) => [label, value]);
    const dataLN = Object.entries(
        allDataJenisInstitusi[filterKey]["Luar Negeri"]
    ).map(([label, value]) => [label, value]);

    drawCustomChartInstitusi("chart_implementasi_dn", dataDN);
    drawCustomChartInstitusi("chart_implementasi_ln", dataLN);
}

function drawCustomChartInstitusi(elementId, values) {
    const container = document.getElementById(elementId);
    if (!container) return;

    const total = values.reduce((sum, [_, jumlah]) => sum + jumlah, 0);
    const isDN = elementId.includes("dn");
    const overlay = document.querySelector(
        isDN ? ".no-data-institusi-dn" : ".no-data-institusi-ln"
    );

    if (total === 0) {
        if (overlay) overlay.classList.remove("d-none");
        container.innerHTML = "";
        return;
    } else {
        if (overlay) overlay.classList.add("d-none");
    }

    const chartData = [
        [
            "Jenis Institusi",
            "Total",
            {
                role: "style",
            },
        ],
    ];
    values.forEach(([label, jumlah]) => {
        chartData.push([label, jumlah, "#2f3185"]);
    });

    const data = google.visualization.arrayToDataTable(chartData);

    const options = {
        legend: "none",
        vAxis: {
            title: "Total",
            minValue: 0,
            format: "0",
            textStyle: {
                fontSize: 12,
            },
            titleTextStyle: {
                fontSize: 14,
                bold: true,
            },
        },
        hAxis: {
            slantedText: true,
            slantedTextAngle: 45,
            textStyle: {
                fontSize: 11,
            },
        },
        bar: {
            groupWidth: "55%",
        },
        backgroundColor: {
            fill: "transparent",
        },
        chartArea: {
            left: 50,
            right: 30,
            top: 30,
            bottom: 80,
            width: "100%",
            height: "80%",
        },
        tooltip: {
            isHtml: true,
        },
    };

    const chart = new google.visualization.ColumnChart(container);
    chart.draw(data, options);

    google.visualization.events.addListener(chart, "ready", function () {
        const bars = container.querySelectorAll("svg rect");
        bars.forEach((bar) => {
            bar.setAttribute("rx", "6");
            bar.setAttribute("ry", "6");
            bar.addEventListener(
                "mouseover",
                () => (bar.style.fill = "#5558d5")
            );
            bar.addEventListener(
                "mouseout",
                () => (bar.style.fill = "#2f3185")
            );
        });
    });
}

let chartLoaded = false;
const allDataNegara = allDataNegaraQuery;

function drawRegionsMap(dataList) {
    const chartData = [["Country", "Jumlah"]];

    if (dataList.length === 0) {
        $("#regions_div").hide().addClass("d-none");
        $(".no-data-sebaran-mitra").show().removeClass("d-none");
        return; // keluar supaya tidak lanjut render chart
    } else {
        $("#regions_div").show().removeClass("d-none");
        $(".no-data-sebaran-mitra").hide().addClass("d-none");
    }

    dataList.forEach((item) => {
        chartData.push([item.nama_negara, item.jumlah]);
    });

    const data = google.visualization.arrayToDataTable(chartData);
    const options = {
        colorAxis: {
            colors: ["#291F71", "#291F71"],
        },
        datalessRegionColor: "#ccc",
    };

    const chart = new google.visualization.GeoChart(
        document.getElementById("regions_div")
    );
    chart.draw(data, options);
}

google.charts.load("current", {
    packages: ["geochart"],
});

// const observer = new IntersectionObserver(
//     (entries, observer) => {
//         entries.forEach((entry) => {
//             if (entry.isIntersecting && !chartLoaded) {
//                 chartLoaded = true;
//                 google.charts.setOnLoadCallback(() =>
//                     drawRegionsMap(allDataNegara["Universitas"])
//                 );
//                 observer.unobserve(entry.target);
//             }
//         });
//     },
//     {
//         threshold: 0.5,
//     }
// );

$(document).ready(function () {
    const $mitraSection = $("#mitra");
    const $filterSelect = $("#filterSebaranMitra");

    if ($mitraSection.length) observer.observe($mitraSection[0]);

    // $filterSelect.on("change", function () {
    //     const key = $(this).val();
    //     drawRegionsMap(allDataNegara[key] || []);
    // });
});
