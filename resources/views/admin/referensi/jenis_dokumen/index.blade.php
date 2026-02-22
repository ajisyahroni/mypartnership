@extends('layouts.app')

@section('contents')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="me-2">
                                        <i class="fa-solid fa-folder-open text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <button id="btnTambah" class="btn btn-primary btn-sm shadow-sm">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Tambah Referensi
                                    </button>
                                    {{-- <a href="{{ route('pengajuan.laporPengajuan') }}"
                                        class="btn btn-success btn-sm shadow-sm">
                                        <i class="fas fa-folder-plus me-2"></i> Simpan Dokumen
                                    </a> --}}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mt-4">
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-hover align-middle custom-table" id="dataTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Dokumen</th>
                                                <th>Alias</th>
                                                <th>Lingkup Unit</th>
                                                <th>Penandatangan</th>
                                                <th>Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-form" aria-labelledby="DetailLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="DetailLabel">Form Referensi Jenis Dokumen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('referensi.jenis_dokumen.store') }}" method="post" id="formInput">
                            @csrf
                            <div class="modal-body">
                                <div class="row p-3">
                                    <input type="hidden" name="uuid" id="uuid">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="nama_dokumen" class="form-label">Nama Dokumen <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen"
                                                placeholder="Masukkan Nama Dokumen" value="">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="alias" class="form-label">Alias <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="alias" name="alias"
                                                placeholder="Masukkan Alias" value="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3">
                                            <label for="lingkup_unit" class="form-label">Lingkup Unit</label>
                                            @foreach ($tingkat_kerjasama as $tingkat)
                                                <div class="custom-check checkbox-lg mb-2">
                                                    <input class="custom-check-input" type="checkbox" name="lingkup_unit[]"
                                                        value="{{ $tingkat->nama }}" id="checkbox-{{ $tingkat->nama }}" />
                                                    <label class="custom-check-label text-dark"
                                                        for="checkbox-{{ $tingkat->nama }}">{{ $tingkat->nama }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3">
                                            <label for="Penandatangan" class="form-label">Penandatangan</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="ttd"
                                                    id="ttd_by_bkui" value="BKUI">
                                                <label class="form-check-label" for="ttd_by_bkui">BKUI</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="ttd"
                                                    id="ttd_by_pengusul" value="Pengusul">
                                                <label class="form-check-label" for="ttd_by_pengusul">Pengusul</label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var getData = "{{ route('referensi.jenis_dokumen.getData') }}"
    </script>
    <script src="{{ asset('js/admin/referensi/jenis_dokumen/index.js') }}"></script>
@endpush
