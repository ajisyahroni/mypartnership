<div class="table-responsive" style="overflow-x: auto;">
    <table class="table table-hover align-middle custom-table" id="dataTableMasukan">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Jenis</th>
                <th>Jawaban</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataMasukan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->jenis }}</td>
                    <td>{{ $item->jawaban }}</td>
                    <td>{{ Tanggal_Indo($item->created_at) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


<script>
    $("#dataTableMasukan").DataTable({
        "responsive": true,
        "autoWidth": false,
        "lengthChange": false,
        "pageLength": 10,
        "language": {
            "emptyTable": "Tidak ada data yang tersedia",
            "zeroRecords": "Tidak ada data yang ditemukan",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered": "(disaring dari total _MAX_ entri)",
            "search": "Cari:",
            "paginate": {
                "previous": "&laquo;",
                "next": "&raquo;"
            }
        }
    });
</script>
