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

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>


<p>Beberapa pengajuan Anda telah selesai diproses. Kami mohon kesediaan Anda untuk mengisi survei terkait.</p>
<div class="table-responsive">
    <table class="table table-hover align-middle table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Professor</th>
                <th>Prodi Pengusul</th>
                <th>Bidang Kepakaran</th>
                <th>Asal Universitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($DataSurvei as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_prof }}</td>
                    <td>{{ $item->faculty }}</td>
                    <td>{{ $item->bidang_kepakaran }}</td>
                    <td>{{ $item->univ_asal }}</td>
                    <td>
                        <!-- Button to open the survey page for this particular id_mou -->
                        <button class="btn btn-sm btn-primary" onclick="fillSurvey('{{ $item->id_rec }}', 'Rekognisi')"
                            data-id_rec="{{ $item->id_rec }}">Isi Survei</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
