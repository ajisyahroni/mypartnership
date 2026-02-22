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
                                    <button id="btnTambah" class="btn btn-primary shadow-sm">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        Tambah Referensi
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 d-flex justify-content-end">
                                <div class="col-12 col-md-4">
                                    <label for="filterjenis" class="form-label fw-semibold">Filter Jenis: <span
                                            class="text-danger">*</span></label>
                                    <select name="filterjenis" id="filterjenis" class="form-select select2 w-100">
                                        <option value="">Pilih Jenis Pertanyaan</option>
                                        <option value="Ajuan Baru">Ajuan Baru</option>
                                        <option value="Lapor Kerma">Lapor Kerma</option>
                                        <option value="Implementasi">Implementasi</option>
                                        <option value="Rekognisi">Rekognisi</option>
                                        <option value="Hibah">Hibah</option>
                                    </select>
                                </div>
                            </div>


                            <div class="table-responsive">
                                <table class="table table-hover align-middle custom-table" id="dataTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis</th>
                                            <th>Judul</th>
                                            <th>Pertanyaan</th>
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

            <div class="modal fade" id="modal-form" aria-labelledby="DetailLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="DetailLabel">Form Referensi Pertanyaan Survei</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('referensi.pertanyaan_survei.store') }}" method="post" id="formInput">
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
                                            <label for="jenis" class="form-label">Jenis <span
                                                    class="text-danger">*</span></label>
                                            <select name="jenis" class="form-control select2" id="jenis">
                                                <option value="">Pilih Jenis Pertanyaan</option>
                                                <option value="Ajuan Baru">Ajuan Baru</option>
                                                <option value="Lapor Kerma">Lapor Kerma</option>
                                                <option value="Implementasi">Implementasi</option>
                                                <option value="Rekognisi">Rekognisi</option>
                                                <option value="Hibah">Hibah</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="judul" class="form-label">Judul Pertanyaan <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="judul" id="judul">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="pertanyaan" class="form-label">Pertanyaan <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="pertanyaan" class="form-control" id="pertanyaan" cols="30" rows="5"
                                                placeholder="Masukkan Pertanyaan"></textarea>
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
        var getData = "{{ route('referensi.pertanyaan_survei.getData') }}"
    </script>
    <script src="{{ asset('js/admin/referensi/pertanyaan_survei/index.js') }}"></script>
@endpush
