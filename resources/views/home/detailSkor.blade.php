<style>
    .fixed-header {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #343a40;
        color: white;
        /* Match the dark background color */
    }

    .fixed-footer {
        position: sticky;
        bottom: 0;
        z-index: 10;
        background-color: #343a40;
        color: white;
        /* Match the dark background color */
    }

    .table th,
    .table td {
        text-align: center;
    }

    .custom-table tbody {
        max-height: 200px;
        /* Set the height for scrollable area */
        overflow-y: auto;
    }
</style>

<div class="alert alert-info d-flex align-items-center p-3 mb-3"
    style="background-color: #e6faff; border-left: 6px solid #00bcd4;">
    <div class="me-3 text-white d-flex align-items-center justify-content-center"
        style="background-color: #00bcd4; width: 30px; height: 30px; border-radius: 4px;">
        <i class="bx bx-info-circle"></i>
    </div>
    <div class="text-dark small">
        <ul class="mb-0">
            @if (@$type == 'ProdiScore')
                <li><strong>Rumus Skor Prodi:</strong> Jika institusi <b>Luar Negeri</b>, maka skor = bobot x
                    {{ $dataSetting->luar_negeri }}. Jika
                    <b>Dalam Negeri</b>, maka skor = bobot.
                </li>
            @elseif(@$type == 'AverageScore')
                <li><strong>Rumus Skor Rata-rata:</strong> Total semua skor dibagi dengan jumlah institusi (dengan skor
                    > 0).</li>
            @endif
        </ul>
    </div>
</div>

@if (@$type == 'ProdiScore')
    <div style="max-height: 400px; overflow-y: auto;">
        <table class="table table-hover align-middle custom-table">
            <thead class="table-dark fixed-header">
                <tr>
                    <th>No</th>
                    <th>Nama Institusi</th>
                    <th>Judul Kegiatan</th>
                    <th>Bentuk Kegiatan</th>
                    <th>Lingkup</th>
                    <th>Jenis Institusi</th>
                    <th>Bobot</th>
                    <th>Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-start">{{ $item->nama_institusi }}</td>
                        <td class="text-start">{{ $item->judul == 'Lain-lain' ? $item->judul_lain : $item->judul }}</td>
                        <td class="text-start">{{ $item->bentuk_kegiatan }}</td>
                        <td>{{ $item->dn_ln }}</td>
                        <td>{{ $item->jenis_institusi }}</td>
                        <td>{{ $item->bobot_ums }}</td>
                        <td>{{ $item->jumlah_skor }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="fixed-footer">
                @if (@$type == 'ProdiScore')
                    <tr>
                        <td colspan="7"><strong>Total Skor Prodi</strong></td>
                        <td>
                            @if ($data->count() > 0)
                                {{ $data->sum('jumlah_skor') }}
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                @elseif(@$type == 'AverageScore')
                    <tr>
                        <td colspan="5"><strong>Rata-rata Skor</strong></td>
                        <td>
                            @if ($data->count() > 0)
                                {{ round($data->sum('jumlah_skor') / $data->count(), 2) }}
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                @endif
            </tfoot>
        </table>
    </div>
@else
    <div style="max-height: 300px; overflow-y: auto;">
        <table class="table table-hover align-middle custom-table">
            <thead class="table-dark fixed-header">
                <tr>
                    <th>No</th>
                    <th class="text-start">Nama Program Studi</th>
                    <th>Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-start">{{ $item->status_tempat }}</td>
                        <td>{{ $item->jumlah_skor }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="fixed-footer">
                @if (@$type == 'ProdiScore')
                    <tr>
                        <td colspan="5"><strong>Total Skor Prodi</strong></td>
                        <td>
                            @if ($data->count() > 0)
                                {{ $data->sum('jumlah_skor') }}
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                @elseif(@$type == 'AverageScore')
                    <tr>
                        <td colspan="2"><strong>Total Skor</strong></td>
                        <td>
                            @if ($data->count() > 0)
                                {{ round($data->sum('jumlah_skor')) }}
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Rata-rata Skor</strong></td>
                        <td>
                            @if ($data->count() > 0)
                                {{ round($data->sum('jumlah_skor') / $data->count(), 2) }}
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                @endif
            </tfoot>
        </table>
    </div>

@endif
