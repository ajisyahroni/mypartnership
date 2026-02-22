<style>
    /* Custom Styles for Modern Table */
    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
        /* Hover effect */
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 12px;
        font-size: 12px;
        /* More space for better readability */
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid #dee2e6;
    }

    .table thead {
        background-color: #007bff;
        color: white;
    }

    .table-striped tbody tr:nth-child(odd) {
        background-color: #f8f9fa;
    }

    /* Button Styling */
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>


<p>Beberapa pengajuan Anda telah selesai diproses. Kami mohon kesediaan Anda untuk mengisi survei terkait.</p>
<table class="table table-hover align-middle table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Judul Kegiatan</th>
            <th>Nama Institusi</th>
            <th>Tanggal Pelaksana</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($DataSurvei as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->judul }}</td>
                <td>{{ $item->nama_institusi }}</td>
                <td>
                    @if ($item->tgl_mulai == null && $item->tgl_selesai == null)
                        <span class="badge bg-danger" style="font-size: 10px!important;"
                            data-title-tooltip="Tanggal Pelaksana Belum diisi">Belum diisi</span>
                    @else
                        @if ($item->tgl_mulai)
                            {{ Tanggal_Indo($item->tgl_mulai) }}
                        @else
                            <span class="badge bg-danger" style="font-size: 10px!important;"
                                data-title-tooltip="Tanggal Mulai Pelaksana Belum diisi">Belum diisi</span>
                        @endif
                        -
                        @if ($item->tgl_selesai)
                            {{ Tanggal_Indo($item->tgl_selesai) }}
                        @else
                            <span class="badge bg-danger" style="font-size: 10px!important;"
                                data-title-tooltip="Tanggal Selesai Pelaksana Belum diisi">Belum diisi</span>
                        @endif
                    @endif
                </td>

                <td>
                    <!-- Button to open the survey page for this particular id_mou -->
                    <button class="btn btn-sm btn-primary" onclick="fillSurvey('{{ $item->id_ev }}', 'Implementasi')"
                        data-id_ev="{{ $item->id_ev }}">Isi Survei</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
