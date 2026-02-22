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

{{-- <div style="max-height: 300px; overflow-y: auto;"> --}}
<div class="p-3" style="overflow-y: auto;">
    <table class="table table-hover align-middle custom-table" id="dataTableKerma">
        <thead class="table-dark fixed-header">
            <tr>
                <th>No</th>
                <th>Nama Institusi</th>
                <th>Lembaga</th>
                <th>Jenis Institusi</th>
                <th>Tanggal Kerja Sama</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-start">{{ $item->nama_institusi }}</td>
                    <td class="text-start">{{ $item->status_tempat }}</td>
                    <td class="text-start">{{ $item->jenis_institusi }}</td>
                    <td class="text-start"><span class="badge bg-primary"
                            style="font-size: 10px!important;">{{ tanggal_indo($item->mulai) }} -
                            {{ tanggal_indo($item->selesai) }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $("#dataTableKerma").DataTable({
            // paging: false,
            info: false,
            searching: true,
            // scrollY: '300px',
            scrollCollapse: true,
        });
    })
</script>
