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
                                <input type="hidden" name="id_setting_bobot" value="{{ @$dataSetting->id_setting_bobot }}">

                                <!-- SECTION 1: Setting Bobot Penilaian -->
                                <div class="mb-4">
                                    <h5 class="mb-3 text-dark"><i class="fa fa-sliders-h me-2"></i> Setting Bobot
                                        Penilaian</h5>
                                    <div class="row">
                                        <!-- Bobot Dalam Negeri -->
                                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                                            <label for="DalamNegeri" class="form-label"><b>Bobot Dalam Negeri</b></label>
                                            <input type="text" name="dalam_negeri" class="form-control isRupiahs"
                                                value="{{ @$dataSetting->dalam_negeri }}">
                                        </div>

                                        <!-- Bobot Luar Negeri -->
                                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                                            <label for="LuarNegeri" class="form-label"><b>Bobot Luar Negeri</b></label>
                                            <input type="text" name="luar_negeri" class="form-control isRupiahs"
                                                value="{{ @$dataSetting->luar_negeri }}">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- SECTION 2: Setting Kontak Kami -->
                                <div class="mb-4">
                                    <h5 class="mb-3 text-dark"><i class="fa fa-address-book me-2"></i> Setting Kontak
                                        Kami</h5>
                                    <div class="row">
                                        <!-- Nomor HP -->
                                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                                            <label for="nomor_hp" class="form-label"><b>Nomor HP</b></label>
                                            <input type="text" name="nomor_hp" class="form-control"
                                                value="{{ @$dataSetting->nomor_hp }}" placeholder="Contoh: 6282133669857">
                                        </div>

                                        <!-- Email -->
                                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                                            <label for="email" class="form-label"><b>Email</b></label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ @$dataSetting->email }}" placeholder="Contoh: email@domain.com">
                                        </div>

                                        <!-- Instagram -->
                                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                                            <label for="instagram" class="form-label"><b>Instagram</b></label>
                                            <input type="text" name="instagram" class="form-control"
                                                value="{{ @$dataSetting->instagram }}" placeholder="Masukkan Link">
                                        </div>

                                        <!-- Facebook -->
                                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                                            <label for="facebook" class="form-label"><b>Facebook</b></label>
                                            <input type="text" name="facebook" class="form-control"
                                                value="{{ @$dataSetting->facebook }}" placeholder="Masukkan Link">
                                        </div>

                                        <!-- Twitter -->
                                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                                            <label for="twitter" class="form-label"><b>Twitter</b></label>
                                            <input type="text" name="twitter" class="form-control"
                                                value="{{ @$dataSetting->twitter }}" placeholder="Masukkan Link">
                                        </div>

                                        <!-- TikTok -->
                                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                                            <label for="tiktok" class="form-label"><b>TikTok</b></label>
                                            <input type="text" name="tiktok" class="form-control"
                                                value="{{ @$dataSetting->tiktok }}" placeholder="Masukkan Link">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- SECTION 3: Setting Tautan Penting -->
                                <div class="mb-4">
                                    <h5 class="mb-3 text-dark"><i class="fa fa-link me-2"></i> Setting Tautan Penting
                                    </h5>
                                    <div class="row">
                                        <!-- Website UMS -->
                                        <div class="col-12 col-md-6 col-lg-6 mb-3">
                                            <label for="website_ums" class="form-label"><b>Website UMS</b></label>
                                            <input type="text" name="website_ums" class="form-control"
                                                value="{{ @$dataSetting->website_ums }}"
                                                placeholder="https://www.ums.ac.id">
                                        </div>

                                        <!-- Website BKUI -->
                                        <div class="col-12 col-md-6 col-lg-6 mb-3">
                                            <label for="website_bkui" class="form-label"><b>Website BKUI</b></label>
                                            <input type="text" name="website_bkui" class="form-control"
                                                value="{{ @$dataSetting->website_bkui }}"
                                                placeholder="https://bkui.ums.ac.id">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- SECTION 4: Setting Google Client Redirect -->
                                <div class="mb-4">
                                    <h5 class="mb-3 text-dark">
                                        <i class="fa-solid fa-code me-2"></i> Environment Website
                                    </h5>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="mode_website" class="form-label"><b>Mode Website</b></label>
                                            <select name="session" class="form-control" id="session">
                                                <option value="dev"
                                                    {{ @$dataConfig->keterangan == 'dev' ? 'selected' : '' }}>
                                                    Developer
                                                </option>
                                                <option value="prod"
                                                    {{ @$dataConfig->keterangan == 'prod' ? 'selected' : '' }}>
                                                    Production
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Simpan -->
                                <div class="bg-primary p-3 d-flex justify-content-end rounded-bottom">
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
