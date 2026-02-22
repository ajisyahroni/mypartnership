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
                                                <th>Id Lembaga</th>
                                                <th>Nama Lembaga</th>
                                                <th>Singkatan</th>
                                                <th>Status Tempat</th>
                                                <th>Jenis Lembaga</th>
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
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="DetailLabel">Form Referensi Lembaga UMS</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('referensi.lembaga_ums.store') }}" method="post" id="formInput">
                            @csrf
                            <div class="modal-body">
                                <div class="row p-3">
                                    <div class="col-12">
                                        <div class="card mb-3 alert alert-danger">
                                            <div class="card-body">
                                                <h5 class="fw-bold text-center w-100">"Perbedaan nama akan mempengaruhi
                                                    hasil rekap."</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3">
                                            <label for="id_lmbg" class="form-label">ID Lembaga<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="id_lmbg" name="id_lmbg"
                                                placeholder="Masukkan ID Lembaga" value="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3">
                                            <label for="nama_lmbg" class="form-label">Nama Lembaga<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama_lmbg" name="nama_lmbg"
                                                placeholder="Masukkan Nama Lembaga" value="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3">
                                            <label for="status_tempat" class="form-label">Status Tempat</label>
                                            <input type="text" class="form-control" id="status_tempat"
                                                name="status_tempat" placeholder="Masukkan Status Tempat" value="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3">
                                            <label for="namalmbg_singkat" class="form-label">Singkatan Nama Lembaga</label>
                                            <input type="text" class="form-control" id="namalmbg_singkat"
                                                name="namalmbg_singkat" placeholder="Masukkan Singkatan Nama Lembaga"
                                                value="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3">
                                            <label for="label" class="form-label">Jenis Lembaga <span
                                                    class="text-danger">*</span></label>
                                            <select name="jenis_lmbg" id="jenis_lmbg" class="form-control">
                                                <option value="">Pilih Jenis Lembaga</option>
                                                @foreach ($jenis_lmbg as $item)
                                                    <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="mb-3">
                                            <label for="label" class="form-label">Super Unit <span
                                                    class="text-danger">*</span></label>
                                            <select name="super_unit" id="super_unit" class="form-control">
                                                <option value="">Pilih Super Unit</option>
                                                @foreach ($super_unit as $item)
                                                    <option value="{{ $item->id_lmbg }}">{{ $item->nama_lmbg }}</option>
                                                @endforeach
                                            </select>
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
        var getData = "{{ route('referensi.lembaga_ums.getData') }}"
        $(document).ready(function() {
            $("#super_unit").select2({
                theme: "bootstrap-5",
                dropdownParent: $("#modal-form"),
                width: '100%',
            });
            $("#jenis_lmbg").select2({
                theme: "bootstrap-5",
                dropdownParent: $("#modal-form"),
                width: '100%',
            });
        });
    </script>
    <script src="{{ asset('js/admin/referensi/lembaga_ums/index.js') }}"></script>
@endpush
