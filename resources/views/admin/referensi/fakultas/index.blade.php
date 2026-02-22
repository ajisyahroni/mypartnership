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
                                                <th>Nama</th>
                                                <th>Label</th>
                                                <th>Check</th>
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
                            <h5 class="modal-title" id="DetailLabel">Form Referensi Pelaksana Kerja Sama</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('referensi.pelaksana_kerjasama.store') }}" method="post" id="formInput">
                            @csrf
                            <div class="modal-body">
                                <div class="row p-3">
                                    <input type="hidden" name="id" id="id">
                                    <div class="col-12">
                                        <div class="card mb-3 alert alert-danger">
                                            <div class="card-body">
                                                <h5 class="fw-bold text-center w-100">"Perbedaan nama akan mempengaruhi
                                                    hasil rekap."</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama" name="nama"
                                                placeholder="Masukkan Nama" value="">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="label" class="form-label">Label <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="label" name="label"
                                                placeholder="Masukkan Label" value="">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="check" class="form-label">Check <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="check" name="check"
                                                placeholder="Masukkan Check" value="">
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
        var getData = "{{ route('referensi.pelaksana_kerjasama.getData') }}"
    </script>
    <script src="{{ asset('js/admin/referensi/pelaksana_kerjasama/index.js') }}"></script>
@endpush
