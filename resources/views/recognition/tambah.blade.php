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
                                <a href="{{ session('current_role') == 'admin' ? route('recognition.InboundStaffRecognition') : route('recognition.dataAjuanSaya') }}"
                                    class="btn btn-danger btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (@$dataRecognisi->status_verify_kaprodi == '0' && @$dataRecognisi->revisi_kaprodi != null)
                                <div class="alert alert-danger d-flex align-items-center p-3" role="alert"
                                    style="background-color: #ffd5d3; border-left: 6px solid #d42a00;">
                                    <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                        style="background-color: #d42a00; width: 30px; height: 30px; border-radius: 4px;">
                                        <i class="bx bx-info-circle"></i>
                                    </div>
                                    <div class="text-dark small">
                                        <span><b>Revisi dari Kaprodi:</b></span><br>
                                        {{ $dataRecognisi->revisi_kaprodi }}
                                    </div>
                                </div>
                            @elseif (@$dataRecognisi->status_verify_admin == '0' && @$dataRecognisi->revisi_admin != null)
                                <div class="alert alert-danger d-flex align-items-center p-3" role="alert"
                                    style="background-color: #ffd5d3; border-left: 6px solid #d42a00;">
                                    <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                        style="background-color: #d42a00; width: 30px; height: 30px; border-radius: 4px;">
                                        <i class="bx bx-info-circle"></i>
                                    </div>
                                    <div class="text-dark small">
                                        <span><b>Revisi dari Admin:</b></span><br>
                                        {{ $dataRecognisi->revisi_admin }}
                                    </div>
                                </div>
                            @endif
                            <div class="alert alert-info d-flex align-items-center p-3" role="alert"
                                style="background-color: #e6faff; border-left: 6px solid #00bcd4;">
                                <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                    style="background-color: #00bcd4; width: 30px; height: 30px; border-radius: 4px;">
                                    <i class="bx bx-info-circle"></i>
                                </div>
                                <div class="text-dark small">
                                    Setelah mengisi form ajuan, kami <b>(BKUI)</b> akan segera membuatkan Surat Tugas/SK dan
                                    bisa di-download setelah proses pembuatan surat selesai di kolom <b>File Surat
                                        Tugas/SK</b>.
                                </div>
                            </div>
                            <form action="{{ route('recognition.store') }}" enctype="multipart/form-data" method="post"
                                id="formInput">
                                @csrf
                                <div class="accordion custom-accordion" id="customAccordion">
                                    <!-- Detail Dokumen Kerja Sama -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="true">
                                                <i class="fa-solid fa-building me-2"></i> Informasi Prodi Pengusul
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <div class="row mb-3 align-items-center ">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                Fakultas <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <select id="faculty" name="faculty"
                                                                    class="form-select text-dark text-sm select2 w-100" {{ @$readonly }}>
                                                                    <option value="">Pilih Fakultas</option>
                                                                    @foreach ($fakultas as $fak)
                                                                        <option value="{{ $fak->id_lmbg }}"
                                                                            data-nama_lmbg = "{{ $fak->nama_lmbg }}"
                                                                            data-id_lmbg = "{{ $fak->id_lmbg }}"
                                                                            {{ @$dataRecognisi->faculty == $fak->id_lmbg ? 'selected' : (@$fakultasUser == $fak->id_lmbg ? 'selected' : '') }}>
                                                                            {{ $fak->nama_lmbg }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3 align-items-center">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">
                                                                Program Studi <span class="text-danger">*</span>
                                                            </label>
                                                            <div class="col-sm-9">
                                                                <select id="departement" name="departement"
                                                                    class="form-select text-dark text-sm select2 w-100">
                                                                    <option value="">Pilih Program Studi</option>
                                                                    @foreach ($program_studi as $prodi)
                                                                        <option value="{{ $prodi->nama_lmbg }}"
                                                                            data-id_fac="{{ $prodi->place_state }}"
                                                                            {{ @$dataRecognisi->department == $prodi->nama_lmbg ? 'selected' : (@$prodiUser == $prodi->nama_lmbg ? 'selected' : '') }}>
                                                                            {{ $prodi->nama_lmbg }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-sm-3">
                                                                <label class="col-form-label fw-bold text-dark">Upload
                                                                    Acceptance Form <span
                                                                        class="text-danger">*</span></label><br>
                                                                @if (count(@$logFileAF) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                        data-log="{{ json_encode(@$logFileAF) }}">Log
                                                                        Draft</button>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-9">
                                                                @if (@$dataRecognisi->status_verify_admin == 1)
                                                                    <a href="{{ getDocumentUrl(@$dataRecognisi->acceptance_form, 'file_rekognisi') }}" href="_blank" class="btn btn-primary"><i class="fa fa-download me-2"></i> Download File</a>
                                                                @else
                                                                    <input type="file" name="acceptance_form"
                                                                        class="form-control custom-file-input" accept=".pdf">
                                                                    <small class="text-danger" style="font-size: 12px;">*Ukuran
                                                                        Maksimal
                                                                        Unggah
                                                                        File
                                                                        5Mb.</small><br>
                                                                    <small class="text-danger"
                                                                        style="font-size: 12px;">*File Berformat
                                                                        PDF.</small><br>
                                                                    @if (@$dataRecognisi->acceptance_form != null)
                                                                        <small style="font-size: 12px;"><a target="_blank"
                                                                                href="{{ getDocumentUrl(@$dataRecognisi->acceptance_form, 'file_rekognisi') }}">Lihat
                                                                                Dokumen Sebelumnya</a></small><br>
                                                                    @endif
                                                                @endif
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
                                                <i class="fa-solid fa-calendar me-2"></i> Informasi Rekrutmen Adjunct
                                                Professor
                                            </button>
                                        </h2>
                                        <div id="collapse2" class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="card mb-3">
                                                    <div class="card-body">
                                                        <div class="row mb-3 align-items-center">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">Nama
                                                                Professor
                                                                <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="nama_prof" {{ @$readonly }}
                                                                    name="nama_prof"
                                                                    placeholder="Tulis Nama Professor disini.."
                                                                    value="{{ @$dataRecognisi->nama_prof }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3 align-items-center">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">Asal
                                                                Universitas
                                                                <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="univ_asal" {{ @$readonly }}
                                                                    name="univ_asal"
                                                                    placeholder="Tulis Asal Universitas disini.."
                                                                    value="{{ @$dataRecognisi->univ_asal }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3 align-items-center">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">Bidang
                                                                Kepakaran
                                                                <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" {{ @$readonly }}
                                                                    id="bidang_kepakaran" name="bidang_kepakaran"
                                                                    placeholder="Tulis Bidang Kepakaran disini.."
                                                                    value="{{ @$dataRecognisi->bidang_kepakaran }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3 align-items-center">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">Tanggal Mulai SK
                                                                <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="date" id="tanggal_mulai" {{ @$readonly }}
                                                                        name="mulai" class=""
                                                                        placeholder="Pilih Tanggal Mulai">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3 align-items-center">
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">Tanggal Selesai SK
                                                                <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="date" id="tanggal_selesai" {{ @$readonly }}
                                                                        name="selesai" class=""
                                                                        placeholder="Pilih Tanggal selesai">
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row mb-3">
                                                            <div class="col-sm-3">
                                                                <label class="col-form-label fw-bold text-dark">Upload
                                                                    CV
                                                                    Professor<span class="text-danger">*</span></label><br>
                                                                @if (count(@$logFileCV) > 0)
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                        data-log="{{ json_encode(@$logFileCV) }}">Log
                                                                        Draft</button>
                                                                @endif
                                                            </div>
                                                            <div class="col-sm-9">
                                                                 @if (@$dataRecognisi->status_verify_admin == 1)
                                                                    <a href="{{ getDocumentUrl(@$dataRecognisi->cv_prof, 'file_rekognisi') }}" href="_blank" class="btn btn-primary"><i class="fa fa-download me-2"></i> Download File</a>
                                                                @else
                                                                    <input type="file" name="cv_prof"
                                                                        class="form-control custom-file-input" accept=".pdf">
                                                                    <small class="text-danger" style="font-size: 12px;">*Ukuran
                                                                        Maksimal
                                                                        Unggah
                                                                        File
                                                                        5Mb.</small><br>
                                                                    <small class="text-danger"
                                                                        style="font-size: 12px;">*File Berformat
                                                                        PDF.</small><br>
                                                                    @if (@$dataRecognisi->cv_prof != null)
                                                                        <small style="font-size: 12px;"><a target="_blank"
                                                                                href="{{ getDocumentUrl(@$dataRecognisi->cv_prof, 'file_rekognisi') }}">Lihat
                                                                                Dokumen Sebelumnya</a></small><br>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (@$dataRecognisi->status_verify_admin == '1')
                                        @php
                                            $role = session('current_role');
                                            if ($role == 'admin') {
                                            $titleFile = 'Unggah File SK';
                                            }else{
                                                $titleFile = 'Unggah File Bukti Pelaksanaan';

                                            }
                                        @endphp
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse3" aria-expanded="true">
                                                    <i class="fa-solid fa-calendar me-2"></i> {{ $titleFile }}
                                                </button>
                                            </h2>
                                            <div id="collapse3" class="accordion-collapse collapse show">
                                                <div class="accordion-body">
                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            @if ($role == 'admin')
                                                                <div class="row mb-3">
                                                                    <div class="col-sm-3">
                                                                        <label class="col-form-label fw-bold text-dark">Upload
                                                                            File SK<span class="text-danger">*</span></label><br>
                                                                        @if (count(@$logFileSK) > 0)
                                                                            <button type="button"
                                                                                class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                                data-log="{{ json_encode(@$logFileSK) }}">Log
                                                                                Draft</button>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-sm-9">
                                                                        <input type="file" name="file_sk"
                                                                            class="form-control custom-file-input" accept=".pdf">
                                                                        <small class="text-danger"
                                                                            style="font-size: 12px;">*Ukuran
                                                                            Maksimal
                                                                            Unggah
                                                                            File
                                                                            5Mb.</small><br>
                                                                        @if (@$dataRecognisi->file_sk != null)
                                                                            <small style="font-size: 12px;"><a target="_blank"
                                                                                    href="{{ getDocumentUrl(@$dataRecognisi->file_sk, 'file_rekognisi') }}">Lihat
                                                                                    Dokumen Sebelumnya</a></small><br>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @else
                                                             <div class="row mb-3">
                                                                    <div class="col-sm-3">
                                                                        <label class="col-form-label fw-bold text-dark">Lihat
                                                                            File SK<span class="text-danger">*</span></label><br>
                                                                        @if (count(@$logFileSK) > 0)
                                                                            <button type="button"
                                                                                class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                                data-log="{{ json_encode(@$logFileSK) }}">Log
                                                                                Draft</button>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-sm-9">
                                                                        @if (@$dataRecognisi->file_sk != null)
                                                                            <a href="{{ getDocumentUrl(@$dataRecognisi->file_sk, 'file_rekognisi') }}" href="_blank" class="btn btn-primary"><i class="fa fa-download me-2"></i> Download File</a>
                                                                        @else
                                                                            <div class="alert alert-danger w-100 d-flex justify-content-center align-items-center">
                                                                                <span>File Belum diupload Admin</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if (@$dataRecognisi->add_by == Auth::user()->username)
                                                                <div class="row mb-3">
                                                                    <div class="col-sm-3">
                                                                        <label class="col-form-label fw-bold text-dark">Upload
                                                                            File Bukti Pelaksanaan<span class="text-danger">*</span></label><br>
                                                                        @if (count(@$logFileBP) > 0)
                                                                            <button type="button"
                                                                                class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                                data-log="{{ json_encode(@$logFileBP) }}">Log
                                                                                Draft</button>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-sm-9">
                                                                        <input type="file" name="bukti_pelaksanaan"
                                                                            class="form-control custom-file-input" accept=".pdf">
                                                                        <small class="text-danger"
                                                                            style="font-size: 12px;">*Ukuran
                                                                            Maksimal
                                                                            Unggah
                                                                            File
                                                                            5Mb.</small><br>
                                                                        @if (@$dataRecognisi->bukti_pelaksanaan != null)
                                                                            <small style="font-size: 12px;"><a target="_blank"
                                                                                    href="{{ getDocumentUrl(@$dataRecognisi->bukti_pelaksanaan, 'file_rekognisi') }}">Lihat
                                                                                    Dokumen Sebelumnya</a></small><br>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                             @else
                                                             <div class="row mb-3">
                                                                    <div class="col-sm-3">
                                                                        <label class="col-form-label fw-bold text-dark">Lihat
                                                                            File Bukti Pelaksanaan<span class="text-danger">*</span></label><br>
                                                                        @if (count(@$logFileBP) > 0)
                                                                            <button type="button"
                                                                                class="btn btn-outline-primary btn-sm btn-detail-log"
                                                                                data-log="{{ json_encode(@$logFileBP) }}">Log
                                                                                Draft</button>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-sm-9">
                                                                        @if (@$dataRecognisi->bukti_pelaksanaan != null)
                                                                            <a href="{{ getDocumentUrl(@$dataRecognisi->bukti_pelaksanaan, 'file_rekognisi') }}" href="_blank" class="btn btn-primary"><i class="fa fa-download me-2"></i> Download File</a>
                                                                        @else
                                                                            <div class="alert alert-danger w-100 d-flex justify-content-center align-items-center">
                                                                                <span>File Belum diupload Pengusul</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                     @endif
                                </div>
                                <input type="hidden" name="id_rec" id="id_rec" value="{{ @$id_rec }}">
                                <input type="hidden" name="rekognisi_key" id="rekognisi_key" value="{{ session('rekognisi_key') }}">
                                <div class="bg-primary p-3 d-flex justify-content-end">
                                    @if (@$dataRecognisi->id_rec)
                                        <button type="submit" class="btn btn-warning btn-simpan">
                                            <i class="bx bx-send me-2"></i> Update
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-light btn-simpan">
                                            <i class="bx bx-send me-2"></i> Kirim
                                        </button>
                                    @endif
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
    <script>
        var getData = "{{ route('recognition.getData') }}"
        var hasVerify = @json($hasVerify);
    </script>
    <script>
        $(document).ready(function() {
            let mulai = @json(@$dataRecognisi->mulai);
            let selesai = @json(@$dataRecognisi->selesai);
            
            const tanggalMulai = flatpickr("#tanggal_mulai", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                locale: "id",
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    // Atur minDate tanggal selesai berdasarkan tanggal mulai
                    tanggalSelesai.set('minDate', selectedDates[0]);
                }
            });

            const tanggalSelesai = flatpickr("#tanggal_selesai", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                locale: "id",
                allowInput: true
            });

            if (hasVerify) {
                $("#formInput")
                    .find("input, select, textarea, button, a")
                    .not('input[name="_token"], input[name="file_sk"], input[name="bukti_pelaksanaan"], .btn-simpan, .btn-detail-log, #id_rec, #rekognisi_key')
                    .prop("disabled", true);
            }

            if (mulai) {
                tanggalMulai.setDate(mulai, true);   // true = triggerOnChange
                tanggalSelesai.set("minDate", mulai); // Lock minimal tanggal selesai
            }

            if (selesai) {
                tanggalSelesai.setDate(selesai, true);
            }
        });

        $(document).ready(function() {
            const $faculty = $('#faculty');
            const $departement = $('#departement');
            const allFacultyOptions = $faculty.find('option');
            const allDepartementOptions = $departement.find('option');
            const prodiUser = @json(@$dataRecognisi->department) != null ? @json(@$dataRecognisi->department) : (@json(@$prodiUser) != null ? @json(@$prodiUser) : '' );
            const fakultasUser = @json(@$dataRecognisi->faculty) != null ? @json(@$dataRecognisi->faculty) : (@json(@$fakultasUser) != null ? @json(@$fakultasUser) : '' );


            function updateDepartementOptions(facultyId) {
                let filteredOptions = '<option value="">Pilih Program Studi</option>';

                if (facultyId) {
                    allDepartementOptions.each(function() {
                        const $opt = $(this);
                        const facId = $opt.data('id_fac');
                        const isSelected = $opt.is(':selected');

                        if (facId == facultyId) {
                            filteredOptions +=
                                `<option value="${$opt.val()}" ${isSelected ? 'selected' : ''}>${$opt.text()}</option>`;
                        }
                    });
                }

                $departement.html(filteredOptions);
            }
            

            $faculty.on('change', function() {
                const selectedFaculty = $(this).val();
                updateDepartementOptions(selectedFaculty);
            });

            if ($faculty.val()) {
                updateDepartementOptions($faculty.val());
            } else {
                $departement.html('<option value="">Pilih Program Studi</option>');
            }
            
            if (fakultasUser) {
                $faculty.val(fakultasUser).trigger('change');
            }
            if (prodiUser) {
                $departement.val(prodiUser).trigger('change');
            }
        });
    </script>


    <script src="{{ asset('js/recognition/tambah.js') }}"></script>
@endpush
