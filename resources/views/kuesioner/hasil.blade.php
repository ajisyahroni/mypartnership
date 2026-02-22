@extends('layouts.app')

@section('contents')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="me-2">
                                        <i class="fa-solid fa-folder-open text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                                <a href="{{ route('kuesioner.home') }}" class="btn btn-danger btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="accordion custom-accordion" id="customAccordion">

                                <!-- Detail Dokumen Kerja Sama -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne" aria-expanded="true">
                                            <i class="fa-solid fa-building me-2"></i> Nama Institusi
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <table class="table table-bordered table-striped"
                                                style="font-size:12px;color: black;">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Institusi</th>
                                                        <th>Masa Kemitraan dengan UMS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($qpartner as $row)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                <span class="fw-bold">{{ $row->qinstitution }}</span>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $period = $row->qperiod;
                                                                    $badgeClass = match ($period) {
                                                                        '< 2 Years' => 'bg-primary',
                                                                        '2 - 4 Year' => 'bg-success',
                                                                        '4 - 6 Year' => 'bg-warning',
                                                                        '> 6 Years' => 'bg-danger',
                                                                        default => 'bg-secondary',
                                                                    };
                                                                @endphp

                                                                <span class="badge {{ $badgeClass }}"
                                                                    style="font-size: 10px!important;"
                                                                    data-title-tooltip="{{ $period }}">
                                                                    {{ $period }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse2" aria-expanded="true">
                                            <i class="fa-solid fa-calendar me-2"></i> Masa Kemitraan
                                        </button>
                                    </h2>
                                    <div id="collapse2" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <table class="table table-bordered table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td>Universiti Malaysia Sabah</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Universiti Teknologi MARA (Centre for Dietetics St)</td>
                                                    </tr>
                                                    <tr>
                                                        <td>National Dong Hwa University</td>
                                                    </tr>
                                                    <tr>
                                                        <td>4</td>
                                                        <td>Universiti Malaysia Sabah</td>
                                                    </tr>
                                                    <tr>
                                                        <td>5</td>
                                                        <td>University Tunn Hussein Onn Malaysia</td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                            <ul class="list-unstyled">
                                                <li><strong>
                                                        < 2 Years</strong>
                                                </li>
                                                <li><strong>> 6 Years</strong></li>
                                                <li><strong>4 - 6 Year</strong></li>
                                                <li><strong>2 - 4 Year</strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse3" aria-expanded="true">
                                            <i class="fa-solid fa-handshake me-2"></i> Jenis Kerja Sama dengan UMS
                                        </button>
                                    </h2>
                                    <div id="collapse3" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <div id="chartKerma" style="width: 100%; height: 500px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse4" aria-expanded="true">
                                            <i class="fa-solid fa-question-circle me-2"></i> Pertanyaan
                                        </button>
                                    </h2>
                                    <div id="collapse4" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <div id="partnerSurveyChart" style="width:100%; height:500px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 310px;
            max-width: 800px;
            margin: 1em auto;
        }

        /* #container {
                                                                                                            height: 400px;
                                                                                                        } */

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
    </style>
@endsection

@push('scripts')
    <script>
        Highcharts.chart('chartKerma', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Kategori Kerja Sama',
                align: 'center'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            tooltip: {
                pointFormat: 'Jumlah: <b>{point.y}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    },
                    showInLegend: true
                }
            },
            exporting: {
                enabled: true
            },
            credits: {
                enabled: false
            },
            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                data: @json($kategoriChart)
            }]
        });
    </script>

    <script>
        Highcharts.chart('partnerSurveyChart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Partner Satisfaction Survey'
            },
            xAxis: {
                categories: @json($surveyCategories),
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Number of Respondents',
                    align: 'high'
                }
            },
            tooltip: {
                valueSuffix: ' respondents'
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    },
                }
            },
            legend: {
                reversed: true,
                align: 'center',
                verticalAlign: 'bottom',
                layout: 'horizontal'
            },
            exporting: {
                enabled: true
            },
            credits: {
                enabled: false
            },
            colors: ['#f03e3e', '#fab005', '#69db7c', '#339af0', '#845ef7'],
            series: {!! $SurveyChart !!}
        });
    </script>

    {{-- <script src="{{ asset('js/kuesioner/index.js') }}"></script> --}}
@endpush
