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
                                <a href="{{ route('recognition.dataAjuanSaya') }}" class="btn btn-danger btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
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
                                                                    class="form-select text-dark text-sm select2 w-100">
                                                                    <option value="">Pilih Fakultas</option>
                                                                    @foreach ($fakultas as $fak)
                                                                        <option value="{{ $fak->id }}"
                                                                            {{ @$dataRecognisi->faculty == $fak->id ? 'selected' : '' }}>
                                                                            {{ $fak->faculty }}
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
                                                                        <option value="{{ $prodi->depart }}"
                                                                            data-id_fac="{{ $prodi->id_fac }}"
                                                                            {{ @$dataRecognisi->department == $prodi->depart ? 'selected' : '' }}>
                                                                            {{ $prodi->depart }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Upload
                                                                Acceptance Form <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="acceptance_form" accept=".pdf"
                                                                    class="form-control custom-file-input">
                                                                <small class="text-danger" style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                @if (@$dataRecognisi->acceptance_form != null)
                                                                    <small style="font-size: 12px;"><a target="_blank"
                                                                            href="{{ asset('storage/' . @$dataRecognisi->acceptance_form) }}">Lihat
                                                                            Dokumen Sebelumnya</a></small><br>
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
                                                            <label class="col-sm-3 text-dark text-sm fw-bold">Nama Professor
                                                                <span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="text" class="form-control" id="nama_prof"
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
                                                                <input type="text" class="form-control" id="univ_asal"
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
                                                                <input type="text" class="form-control"
                                                                    id="bidang_kepakaran" name="bidang_kepakaran"
                                                                    placeholder="Tulis Bidang Kepakaran disini.."
                                                                    value="{{ @$dataRecognisi->bidang_kepakaran }}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <label class="col-sm-3 col-form-label fw-bold text-dark">Upload
                                                                CV
                                                                Professor<span class="text-danger">*</span></label>
                                                            <div class="col-sm-9">
                                                                <input type="file" name="cv_prof"
                                                                    class="form-control custom-file-input" accept=".pdf">
                                                                <small class="text-danger"
                                                                    style="font-size: 12px;">*Ukuran
                                                                    Maksimal
                                                                    Unggah
                                                                    File
                                                                    5Mb.</small><br>
                                                                @if (@$dataRecognisi->cv_prof != null)
                                                                    <small style="font-size: 12px;"><a target="_blank"
                                                                            href="{{ asset('storage/' . @$dataRecognisi->cv_prof) }}">Lihat
                                                                            Dokumen Sebelumnya</a></small><br>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="id_rec" id="id_rec" value="{{ @$id_rec }}">
                                <div class="bg-primary p-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-light">
                                        <i class="bx bx-send me-2"></i> Kirim
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
    <script>
        var getData = "{{ route('recognition.getData') }}"
    </script>
    <script>
        $(document).ready(function() {
            const $faculty = $('#faculty');
            const $departement = $('#departement');
            const allDepartementOptions = $departement.find('option');

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

            // Jalankan saat halaman load (jika dalam mode edit)
            if ($faculty.val()) {
                updateDepartementOptions($faculty.val());
            } else {
                $departement.html('<option value="">Pilih Program Studi</option>');
            }
        });
    </script>


    <script src="{{ asset('js/recognition/tambahLaporan.js') }}"></script>
@endpush
