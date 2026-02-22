@extends('layouts.app')

@section('contents')
    <style>
        input.flatpickr-input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input.flatpickr-input:focus {
            border-color: #00b4d8;
            box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.2);
            outline: none;
        }
    </style>
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="me-2">
                                        <i class="fas fa-cog text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                                <a href="{{ route('hibah.home') }}" class="btn btn-danger">Kembali</a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                           <form action="{{ route('hibah.storeSetting') }}" method="post" id="formInput" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id_setting_hibah" value="{{ @$dataSetting->id_setting_hibah }}">
                                    <!-- Latar Belakang Proposal -->
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-file-alt me-2"></i>
                                            Latar Belakang Proposal
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="min_latar_belakang_proposal" class="form-label fw-semibold">
                                                    Minimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-minus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="min_latar_belakang_proposal" 
                                                        id="min_latar_belakang_proposal"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->min_latar_belakang_proposal }}"
                                                        placeholder="Contoh: 100"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata minimum yang harus diisi</small>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="latar_belakang_proposal" class="form-label fw-semibold">
                                                    Maksimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="latar_belakang_proposal" 
                                                        id="latar_belakang_proposal"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->latar_belakang_proposal }}"
                                                        placeholder="Contoh: 500"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata maksimum yang diperbolehkan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Tujuan Proposal -->
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-bullseye me-2"></i>
                                            Tujuan Proposal
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="min_tujuan_proposal" class="form-label fw-semibold">
                                                    Minimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-minus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="min_tujuan_proposal" 
                                                        id="min_tujuan_proposal"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->min_tujuan_proposal }}"
                                                        placeholder="Contoh: 50"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata minimum yang harus diisi</small>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="tujuan_proposal" class="form-label fw-semibold">
                                                    Maksimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="tujuan_proposal" 
                                                        id="tujuan_proposal"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->tujuan_proposal }}"
                                                        placeholder="Contoh: 300"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata maksimum yang diperbolehkan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Target Proposal -->
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-crosshairs me-2"></i>
                                            Target Proposal
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="min_target_proposal" class="form-label fw-semibold">
                                                    Minimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-minus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="min_target_proposal" 
                                                        id="min_target_proposal"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->min_target_proposal }}"
                                                        placeholder="Contoh: 50"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata minimum yang harus diisi</small>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="target_proposal" class="form-label fw-semibold">
                                                    Maksimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="target_proposal" 
                                                        id="target_proposal"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->target_proposal }}"
                                                        placeholder="Contoh: 300"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata maksimum yang diperbolehkan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Detail Institusi Mitra -->
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-building me-2"></i>
                                            Detail Institusi Mitra
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="min_detail_institusi_mitra" class="form-label fw-semibold">
                                                    Minimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-minus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="min_detail_institusi_mitra" 
                                                        id="min_detail_institusi_mitra"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->min_detail_institusi_mitra }}"
                                                        placeholder="Contoh: 100"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata minimum yang harus diisi</small>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="detail_institusi_mitra" class="form-label fw-semibold">
                                                    Maksimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="detail_institusi_mitra" 
                                                        id="detail_institusi_mitra"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->detail_institusi_mitra }}"
                                                        placeholder="Contoh: 500"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata maksimum yang diperbolehkan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Detail Kerja Sama -->
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-handshake me-2"></i>
                                            Detail Kerja Sama
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="min_detail_kerma" class="form-label fw-semibold">
                                                    Minimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-minus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="min_detail_kerma" 
                                                        id="min_detail_kerma"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->min_detail_kerma }}"
                                                        placeholder="Contoh: 100"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata minimum yang harus diisi</small>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="detail_kerma" class="form-label fw-semibold">
                                                    Maksimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="detail_kerma" 
                                                        id="detail_kerma"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->detail_kerma }}"
                                                        placeholder="Contoh: 500"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata maksimum yang diperbolehkan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Indikator Keberhasilan -->
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-chart-line me-2"></i>
                                            Indikator Keberhasilan
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="min_indikator_keberhasilan" class="form-label fw-semibold">
                                                    Minimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-minus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="min_indikator_keberhasilan" 
                                                        id="min_indikator_keberhasilan"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->min_indikator_keberhasilan }}"
                                                        placeholder="Contoh: 50"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata minimum yang harus diisi</small>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="indikator_keberhasilan" class="form-label fw-semibold">
                                                    Maksimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="indikator_keberhasilan" 
                                                        id="indikator_keberhasilan"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->indikator_keberhasilan }}"
                                                        placeholder="Contoh: 300"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata maksimum yang diperbolehkan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Rencana Proposal -->
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            Rencana Proposal
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="min_rencana_proposal" class="form-label fw-semibold">
                                                    Minimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-minus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="min_rencana_proposal" 
                                                        id="min_rencana_proposal"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->min_rencana_proposal }}"
                                                        placeholder="Contoh: 100"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata minimum yang harus diisi</small>
                                            </div>
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="rencana_proposal" class="form-label fw-semibold">
                                                    Maksimum Kata
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        <i class="fas fa-plus"></i>
                                                    </span>
                                                    <input type="number" 
                                                        name="rencana_proposal" 
                                                        id="rencana_proposal"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->rencana_proposal }}"
                                                        placeholder="Contoh: 500"
                                                        required>
                                                    <span class="input-group-text bg-light">kata</span>
                                                </div>
                                                <small class="text-muted">Jumlah kata maksimum yang diperbolehkan</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Maksimal Pendanaan BKUI -->
                                    {{-- <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            Pendanaan BKUI
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label for="min_rencana_proposal" class="form-label fw-semibold">
                                                    Nilai Maksimum
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light">
                                                        Rp
                                                    </span>
                                                    <input type="number" 
                                                        name="pendanaan_bkui" 
                                                        id="pendanaan_bkui"
                                                        class="form-control" 
                                                        value="{{ @$dataSetting->pendanaan_bkui }}"
                                                        placeholder="Contoh: 1000000"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                <div class="card-footer bg-light p-3 d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Pastikan nilai minimum lebih kecil dari maksimum
                                    </small>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Simpan Pengaturan
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-lihat-file" aria-labelledby="DetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DetailLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="konten-detail">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/hibah/setting.js') }}"></script>
@endpush
