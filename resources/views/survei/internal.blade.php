<style>
    .chart-container {
        width: 100%;
        overflow-x: hidden;
    }

    .chart-container>div {
        min-width: 100%;
        /* height: 400px; */
    }

    /* Media Query Optional untuk kontrol ukuran */
    @media (max-width: 768px) {
        .chart-container>div {
            height: 400px;
        }
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid var(--highcharts-neutral-color-10, #e6e6e6);
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: var(--highcharts-neutral-color-60, #666);
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tbody tr:nth-child(even) {
        background: var(--highcharts-neutral-color-3, #f7f7f7);
    }

    .highcharts-description {
        margin: 0.3rem 10px;
    }

    text.highcharts-title {
        font-size: 12px !important;
    }
</style>

<div class="accordion custom-accordion" id="customAccordion">
    @foreach ($dataJenis as $jenis)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse{{ str_replace(' ', '', $jenis) }}" aria-expanded="true">
                    <i
                        class="fa-solid {{ $jenis == 'Kerja Sama' ? 'fa-file-signature' : ($jenis == 'Implementasi' ? 'fa-diagram-project' : ($jenis == 'Rekognisi' ? 'fa-user-graduate' : 'fa-hand-holding-dollar')) }} me-2"></i>
                    Survei {{ $jenis }}
                </button>
            </h2>
            <div id="collapse{{ str_replace(' ', '', $jenis) }}" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="chart-container">
                                <div class="row d-flex justify-content-center">
                                    @foreach ($DataSurvei as $chartItem)
                                        @if ($chartItem['jenis'] == $jenis)
                                            <div class="col-12 col-md-4 mb-2">
                                                <div class="ms-2">
                                                    <div id="{{ $chartItem['id_chart'] }}"
                                                        style="height: 300px;width:100%;"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    <div class="col-12">
                                        <button type="button" class="btn btn-purple mt-3 btnMasukan w-100"
                                            data-jenis="{{ $jenis }}">
                                            <i class="fa-solid fa-comments me-2"></i> Lihat Masukan Saran
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="modal fade" id="modal-detail" aria-labelledby="DetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DetailLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="konten-detail">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<script>
    const DataSurvei = @json($DataSurvei);
    const defaultJawaban = ['Sangat Tidak Puas', 'Tidak Puas', 'Puas', 'Sangat Puas'];
    const colorMap = {
        'Sangat Tidak Puas': '#dc3545',
        'Tidak Puas': '#ffc107',
        'Puas': '#74c0fc',
        'Sangat Puas': '#007bff'
    };

    DataSurvei.forEach(chartItem => {
        const series = defaultJawaban.map(j => ({
            name: j, // tampilkan nama kategori sebagai legend
            data: [chartItem.data[j] || 0],
            color: colorMap[j]
        }));

        Highcharts.chart(chartItem.id_chart, {
            chart: {
                type: 'column'
            },
            title: {
                text: chartItem.judul
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: [''], // hanya satu kolom per chart (kosong agar tidak tampil apa-apa)
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah Responden'
                }
            },
            tooltip: {
                shared: true,
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>'
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true
                    },
                    grouping: true
                }
            },
            legend: {
                enabled: true
            },
            series: series
        });
    });


    // $(".btnMasukan").on('click', function() {
    //     var jenis = $(this).data('jenis');
    //     var urlgetMasukan = @json(route('survei.getMasukan'));
    //     var jenisUcwords = jenis.replace(/\b\w/g, c => c.toUpperCase());

    //     $("#DetailLabel").html("Masukan Saran " + jenisUcwords);
    //     $("#modal-detail").modal("show");
    //     $("#konten-detail").html(`
    //         <div id="loading" class="d-flex flex-column justify-content-center align-items-center py-5">
    //             <i class="bx bx-loader bx-spin text-primary mb-3" style="font-size: 3rem;"></i>
    //             <p class="text-muted fs-5">Sedang memuat data, mohon tunggu...</p>
    //         </div>
    //     `);

    //     $.ajax({
    //         url: urlgetMasukan,
    //         type: "get",
    //         data: {
    //             jenis
    //         },
    //         success: function(response) {
    //             $("#konten-detail").html(response.tabel_masukan);
    //         },
    //         error: function(xhr, status, error) {
    //             $("#konten-detail").html(`<p class="text-danger">Gagal memuat data.</p>`);
    //         }
    //     });
    // });
</script>


{{-- <script>
    const DataSurvei = @json($DataSurvei);
    const arrPertanyaanKerma = @json($arrPertanyaanKerma);
    // const defaultJawaban = ["Sangat Tidak Puas", "Tidak Puas", "Puas", "Sangat Puas"];
    const defaultJawaban = ['Sangat Tidak Puas', 'Tidak Puas', 'Puas', 'Sangat Puas'];
    const colorMap = {
        'Sangat Tidak Puas': '#dc3545',
        'Tidak Puas': '#ffc107',
        'Puas': '#74c0fc',
        'Sangat Puas': '#007bff'
    };
    // const kategoriRespon = ["Sangat Tidak Puas", "Tidak Puas", "Puas", "Sangat Puas"];
</script>

<script>
    DataSurvei.forEach(chartItem => {
        const pertanyaanList = Object.keys(chartItem.data); // ambil nama-nama pertanyaan

        // Buat series untuk masing-masing kategori jawaban
        const series = defaultJawaban.map(jawaban => ({
            name: jawaban,
            color: colorMap[jawaban],
            data: pertanyaanList.map(pertanyaan => {
                const val = chartItem.data?.[pertanyaan]?.[jawaban];
                return typeof val === 'number' ? val : 0;
            })
        }));

        Highcharts.chart(chartItem.id_chart, {
            chart: {
                type: 'column',
                zoomType: 'xy',
                scrollablePlotArea: {
                    minWidth: 700,
                    scrollPositionX: 0
                }
            },
            credits: {
                enabled: false
            },
            title: {
                text: chartItem.judul
            },
            xAxis: {
                categories: pertanyaanList,
                crosshair: true,
                accessibility: {
                    description: 'Pertanyaan'
                },
                scrollbar: {
                    enabled: pertanyaanList.length > 4 // aktifkan scrollbar jika banyak pertanyaan
                },
                min: 0,
                max: Math.min(4, pertanyaanList.length - 1) // tampilkan sebagian dulu
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah Responden'
                }
            },
            tooltip: {
                shared: true,
                valueSuffix: ' Responden'
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: series
        });
    });
</script> --}}


<script>
    $(".btnMasukan").on('click', function() {
        var jenis = $(this).data('jenis');
        var urlgetMasukan = @json(route('survei.getMasukan'));
        var jenisUcwords = jenis.replace(/\b\w/g, function(c) {
            return c.toUpperCase();
        });

        $("#modal-detail #DetailLabel").html("Masukan Saran " + jenisUcwords);
        $("#modal-detail").modal("show");

        $("#konten-detail").html(`
                     <div id="loading" class="d-flex flex-column justify-content-center align-items-center py-5">
                        <i class="bx bx-loader bx-spin text-primary mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted fs-5">Sedang memuat data, mohon tunggu...</p>
                    </div>
                `);

        $.ajax({
            url: urlgetMasukan,
            type: "get",
            data: {
                jenis: jenis,
            },
            success: function(response) {
                $("#konten-detail").html(response.tabel_masukan);
            },
            error: function(xhr, status, error) {},
        });
    })
</script>
