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

<div class="p-3" style="overflow-y: auto;">
    <table class="table table-hover align-middle custom-table" id="dataTableKerma" style="width:100%;">
        <thead class="table-dark fixed-header">
            <tr>
                <th style="white-space: nowrap; width:1%;">No</th>
                <th style="white-space: nowrap; width:1%;">Mitra Kerja Sama</th>
                <th style="white-space: nowrap; width:1%;">Jumlah Produktif</th>
                <th style="white-space: nowrap; width:1%;">Tingkat Kerja Sama</th>
                <th style="white-space: nowrap; width:1%;">Pelaksana</th>
                <th style="white-space: nowrap; width:1%;">Kontribusi</th>
                <th style="white-space: nowrap; width:1%;">Link Kerja Sama</th>
                <th style="white-space: nowrap; width:1%;">Tanggal Kerja Sama</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-start">{{ $item->nama_institusi }}</td>
                    <td class="text-start">{{ $item->jumlah_produktivitas }}</td>
                    <td class="text-start">
                        {{ $item->dn_ln == 'Dalam Negeri' ? $item->dn_ln . ' ' . $item->wilayah_mitra : $item->negara_mitra }}
                    </td>
                    <td class="text-start">{{ $item->status_tempat }}</td>
                    <td class="text-start">{{ $item->kontribusi }}</td>
                    <td class="text-start">
                        <a href="{{ getDocumentUrl($item->file_mou, 'file_mou') }}" target="_blank" class="btn btn-primary">
                            <i class="bx bx-download"></i>
                        </a>
                    </td>
                    @if ($item->periode_kerma == 'notknown')
                        <td class="text-start">
                            <span class="badge bg-primary" style="font-size: 10px!important;">
                                {{ $item->mulai == '0000-00-00' || $item->mulai == null ? tanggal_indo($item->awal) : tanggal_indo($item->mulai) }}
                            </span>
                    @else
                    <td class="text-start">
                        <span class="badge bg-primary" style="font-size: 10px!important;">
                            {{ tanggal_indo($item->mulai) }} - {{ tanggal_indo($item->selesai) }}
                        </span>
                    </td>
                    @endif
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
