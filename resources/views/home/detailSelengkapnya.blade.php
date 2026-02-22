@if ($tipe == 'peringkat')
    <div class="col-12 mb-3">
        <div class="ranking-card p-3">
            <div class="table-wrapper">
                <table class="table table-bordered ranking-table mb-0" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Program Studi</th>
                            <th>Jumlah Implementasi</th>
                            <th style="width: 100px;">Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $dt)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $dt->status_tempat }}</td>
                                <td>{{ number_format($dt->jumlah_produktivitas, 0) }}
                                <td>{{ number_format($dt->jumlah_skor, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada
                                    data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex align-items-center mt-2">
                <span class="dot bg-success me-2"
                    style="width: 10px; height: 10px; border-radius: 50%; display: inline-block;"></span>
                <span class="fw-semibold text-muted">Program Studi</span>
            </div>
        </div>
    </div>
@elseif($tipe == 'produktif')
    <div class="col-12 mb-3">
        <div class="ranking-card p-3">
            <div class="table-wrapper">
                <table class="table table-bordered ranking-table" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align:middle;text-align:center;">
                                No</th>
                            <th rowspan="2" style="vertical-align:middle;text-align:center;">
                                Lembaga</th>
                            <th colspan="2" style="text-align:center;">
                                Implementasi Kerja Sama</th>
                        </tr>
                        <tr>
                            <th class="text-center">Dalam Negeri
                            </th>
                            <th class="text-center">Luar
                                Negeri</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $dt)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $dt->status_tempat }}
                                </td>
                                <td>{{ number_format($dt->jumlah_produktivitas_kerma_dn, 0) }}
                                </td>
                                <td>{{ number_format($dt->jumlah_produktivitas_kerma_ln, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak
                                    ada
                                    data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex align-items-center">
                <span class="dot me-2"></span>
                <span class="title-dashboard">Produktivitas Kerja
                    Sama</span>
            </div>
        </div>
    </div>
@elseif($tipe == 'mitra')
    <div class="col-12 mb-3">
        <div class="ranking-card p-3">
            <div class="table-wrapper">
                <table class="table table-bordered ranking-table" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align:middle;text-align:center;">
                                No</th>
                            <th rowspan="2" style="vertical-align:middle;text-align:center;">
                                Lembaga</th>
                            <th colspan="2" style="text-align:center;">
                                Implementasi Kerja Sama</th>
                        </tr>
                        <tr>
                            <th class="text-center">Dalam Negeri
                            </th>
                            <th class="text-center">Luar
                                Negeri</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $dt)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $dt->status_tempat }}
                                </td>
                                <td>{{ number_format($dt->jumlah_mitra_kerma_dn, 0) }}
                                </td>
                                <td>{{ number_format($dt->jumlah_mitra_kerma_ln, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak
                                    ada
                                    data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex align-items-center">
                <span class="dot me-2"></span>
                <span class="title-dashboard">Jumlah Mitra Kerja
                    Sama</span>
            </div>
        </div>
    </div>
@endif
