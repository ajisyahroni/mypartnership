<div class="table-responsive">
    <table class="table table-bordered table-hover" id="detailKerma" style="font-size: 12px; table-layout: fixed;">
        <tbody>
            <tr>
                <td style="white-space:nowrap; width: 30%;"><b>Pengusul :</b></td>
                <td>
                    <b>Username : </b> {{ @$dataRecognition->add_by }} <br>
                    <b>Nama : </b> {{ @$dataRecognition->nama_pengusul }}
                </td>
            </tr>
            <tr>
                <td><b>Prodi Pengusul :</b></td>
                <td>{{ @$dataRecognition->department }}</td>
            </tr>
            <tr>
                <td><b>Nama Professor :</b></td>
                <td>{{ @$dataRecognition->nama_prof }}</td>
            </tr>
            <tr>
                <td><b>Asal Universitas :</b></td>
                <td>{{ @$dataRecognition->univ_asal }}</td>
            </tr>
            <tr>
                <td><b>Bidang Kepakaran :</b></td>
                <td><b>{{ @$dataRecognition->bidang_kepakaran }}</b></td>
            </tr>
            <tr>
                <td><b>Tanggal SK :</b></td>
                <td><b> {!! @$dataRecognition->tanggal_sk_label !!}</b></td>
            </tr>
            <tr>
                <td><b>Tanggal Ajuan :</b></td>
                <td><b>{!! @$dataRecognition->timestamp_ajuan_label !!}</b></td>
            </tr>
            <tr>
                <td><b>Tanggal Selesai :</b></td>
                <td><b>{!! @$dataRecognition->timestamp_selesai_label !!}</b></td>
            </tr>

            {{-- FILE ACCEPTANCE FORM --}}
            <tr>
                <td><b>File Acceptance Form : </b></td>
                <td>
                    @if (!empty($dataRecognition->acceptance_form))
                        <a href="{{ getDocumentUrl(@$dataRecognition->acceptance_form, 'file_rekognisi') }}"
                            class="btn btn-sm btn-primary mb-2" target="_blank"><i class="bx bx-download"></i> Download</a>
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ $fileUrlAF }}"
                                class="w-100" style="border: none;"></iframe>
                        </div>
                    @else
                        <div class="alert alert-danger w-100 text-center mt-3 mb-0">
                            Belum Upload File
                        </div>
                    @endif
                </td>
            </tr>

            {{-- FILE CV PROFESSOR --}}
            <tr>
                <td><b>File CV Professor :</b></td>
                <td>
                    @if (!empty($dataRecognition->cv_prof))
                        <a href="{{ getDocumentUrl(@$dataRecognition->cv_prof, 'file_rekognisi') }}"
                            class="btn btn-sm btn-primary mb-2" target="_blank"><i class="bx bx-download"></i>
                            Download</a>
                        <div class="ratio ratio-16x9">
                            {{-- <iframe src="{{ getDocumentUrl(@$dataRecognition->cv_prof, 'file_rekognisi') }}"
                                class="w-100" style="border: none;"></iframe> --}}
                            <iframe src="{{ $fileUrlCV }}"
                                class="w-100" style="border: none;"></iframe>
                        </div>
                    @else
                        <div class="alert alert-danger w-100 text-center mt-3 mb-0">
                            Belum Upload File
                        </div>
                    @endif
                </td>
            </tr>

            {{-- FILE BUKTI PELAKSANAAN --}}
            <tr>
                <td><b>File Bukti Pelaksanaan :</b></td>
                <td>
                    @if (!empty($dataRecognition->bukti_pelaksanaan))
                        <a href="{{ getDocumentUrl(@$dataRecognition->bukti_pelaksanaan, 'file_rekognisi') }}"
                            class="btn btn-sm btn-primary mb-2" target="_blank"><i class="bx bx-download"></i>
                            Download</a>
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ $fileUrlBP }}"
                                class="w-100" style="border: none;"></iframe>
                        </div>
                    @else
                        <div class="alert alert-danger w-100 text-center mt-3 mb-0">
                            Belum Upload File
                        </div>
                    @endif
                </td>
            </tr>

            {{-- FILE SK --}}
            <tr>
                <td><b>File SK :</b></td>
                <td>
                    @if (!empty($dataRecognition->file_sk))
                        <a href="{{ getDocumentUrl(@$dataRecognition->file_sk, 'file_sk') }}"
                            class="btn btn-sm btn-primary mb-2" target="_blank"><i class="bx bx-download"></i>
                            Download</a>
                        <div class="ratio ratio-16x9">
                                <iframe src="{{ $fileUrlSK }}"
                                class="w-100" style="border: none;"></iframe>
                        </div>
                    @else
                        <div class="alert alert-danger w-100 text-center mt-3 mb-0">
                            Belum Upload File
                        </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
