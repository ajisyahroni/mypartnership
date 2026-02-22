<div id="tren-grafis" class="content-section p-3">
    <div class="container">
        <div class="d-block d-sm-flex justify-content-between">
            <div class="col-12 col-md-6 mb-2">
                <div class="title-bar"></div>
                <h3 class="title-dashboard">Grafik Kerja Sama</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="ranking-card p-3 text-center">
                    <span class="title-dashboard">Kerja Sama Produktif</span>
                    <div id="lineChartKerjasamaProduktif" class="mt-2" style="height: 400px;"></div>
                    <div class="no-data-tren-kerma-produktif d-none text-center text-muted"
                        style="width: 100%; height: 400px; display: flex; align-items: center; justify-content: center; background-color: rgba(255,255,255,0.9); z-index: 5;">
                        Tidak ada data untuk tren kerja sama produktif.
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="ranking-card p-3 text-center">
                    <span class="title-dashboard">Dokumen Kerja Sama</span>
                    <div id="lineChartKerjasama" class="mt-2" style="height: 400px;"></div>
                    <div class="no-data-tren-kerma d-none text-center text-muted"
                        style="width: 100%; height: 400px; display: flex; align-items: center; justify-content: center; background-color: rgba(255,255,255,0.9); z-index: 5;">
                        Tidak ada data untuk tren kerja sama.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let dataTrenGrafis = @json($trenGrafis);
        let categoriesTahun = dataTrenGrafis.map(item => item.tahun);

        // Data
        let dataKerjasama = {
            all: dataTrenGrafis.map(item => parseInt(item.total, 10) || 0),
            dn: dataTrenGrafis.map(item => parseInt(item.dn, 10) || 0),
            ln: dataTrenGrafis.map(item => parseInt(item.ln, 10) || 0)
        };

        if (dataTrenGrafis.length === 0) {
            // Kalau tidak ada data, tampilkan no-data
            $("#lineChartKerjasama").hide();
            $(".no-data-tren-kerma").removeClass("d-none");
        } else {
            // Kalau ada data, render chart
            Highcharts.chart('lineChartKerjasama', {
                chart: {
                    type: 'column'
                },
                title: null,
                xAxis: {
                    categories: categoriesTahun,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Jumlah Kerja Sama'
                    }
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true
                },
                tooltip: {
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                        name: "Semua",
                        data: dataKerjasama.all,
                        color: '#007bff'
                    },
                    {
                        name: "Dalam Negeri",
                        data: dataKerjasama.dn,
                        color: '#28a745'
                    },
                    {
                        name: "Luar Negeri",
                        data: dataKerjasama.ln,
                        color: '#dc3545'
                    }
                ]
            });
        }
    });

    $(document).ready(function() {
        let dataTrenGrafisProduktif = @json($trenGrafisProduktif);
        let categoriesTahunProduktif = dataTrenGrafisProduktif.map(item => item.tahun);

        // Data
        let dataKerjasamaProduktif = {
            all: dataTrenGrafisProduktif.map(item => parseInt(item.total, 10) || 0),
            dn: dataTrenGrafisProduktif.map(item => parseInt(item.dn, 10) || 0),
            ln: dataTrenGrafisProduktif.map(item => parseInt(item.ln, 10) || 0)
        };

        if (dataTrenGrafisProduktif.length === 0) {
            // Kalau tidak ada data, tampilkan no-data
            $("#lineChartKerjasamaProduktif").hide();
            $(".no-data-tren-kerma-produktif").removeClass("d-none");
        } else {
            // Kalau ada data, render chart
            Highcharts.chart('lineChartKerjasamaProduktif', {
                chart: {
                    type: 'column'
                },
                title: null,
                xAxis: {
                    categories: categoriesTahunProduktif,
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Jumlah Kerja Sama'
                    }
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true
                },
                tooltip: {
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                        name: "Semua",
                        data: dataKerjasamaProduktif.all,
                        color: '#007bff'
                    },
                    {
                        name: "Dalam Negeri",
                        data: dataKerjasamaProduktif.dn,
                        color: '#28a745'
                    },
                    {
                        name: "Luar Negeri",
                        data: dataKerjasamaProduktif.ln,
                        color: '#dc3545'
                    }
                ]
            });
        }
    });
</script>
