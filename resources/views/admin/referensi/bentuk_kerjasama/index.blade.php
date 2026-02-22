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
                                                <th>Deskripsi</th>
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
                            <h5 class="modal-title" id="DetailLabel">Form Referensi Bentuk Kerja Sama</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('referensi.bentuk_kerjasama.store') }}" method="post" id="formInput">
                            @csrf
                            <div class="modal-body">
                                <div class="row p-3">
                                    <input type="hidden" name="uuid" id="uuid">
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
                                            <label for="deskripsi" class="form-label">Deskripsi <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="deskripsi" class="form-control" id="deskripsi" cols="30" rows="10"
                                                placeholder="Masukkan Deskripsi"></textarea>
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
        var getData = "{{ route('referensi.bentuk_kerjasama.getData') }}"
    </script>
    <script src="{{ asset('js/admin/referensi/bentuk_kerjasama/index.js') }}"></script>
@endpush
