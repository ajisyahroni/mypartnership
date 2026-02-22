<style>
    .table {
        font-size: 14px;
    }

    .table td,
    .table th {
        padding: 10px 14px !important;
        line-height: 1.3;
        vertical-align: middle;
    }

    .table td img {
        max-width: 300px;
        height: auto;
        display: block;
        margin-top: 5px;
    }
</style>

@php
    $dataHibah = $dataHibah ?? null;

    function encodedFileUrl($filePath)
    {
        if (!$filePath) {
            return null;
        }
        return asset('storage/' . ltrim($filePath, '/'));
    }
@endphp

<input type="hidden" name="id_hibah" value="{{ @$dataHibah->id_hibah }}">
<input type="hidden" name="tahap" value="{{ @$tahap }}">
<input type="hidden" name="id_laporan_hibah" value="{{ @$dataHibah->id_laporan_hibah }}">
<table id="tabelDetailHibah" class="table table-hover table-bordered">
    <tbody>
        <tr>
            <td>Biaya yang diajukan</td>
            <td>:</td>
            <td>Rp. {{ rupiah(optional($dataHibah)->biaya ?? 0) }}</td>
        </tr>
        <tr>
            <td>Biaya yang disetujui</td>
            <td>:</td>
            <td>Rp. {{ rupiah(optional($dataHibah)->dana_disetujui_bkui ?? 0) }}</td>
        </tr>
        <tr>
            <td>Sisa Dana</td>
            <td>:</td>
            <td>Rp. {{ rupiah($dataHibah->sisa_dana) }}</td>
        </tr>

        @if ($tahap == '1')
            <tr>
                <td>Nominal Pencairan Dana Tahap 1</td>
                <td>:</td>
                <td>
                    <input type="text" name="pencairan_tahap_satu" class="form-control isRupiahs"
                        placeholder="Masukkan Nominal Pencairan Dana Tahap 1">
                </td>
            </tr>
        @else
            <tr>
                <td>Nominal Pencairan Dana Tahap 1</td>
                <td>:</td>
                <td>Rp. {{ rupiah(optional($dataHibah)->pencairan_tahap_satu ?? 0) }}</td>
            </tr>
            <tr>
                <td>Nominal Pencairan Dana Tahap 2</td>
                <td>:</td>
                <td>
                    <input type="text" name="pencairan_tahap_dua" class="form-control isRupiahs"
                        placeholder="Masukkan Nominal Pencairan Dana Tahap 2">
                </td>
            </tr>
        @endif

        <tr>
            <td>Metode Pembayaran</td>
            <td>:</td>
            <td>
                <select id="metode_pembayaran" name="metode_pembayaran" class="form-select select2">
                    <option value="">Pilih Metode Pembayaran</option>
                    <option value="transfer">Transfer</option>
                    <option value="tunai">Tunai</option>
                </select>
            </td>
        </tr>

        {{-- Upload Bukti Transfer Tahap 1 --}}
        <tr>
            <td>Upload File Bukti Transfer Tahap 1</td>
            <td>:</td>
            <td>
                @if ($tahap == '1')
                    <input type="file" name="file_bukti_transfer_tahap_satu" class="form-control" accept=".pdf">
                @endif

                @php $fileTahapSatu = encodedFileUrl(optional($dataHibah)->file_bukti_transfer_tahap_satu); @endphp

                @if ($fileTahapSatu)
                    <a href="{{ $fileTahapSatu }}" class="btn btn-sm btn-primary mt-2" target="_blank">
                        <i class="bx bx-download"></i> Download
                    </a>
                    <iframe src="{{ $fileTahapSatu }}" frameborder="0" style="width: 100%; height: 300px;"
                        class="mt-2"></iframe>
                @elseif($tahap != '1')
                    <small class="text-danger">Belum Ada File Upload</small>
                @endif
            </td>
        </tr>

        {{-- Upload Bukti Transfer Tahap 2 (Jika Tahap 2) --}}
        @if ($tahap != '1')
            <tr>
                <td>Upload File Bukti Transfer Tahap 2</td>
                <td>:</td>
                <td>
                    <input type="file" name="file_bukti_transfer_tahap_dua" class="form-control" accept=".pdf">

                    @php $fileTahapDua = encodedFileUrl(optional($dataHibah)->file_bukti_transfer_tahap_dua); @endphp

                    @if ($fileTahapDua)
                        <a href="{{ $fileTahapDua }}" class="btn btn-sm btn-primary mt-2" target="_blank">
                            <i class="bx bx-download"></i> Download
                        </a>
                        <iframe src="{{ $fileTahapDua }}" frameborder="0" style="width: 100%; height: 300px;"
                            class="mt-2"></iframe>
                    @else
                        <small class="text-danger">Belum Ada File Upload</small>
                    @endif
                </td>
            </tr>
        @endif

    </tbody>
</table>
