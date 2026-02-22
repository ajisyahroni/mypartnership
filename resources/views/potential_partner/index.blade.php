@extends('layouts.app')

@section('contents')
    <style>
        .icon-circle {
            width: 80px;
            height: 80px;
            border: 2px solid #3f51b5;
            /* Warna biru */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    <div class="page-body">
        <div class="container-fluid">
            <div class="content pt-4">
                <!-- Card Line 1 -->
                <div class="row">
                    <!-- Skor Instansi Card -->
                    <div class="col-xl-3 col-md-6 col-12 mb-4">
                        {{-- Tombol Tambah --}}
                        <a href="{{ route('potential_partner.tambah') }}"
                            class="btn btn-success w-100 mb-3 rounded-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-plus me-2"></i> Tambah Mitra Potensial
                        </a>
                        <div class="card shadow-sm border-0 rounded-4 text-center">
                            <div class="card-header bg-primary text-white rounded-top-4">
                                <h6 class="mb-0 text-light d-flex justify-content-center align-items-center">
                                    <i class="fas fa-trophy me-2"></i> Reward Points
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="icon-circle mx-auto mb-3">
                                    <i class='bx bx-id-card text-primary' style="font-size:30px!important"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">{{ $rewardPoint }}</h4>
                                <p class="text-muted mb-0">Reward Points</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 col-6 mb-4">
                        {{-- <button class="btn btn-primary">Add Prospecive Partner</button> --}}
                        <div class="card shadow-sm border-0 rounded-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="bx bx-medal me-1"></i> Hall of Fame
                                </h6>
                            </div>
                            <style>
                                .medal-number {
                                    width: 36px;
                                    height: 36px;
                                    border-radius: 50%;
                                    font-weight: bold;
                                    font-size: 16px;
                                    color: #222;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    box-shadow: inset -2px -2px 3px rgba(0, 0, 0, 0.2), inset 2px 2px 3px rgba(255, 255, 255, 0.2);
                                    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.4);
                                }

                                /* GOLD: Sesuai warna tengah medal di gambar */
                                .medal-gold {
                                    background: radial-gradient(circle at 30% 30%, #f9d976, #cfa400);
                                    border: 2px solid #b88a00;
                                }

                                /* SILVER: Gradasi ke arah metalik abu */
                                .medal-silver {
                                    background: radial-gradient(circle at 30% 30%, #e6e8ea, #9e9e9e);
                                    border: 2px solid #888;
                                }

                                /* BRONZE: Warna coklat tembaga dengan nuansa merah */
                                .medal-bronze {
                                    background: radial-gradient(circle at 30% 30%, #e7c6a5, #a0522d);
                                    border: 2px solid #804000;
                                }
                            </style>
                            <div class="card-body text-center" style="max-height: 300px; overflow-y: auto;">
                                @if ($rangking->isEmpty())
                                    <div class="text-center text-muted">
                                        <i class="bx bx-trophy-alt" style="font-size: 50px;"></i>
                                        <p class="mt-2">No Hall of Fame yet</p>
                                    </div>
                                @else
                                    @foreach ($rangking as $item)
                                        <li class="list-group-item mb-3 d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                @if ($loop->iteration <= 3)
                                                    <div
                                                        class="me-2 medal-number 
                                                @if ($loop->iteration == 1) medal-gold
                                                @elseif ($loop->iteration == 2) medal-silver
                                                @elseif ($loop->iteration == 3) medal-bronze @endif">
                                                        {{ $loop->iteration }}
                                                    </div>
                                                @else
                                                    <div class="me-2" style="width: 36px;"></div>
                                                @endif

                                                <span>{{ $loop->iteration }}. {{ $item->name_user }}</span>
                                            </div>
                                            <span class="badge bg-success">{{ $item->total_point }} pts</span>
                                        </li>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-md-6 col-6 mb-4">
                        <div class="card shadow-sm border-0 rounded-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="bx bx-time-five me-1"></i> Latest Recorded
                                </h6>
                            </div>
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                @foreach ($lastRecord as $record)
                                    <div
                                        class="d-flex align-items-start bg-primary text-white shadow rounded-3 p-3 mb-3 position-relative">
                                        <div class="badge rounded-circle bg-white text-primary me-3 d-flex align-items-center justify-content-center fw-bold"
                                            style="width: 32px; height: 32px;">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">
                                                <b>{{ $record->name }}</b> telah ditambahkan oleh
                                                <b>{{ $record->name_user }}</b>
                                            </div>
                                            {{-- <div class="small">
                                                telah ditambahkan oleh <b>{{ $record->name_user }}</b>
                                            </div> --}}
                                            <div class="small mt-1">{{ tanggalIndonesia($record->created_at) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm border-0 rounded-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-light">
                                    <i class="bx bx-world me-1"></i> Prospective Partners by Country
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <div id="regions_div" style="width: 100%; height: 600px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Chart.js --}}
    <script type="text/javascript">
    let chartLoaded = false;

    const dataNegara = @json($dataNegara);

    function drawRegionsMap() {
        const chartData = [
            ['Country', 'Jumlah']
        ];

        if (dataNegara.length === 0) {
            chartData.push(['No Data', 0]);
        } else {
            dataNegara.forEach(item => {
                chartData.push([item.nama_negara, item.jumlah]);
            });
        }

        const data = google.visualization.arrayToDataTable(chartData);

        const options = {
            backgroundColor: {},
            colorAxis: {
                colors: ['#C6D6FF', '#5A72C9', '#291F71'] 
            },
            datalessRegionColor: '#ccc'
        };

        const chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
        chart.draw(data, options);
    }

    google.charts.load('current', {
        packages: ['geochart']
    });

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !chartLoaded) {
                chartLoaded = true;
                google.charts.setOnLoadCallback(drawRegionsMap);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.addEventListener("DOMContentLoaded", () => {
        const chartSection = document.getElementById('regions_div');
        if (chartSection) {
            observer.observe(chartSection);
        }
    });
</script>

@endpush
