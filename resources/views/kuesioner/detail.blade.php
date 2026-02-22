<style>
    .badge-kategori {
        background-color: #0dcaf0;
        font-size: 0.8rem;
        padding: 0.4em 0.6em;
        border-radius: 0.5rem;
    }

    .video-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }

    .section-title {
        font-weight: 600;
    }

    table.table-bordered th,
    table.table-bordered td {
        border: 1px solid #dee2e6 !important;
    }
</style>

<div class="container">
    <div class="mb-4">
        <h4 class="fw-semibold">Follow Up MoU Kerjasama UMS - {{ $dataKuesioner->nama_institusi }}</h4>
        <div class="text-muted small">by <strong>{{ $dataKuesioner->postby }}</strong> •
            {{ TanggalIndonesia($dataKuesioner->que_create) }}</div>
    </div>

    <table class="table table-bordered bg-white">
        <tbody>
            <tr>
                <th style="width: 20%">Bentuk Kegiatan</th>
                <td><strong>{{ $dataKuesioner->bentuk_kegiatan }}</strong></td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td><span class="badge bg-primary" style="font-size: 10px!important;"
                        data-title-tooltip="{{ $dataKuesioner->category }}">{{ $dataKuesioner->category }}</span></td>
            </tr>
            <tr>
                <th>Bukti Implementasi</th>
                <td>
                    @if ($dataKuesioner->file_imp != null)
                        <a href="{{ getDocumentUrl($dataKuesioner->file_imp, 'file_imp') }}"
                            class="btn btn-primary btn-sm shadow-sm">
                            <i class="fas fa-file-pdf"></i> Download Bukti Implementasi
                        </a>
                    @else
                        <span class="badge bg-danger" style="font-size: 10px!important;"
                            data-title-tooltip="Belum Diupload">Belum Diupload</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <style>
        .ev_content p img {
            width: 100% !important;
        }
    </style>
    <div class="card">
        <div class="card-body ev_content">
            {!! $dataKuesioner->ev_content !!}
            {!! $dataKuesioner->deskripsi_singkat !!}
        </div>
    </div>


</div>
