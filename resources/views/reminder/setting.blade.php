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
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('pengajuan.storeSetting') }}" method="post" id="formInput"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-3">
                                    <input type="hidden" name="id_setting_bobot"
                                        value="{{ @$dataSetting->id_setting_bobot }}">
                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                        <label for="DalamNegeri"><b>Bobot Dalam Negeri</b></label>
                                        <input type="text" name="dalam_negeri" class="form-control isRupiahs"
                                            value="{{ @$dataSetting->dalam_negeri }}">
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                                        <label for="LuarNegeri"><b>Bobot Luar Negeri</b></label>
                                        <input type="text" name="luar_negeri" class="form-control isRupiahs"
                                            value="{{ @$dataSetting->luar_negeri }}">
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
@endsection

@push('scripts')
    <script src="{{ asset('js/pengajuan/setting.js') }}"></script>
@endpush
