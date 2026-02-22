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
            <th>Nama Institusi</th>
            <th>Jenis Institusi</th>
            <th>Status Kerma</th>
            <th>Tanggal Selesai</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($DataSurvei as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nama_institusi }}</td>
                <td>{{ $item->jenis_kerjasama }}</td>
                <td>{{ $item->stats_kerma }}
                </td>
                <td>{{ TanggalLengkap($item->tgl_selesai) }}</td>
                <td>
                    <!-- Button to open the survey page for this particular id_mou -->
                    <button class="btn btn-sm btn-primary"
                        onclick="fillSurvey('{{ $item->id_mou }}', '{{ $item->stats_kerma }}')"
                        data-id_mou="{{ $item->id_mou }}">Isi Survei</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
