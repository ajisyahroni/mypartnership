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
                                                <th>Jenis Hibah</th>
                                                <th>Maksimum</th>
                                                <th>Deadline Proposal</th>
                                                <th>Deadline Laporan</th>
                                                <th>Status</th>
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
                            <h5 class="modal-title" id="DetailLabel">Form Referensi Jenis Hibah</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('referensi.jenis_hibah.store') }}" method="post" id="formInput">
                            @csrf
                            <div class="modal-body">
                                <div class="row p-3">
                                    <input type="hidden" name="id" id="id">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="jenis_hibah" class="form-label">Jenis Hibah <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="jenis_hibah" name="jenis_hibah"
                                                placeholder="Masukkan Jenis Hibah" value="">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="maksimum" class="form-label">Maksimum <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control isRupiahs" id="maksimum"
                                                name="maksimum" placeholder="Masukkan Maksimum" value="">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="dl_proposal" class="form-label">Deadline Proposal<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="dl_proposal" name="dl_proposal"
                                                placeholder="Pilih Tanggal Deadline Proposal" value="">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="dl_laporan" class="form-label">Deadline Laporan<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="dl_laporan" name="dl_laporan"
                                                placeholder="Pilih Tanggal Deadline Laporan" value="">
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
        var UrlSwitchStatus = @json(route('referensi.jenis_hibah.switch-status'));
        var getData = "{{ route('referensi.jenis_hibah.getData') }}"

        const dlProposal = flatpickr("#dl_proposal", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            locale: "id",
            allowInput: true,
        });

        const dlLaporan = flatpickr("#dl_laporan", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            locale: "id",
            allowInput: true
        });
    </script>
    <script src="{{ asset('js/admin/referensi/jenis_hibah/index.js') }}"></script>
@endpush
