<link rel="stylesheet" href="//cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
<style>
    .tableNotif .table {
        font-size: 12px;
    }

    .tableNotif .table td,
    .tableNotif .table th {
        padding: 4px 6px !important;
        line-height: 1.3;
        vertical-align: middle;
    }

    .tableNotif .table td img {
        max-width: 300px;
        height: auto;
        display: block;
        margin-top: 5px;
    }
</style>

<div class="tableNotif">
    <table id="tabelDetailPendanaan" class="table table-hover">
        <thead>
            <tr>
                <th colspan="6" class="text-center">DATA YANG HARUS DI TINDAK LANJUTI</th>
            </tr>
            <tr>
                <th>No</th>
                <th>Judul Proposal</th>
                <th>Institusi Mitra</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if (empty($dataUser))
                <tr>
                    <td colspan="7">Belum Ada Data</td>
                </tr>
            @else
                @foreach ($dataUser as $index => $dt)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $dt->judul_proposal }}</td>
                        <td>{{ $dt->institusi_mitra }}</td>
                        <td>{!! $dt->status_label !!}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
