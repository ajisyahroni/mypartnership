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
                                <a href="{{ route('potential_partner.activity') }}" class="btn btn-danger">Kembali</a>

                            </div>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('potential_partner.store') }}" method="post" id="formInput"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ $dataActivity->id }}">
                                @if ($dataActivity->status == 'revisi')
                                        <div class="alert alert-danger d-flex align-items-center p-3" role="alert"
                                            style="background-color: #f8cdcd; border-left: 6px solid #f33232;">
                                            <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                                style="background-color: #f33232; width: 30px; height: 30px; border-radius: 4px;">
                                                <i class="bx bx-info-circle"></i>
                                            </div>
                                            <div class="text-dark">
                                                <span><b>Catatan Admin:</b></span><br>
                                                <div>{{ $dataActivity->revisi ?? '' }}</div>
                                            </div>
                                        </div>
                                    @endif
                                <div class="accordion custom-accordion" id="customAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse1" aria-expanded="true">
                                                INFORMASI PERSONAL
                                            </button>
                                        </h2>
                                        <div id="collapse1" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Nama<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="name" class="form-control"
                                                                    placeholder="Masukkan Nama"
                                                                    value="{{ $dataActivity->name }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Email<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="email" class="form-control"
                                                                    placeholder="Masukkan Email"
                                                                    value="{{ $dataActivity->email }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Jabatan<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="occupation" class="form-control"
                                                                    placeholder="Masukkan Jabatan"
                                                                    value="{{ $dataActivity->occupation }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Nomor
                                                                HP<span style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="number" name="phonenumber"
                                                                    class="form-control" placeholder="Masukkan Nomor HP"
                                                                    value="{{ $dataActivity->phonenumber }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Sosial
                                                                Media</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="socmed" class="form-control"
                                                                    placeholder="Masukkan Sosial Media"
                                                                    value="{{ $dataActivity->socmed }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Minat
                                                                Penelitian<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="researchint"
                                                                    class="form-control"
                                                                    placeholder="Masukkan Minat Penelitian"
                                                                    value="{{ $dataActivity->researchint }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse2" aria-expanded="true">
                                                LOKASI MITRA
                                            </button>
                                        </h2>
                                        <div id="collapse2" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Institusi<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="institution"
                                                                    class="form-control" placeholder="Masukkan Institusi"
                                                                    value="{{ $dataActivity->institution }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Negara<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <select class="form-control select2" name="country"
                                                                    id="country">
                                                                    <option value="">Pilih Negara</option>
                                                                    @foreach ($country as $item)
                                                                        <option value="{{ $item->id }}"
                                                                            {{ $item->id == $dataActivity->country ? 'selected' : '' }}>
                                                                            {{ $item->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Website</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="website" class="form-control"
                                                                    placeholder="Masukkan Website"
                                                                    value="{{ $dataActivity->website }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label
                                                                class="col-sm-3 col-form-label fw-bold text-dark">Alamat</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="address" class="form-control"
                                                                    placeholder="Masukkan Alamat"
                                                                    value="{{ $dataActivity->address }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse3" aria-expanded="true">
                                                UNGGAH GAMBAR
                                            </button>
                                        </h2>
                                        <div id="collapse3" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Unggah
                                                                Kartu Nama bagian Depan<span
                                                                    style="font-size: 12px;color:red;">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="cardname1"
                                                                    class="form-control custom-file-input"
                                                                    placeholder="Upload File" accept="image/*">

                                                                @if ($dataActivity->cardname1)
                                                                    <!-- Preview Gambar Lama -->
                                                                    <div class="mb-2">
                                                                        <small class="text-muted d-block mb-1">Preview
                                                                            sebelumnya:</small>
                                                                        <img id="preview-cardname1"
                                                                            src="{{ asset('storage/' . $dataActivity->cardname1) }}"
                                                                            alt="Cardname Side A" class="img-thumbnail"
                                                                            style="max-height: 200px;">
                                                                    </div>
                                                                @endif

                                                                <small class="text-danger"
                                                                    style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                <small class="text-danger" style="font-size: 12px;">*File
                                                                    ber
                                                                    Berformat
                                                                    jpg, jpeg, png.</small><br>
                                                                @if (count(@$logFileCN1) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                        data-log="{{ json_encode(@$logFileCN1) }}">Log
                                                                        Draft</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Unggah
                                                                Kartu Nama bagian Belakang</label>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="cardname2"
                                                                    class="form-control custom-file-input"
                                                                    placeholder="Upload File" accept="image/*">
                                                                @if ($dataActivity->cardname2)
                                                                    <!-- Preview Gambar Lama -->
                                                                    <div class="mb-2">
                                                                        <small class="text-muted d-block mb-1">Preview
                                                                            sebelumnya:</small>
                                                                        <img id="preview-cardname2"
                                                                            src="{{ asset('storage/' . $dataActivity->cardname2) }}"
                                                                            alt="Cardname Side A" class="img-thumbnail"
                                                                            style="max-height: 200px;">
                                                                    </div>
                                                                @endif
                                                                <small class="text-danger"
                                                                    style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                <small class="text-danger" style="font-size: 12px;">*File
                                                                    ber
                                                                    Berformat
                                                                    jpg, jpeg, png.</small><br>
                                                                @if (count(@$logFileCN2) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                        data-log="{{ json_encode(@$logFileCN2) }}">Log
                                                                        Draft</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="partner_key" id="partner_key" value="{{ session('partner_key') }}">
                                <div class="bg-primary p-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa fa-save me-2"></i> Update
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
    <script>
        var getData = "{{ route('potential_partner.getDataActivity') }}";
        let role = @json(session('current_role'));
    </script>
    <script src="{{ asset('js/potential_partner/activity.js') }}"></script>
@endpush
