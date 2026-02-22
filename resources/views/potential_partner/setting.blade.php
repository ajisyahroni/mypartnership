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
                                        <i class="fa-solid fa-folder-open text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                                <a href="{{ route('potential_partner.home') }}" class="btn btn-danger">Kembali</a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('potential_partner.storeSetting') }}" method="post" id="formInput"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-3">
                                    <input type="hidden" name="id_setting_partner"
                                        value="{{ @$dataSetting->id_setting_partner }}">
                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                        <label for="PoinDN"><b>Poin Dalam Negeri</b></label>
                                        <input type="number" name="poin_dn" class="form-control"
                                            value="{{ @$dataSetting->poin_dn }}">
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                        <label for="PoinLN"><b>Poin Luar Negeri</b></label>
                                        <input type="number" name="poin_ln" class="form-control"
                                            value="{{ @$dataSetting->poin_ln }}">
                                    </div>
                                </div>
                                <div class="bg-primary p-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-light">
                                        <i class="fa fa-save me-2"></i> Simpan
                                    </button>
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
    <script src="{{ asset('js/potential_partner/setting.js') }}"></script>
@endpush
