
<!-- Tab Content -->
<div class="row g-3">
    <div class="col-lg-8 col-12">
        <ul class="nav nav-tabs" id="myTabVerifikasi" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="modal-tab-verifikasi" data-bs-toggle="tab" data-bs-target="#modal" type="button"
                    role="tab" aria-controls="modal" aria-selected="true">
                    Detail Kerja Sama
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dokumen-verifikasi-tab" data-bs-toggle="tab" data-bs-target="#dokumen-verifikasi" type="button"
                    role="tab" aria-controls="dokumen-verifikasi" aria-selected="false">
                    Lihat Dokumen Kerja Sama
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="myTabVerifikasiContent">
            <div class="tab-pane fade  show active" id="modal" role="tabpanel" aria-labelledby="modal-tab-verifikasi">
                <div id="modal_read" style="max-height: 450px; overflow-y:auto; padding-right: 5px;">
                    <table class="table table-bordered table-hover" id="detailKerma" style="font-size: 12px;">
                        <tbody>
                            <tr>
                                <td style="white-space:nowrap;"><b>Status Ajuan :</b></td>
                                <td>
                                    <b>{{ @$dataPengajuan->stats_kerma }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Kerja Sama Tingkat :</b></td>
                                <td>
                                    <b>{{ @$dataPengajuan->prodi_unit }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Jenis Kerja Sama :</b></td>
                                <td>
                                    {{ @$dataPengajuan->jenis_kerjasama }} </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Nomor Dokumen :</b></td>
                                <td>
                                    {{ @$dataPengajuan->no_dokumen }} </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Kerja Sama :</b></td>
                                <td>
                                    {{ @$dataPengajuan->dn_ln }} </td>
                            </tr>
                            @if (@$dataPengajuan->dn_ln == 'Dalam Negeri')
                                <tr>
                                    <td style="white-space:nowrap;"><b>Wilayah Mitra :</b></td>
                                    <td>
                                        <b>{{ @$dataPengajuan->wilayah_mitra }}</b>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td style="white-space:nowrap;"><b>Negara Mitra :</b></td>
                                    <td>
                                        <b>{{ @$dataPengajuan->negara_mitra }}</b>
                                    </td>
                                </tr>
                            @endif
                            @if (@$dataPengajuan->jenis_institusi == 'Lain-lain')
                                <tr>
                                    <td style="white-space:nowrap;"><b>Jenis Institusi Mitra :</b></td>
                                    <td>
                                        {{ @$dataPengajuan->jenis_institusi_lain }} </td>
                                </tr>
                            @else
                                <tr>
                                    <td style="white-space:nowrap;"><b>Jenis Institusi Mitra :</b></td>
                                    <td>
                                        {{ @$dataPengajuan->jenis_institusi }} </td>
                                </tr>
                            @endif
                            <tr>
                                <td style="white-space:nowrap;"><b>Nama Institusi Mitra :</b></td>
                                <td><b>{{ @$dataPengajuan->nama_institusi }}</b></td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Alamat Institusi Mitra :</b></td>
                                <td>{{ @$dataPengajuan->alamat_mitra }}</td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Penandatangan Internal :</b></td>
                                <td>{{ @$dataPengajuan->ttd_internal }}</td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Jabatan Internal :</b></td>
                                <td>{{ @$dataPengajuan->lvl_internal }}</td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Penandatangan Eksternal :</b></td>
                                <td>{{ @$dataPengajuan->ttd_eksternal }}</td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Jabatan Eksternal :</b></td>
                                <td>{{ @$dataPengajuan->lvl_eksternal }}</td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap; vertical-align: top;"><b>PIC Kegiatan Internal:</b></td>
                                <td>
                                    <div><b>Nama:</b> {{ @$dataPengajuan->nama_internal_pic ?? '-' }}</div>
                                    <div><b>Jabatan:</b> {{ @$dataPengajuan->lvl_internal_pic ?? '-' }}</div>
                                    <div><b>No. Telp:</b> {{ @$dataPengajuan->telp_internal_pic ?? '-' }}</div>
                                    <div><b>Email:</b> {{ @$dataPengajuan->email_internal_pic ?? '-' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space: nowrap; vertical-align: top;"><b>PIC Kegiatan Mitra:</b></td>
                                <td>
                                    <div><b>Nama:</b> {{ @$dataPengajuan->nama_eksternal_pic ?? '-' }}</div>
                                    <div><b>Jabatan:</b> {{ @$dataPengajuan->lvl_eksternal_pic ?? '-' }}</div>
                                    <div><b>No. Telp:</b> {{ @$dataPengajuan->telp_eksternal_pic ?? '-' }}</div>
                                    <div><b>Email:</b> {{ @$dataPengajuan->email_eksternal_pic ?? '-' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Kontribusi Kerja Sama :</b></td>
                                @if (@$dataPengajuan->kontribusi != 'Lain-lain')
                                    <td>{{ @$dataPengajuan->kontribusi }}</td>
                                @else
                                    <td>{{ @$dataPengajuan->kontribusi_lain }}</td>
                                @endif
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Kerja Sama Mulai :</b></td>
                                <td>{{ Tanggal_Indo(@$dataPengajuan->mulai) }}</td>
                            </tr>
                            <tr>
                                <td style="white-space:nowrap;"><b>Kerja Sama Berakhir :</b></td>
                                <td>{{ Tanggal_Indo(@$dataPengajuan->selesai) }}</td>
                            </tr>

                            @if ($dataPengajuan->periode_kerma == 'notknown')
                                <tr>
                                    <td style="white-space:nowrap;"><b>Status Kerja Sama :</b></td>
                                    <td>{{ @$dataPengajuan->status_mou }}</td>
                                </tr>
                            @endif

                            @if ($dokumenPerpanjangan)
                                <tr>
                                    <td style="white-space:nowrap;"><b>Dokumen Perpanjangan:</b></td>
                                    <td>
                                        {{ $dokumenPerpanjangan->nama_institusi }}, Tanggal Selesai : {{ $dokumenPerpanjangan->selesai }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td style="white-space:nowrap;"><b>Status Kerja Sama:</b></td>
                                <td>
                                    {!! $dataPengajuan->statusPengajuanKerjaSama() !!}
                                    @if ($dataPengajuan->status_kerma)
                                        <span class="badge badge-sm bg-success" style="font-size: 10px!important;">{{ $dataPengajuan->status_kerma }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="dokumen-verifikasi" role="tabpanel" aria-labelledby="dokumen-verifikasi-tab">
                <div id="modal-read-dokumen" style="max-height: 450px; overflow-y:auto; padding-right: 5px;">
                    <table class="table table-bordered table-hover" style="font-size: 12px;">
                        <tbody>
                            <tr>
                                <td style="white-space:nowrap;"><b>Dokumen File SK Pendirian :</b></td>
                                <td>
                                    @if ($dataPengajuan->file_sk_mitra != null && $dataPengajuan->file_sk_mitra != '')
                                        <a href="{{ $fileUrlMitra }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                            data-title-tooltip="Unduh File Kerja Sama"
                                            data-original-data-title-tooltip="Unduh File  Kerja Sama"><i
                                                class="fa-solid fa-download"></i></a>
                                    @else
                                        Belum Ada Dokumen
                                    @endif
                                    <div class="iframe-mitra"></div>
                                </td>
                            </tr>
                            @if ($dataPengajuan->stats_kerma == 'Ajuan Baru')
                                <tr>
                                    <td style="white-space:nowrap;"><b>Dokumen Draft Kerja Sama :</b></td>
                                    <td>
                                        @if ($dataPengajuan->file_ajuan != null)
                                            <a href="{{ $fileUrlDraft }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                                data-title-tooltip="Unduh File Draft Kerja Sama"
                                                data-original-data-title-tooltip="Unduh File Draft Kerja Sama"><i
                                                    class="fa-solid fa-download"></i></a>
                                        @else
                                            Belum Ada Dokumen
                                        @endif
                                        <div class="iframe-draft"></div>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td style="white-space:nowrap;"><b>Dokumen  Kerja Sama :</b></td>
                                <td>
                                    @if ($dataPengajuan->file_mou != null)
                                        <a href="{{ $fileUrl }}" class="btn btn-primary btn-icon mb-2" target="_blank"
                                            data-title-tooltip="Unduh File Kerja Sama"
                                            data-original-data-title-tooltip="Unduh File  Kerja Sama"><i
                                                class="fa-solid fa-download"></i></a>
                                    @else
                                        Belum Ada Dokumen
                                    @endif
                                    <div class="iframe-mou"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
   <div class="col-lg-4 col-12">
        @php
            $showUploadDraft = ($fileUrlDraft == null 
                                && session('current_role') == 'admin' 
                                && @$dataPengajuan->stats_kerma == 'Ajuan Baru');
        @endphp
        {{-- === VERIFIKASI SECTION === --}}
       <div class="p-4 border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            {{-- Header Section --}}
            <div class="mb-2">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                        <i class="bi bi-clipboard-check text-light fs-5"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">
                        {{ $showUploadDraft ? 'Form Verifikasi & Upload Draft' : 'Form Verifikasi' }}
                    </h5>
                </div>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    Apakah Anda akan memverifikasi data ini?
                </p>
            </div>

            {{-- Alert Draft Belum Upload --}}
            @if ($showUploadDraft)
                <div class="alert alert-danger border-0 d-flex align-items-center gap-2 mb-3 rounded-3" style="font-size: 0.85rem;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>Draft dokumen kerja sama belum di-upload.</span>
                </div>

                {{-- Upload Draft Section --}}
                <div class="card border-0 shadow-sm rounded-3 mb-3">
                    <div class="card-body p-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="bi bi-cloud-upload text-primary me-1"></i>
                            Upload Draft Kerja Sama
                        </label>
                        <input type="file" name="file_ajuan" 
                            class="form-control form-control-sm rounded-3"
                            accept=".doc,.docx">
                        <div class="mt-2">
                            <small class="text-danger d-block" style="font-size: 0.75rem;">
                                <i class="bi bi-dot"></i> Ukuran maksimal file 5MB
                            </small>
                            <small class="text-danger d-block" style="font-size: 0.75rem;">
                                <i class="bi bi-dot"></i> Format file: .doc atau .docx
                            </small>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Catatan Section --}}
            <div class="mb-3 d-none" id="noteWrapper">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <label for="note" class="form-label fw-semibold mb-2">
                            <i class="bi bi-pencil-square text-warning me-1"></i>
                            Catatan
                        </label>
                        <textarea id="note" name="note" rows="4" 
                                class="form-control rounded-3"
                                placeholder="Tulis catatan Anda di sini..." 
                                style="font-size: 0.9rem;">{{ @$dataPengajuan->note }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 mb-3">
                <button data-status="1"
                        data-tipe="{{ $tipe ?? 'bidang' }}"
                        data-id_mou="{{ @$dataPengajuan->id_mou }}"
                        class="btn btn-success flex-fill d-flex align-items-center justify-content-center gap-2 rounded-pill shadow-sm btnActionVerifikasi">
                    <i class="bi bi-check-circle"></i>
                    <span class="fw-semibold">
                        {{ $showUploadDraft ? 'Verifikasi & Upload' : 'Verifikasi' }}
                    </span>
                </button>

                <button data-status="0"
                        data-tipe="{{ $tipe ?? 'bidang' }}"
                        data-id_mou="{{ @$dataPengajuan->id_mou }}"
                        class="btn btn-danger flex-fill d-flex align-items-center justify-content-center gap-2 rounded-pill shadow-sm btnActionVerifikasi">
                    <i class="bi bi-x-circle"></i>
                    <span class="fw-semibold">Tolak</span>
                </button>
            </div>

            {{-- Simpan Button --}}
            <button class="btn btn-primary w-100 d-none rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2" id="btnSimpanAksi">
                <i class="bi bi-save"></i>
                <span class="fw-semibold">Simpan</span>
            </button>

        </div>

        {{-- Optional: Add hover effects with inline style or in your CSS --}}
        <style>
            .btnActionVerifikasi:hover {
                transform: translateY(-2px);
                box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
                transition: all 0.3s ease;
            }
        </style>
    </div>

</div>

<script>
var fileUrlMoU = @json($fileUrl);
var fileUrlDraft = @json($fileUrlDraft);
var fileUrlMitra = @json($fileUrlMitra);
var urlVerifikasi = "/pengajuan/verifikasi";
var tokenVerifikasi = $('meta[name="csrf-token"]').attr("content");
var hasNote = @json($dataPengajuan->note ?? null);
var hasVerify = @json($hasVerify);
var hasNotVerify = @json($hasNotVerify);
var tipeVerifikasi = @json($tipe);

var addBy = @json($dataPengajuan->add_by);
var userName = @json(auth()->user()->username);

var roleVerify = @json(session('current_role'));

$(document).on("click", ".btnActionVerifikasi", function () {
    let status = $(this).data("status");
    let tipe = $(this).data("tipe");
    let id_mou = $(this).data("id_mou");

    // reset semua tombol
    $(".btnActionVerifikasi")
        .removeClass("btn-success btn-danger active")
        .addClass("btn-outline-success btn-outline-danger");

    // tampilkan tombol simpan
    $("#btnSimpanAksi").removeClass("d-none");

    if (status == "1") {
        // verifikasi → catatan disembunyikan
        $("#noteWrapper").addClass("d-none").find("#note").val("");

        $(this)
            .removeClass("btn-outline-success")
            .addClass("btn-success active");
    } else {
        // tolak → catatan muncul
        $("#noteWrapper").removeClass("d-none");

        $(this)
            .removeClass("btn-outline-danger")
            .addClass("btn-danger active");
    }

    // simpan status untuk submit
    $("#btnSimpanAksi").data("status", status);

    // sekaligus simpan tipe & id_mou (fix undefined)
    $("#btnSimpanAksi").data("tipe", tipe);
    $("#btnSimpanAksi").data("id_mou", id_mou);
});


$(document).ready(function(){

    function updateForm() {
        $(`.btnActionVerifikasi[data-status='1']`)
            .removeClass("btn-success active")
            .addClass("btn-outline-success");

        $(`.btnActionVerifikasi[data-status='0']`)
            .removeClass("btn-danger active")
            .addClass("btn-outline-danger");

        $("#noteWrapper").addClass("d-none");

        if (roleVerify === "admin") {
            if (tipeVerifikasi == 'bidang') {
                if (hasVerify["admin"]) {
                    $(`.btnActionVerifikasi[data-status='1']`)
                        .removeClass("btn-outline-success")
                        .addClass("btn-success active");
                } else if (hasNotVerify["admin"]) {
                    $("#noteWrapper").removeClass("d-none");
                    $(`.btnActionVerifikasi[data-status='0']`)
                        .removeClass("btn-outline-danger")
                        .addClass("btn-danger active");
                }
            }else if(tipeVerifikasi == 'dokumen'){
                if (hasVerify["publish"]) {
                    $(`.btnActionVerifikasi[data-status='1']`)
                        .removeClass("btn-outline-success")
                        .addClass("btn-success active");
                } else if (hasNotVerify["publish"]) {
                    $("#noteWrapper").removeClass("d-none");
                    $(`.btnActionVerifikasi[data-status='0']`)
                        .removeClass("btn-outline-danger")
                        .addClass("btn-danger active");
                }
            }
        }

        if (roleVerify === "verifikator") {
            if (hasVerify["kaprodi"] && addBy != userName) {
                $(`.btnActionVerifikasi[data-status='1']`)
                    .removeClass("btn-outline-success")
                    .addClass("btn-success active");
            } else if (hasNotVerify["kaprodi"]) {
                $("#noteWrapper").removeClass("d-none");
                $(`.btnActionVerifikasi[data-status='0']`)
                    .removeClass("btn-outline-danger")
                    .addClass("btn-danger active");
            }
        }

        if (roleVerify === "user") {
            if (hasVerify["user"]) {
                $(`.btnActionVerifikasi[data-status='1']`)
                    .removeClass("btn-outline-success")
                    .addClass("btn-success active");
            } else if (hasNotVerify["user"]) {
                $("#noteWrapper").removeClass("d-none");
                $(`.btnActionVerifikasi[data-status='0']`)
                    .removeClass("btn-outline-danger")
                    .addClass("btn-danger active");
            }
        }
    }


    function generatePreview(url) {
        
        if (!url || typeof url !== "string" || url.trim() === "") {
            return `<div class="alert alert-secondary text-center mt-3">Tidak ada file untuk ditampilkan.</div>`;
        }
        
        const ext = url.split(".").pop().toLowerCase();
        
        if (ext === "pdf") {
            if (url.startsWith("https://")) {
                return `<iframe
                    src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true"
                    style="width:100%;height:500px;border:none;">
                </iframe>`;
            }

            return `<iframe
                src="${url}"
                width="100%"
                height="500px"
                style="border:none;">
            </iframe>`;
        }


        if (["png", "jpg", "jpeg", "webp"].includes(ext)) {
            return `<div class="text-center"><img src="${url}" class="img-fluid" style="max-height:500px;"></div>`;
        }

        if (url.startsWith("/")) {
            url = BASE_URL + url;
        }

        if (["doc", "docx"].includes(ext)) {
            return `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true"
                        style="width:100%;height:500px;border:none;"></iframe>`;
        }

        return `<div class="alert alert-warning text-center mt-3">Format file <b>.${ext}</b> tidak didukung.</div>`;
    }

    $("#btnSimpanAksi").on("click", function () {
        let status = $(this).data("status");
        let tipe = $(this).data("tipe");    
        let id_mou = $(this).data("id_mou");

        verifikasi(status, tipe, id_mou);
    });

    function verifikasi(status, tipe, id_mou) {
        let title = "";
        let button = "";
        let note = $("#note").val();
        let fileAjuan = $("input[name='file_ajuan']")[0]?.files[0] ?? null;

        if (status == "1") {
            title = "Anda yakin ingin verifikasi data ini?";
            button = "Verifikasi";
        } else if (status == "0") {
            title = "Anda yakin ingin menolak verifikasi data ini?";
            button = "Tolak";
        }

        Swal.fire({
            text: title,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, " + button,
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading(button + " data...");

                let formData = new FormData();
                formData.append("_token", tokenVerifikasi);
                formData.append("status", status);
                formData.append("tipe", tipe);
                formData.append("id_mou", id_mou);
                formData.append("note", note);

                if (fileAjuan) {
                    formData.append("file_ajuan", fileAjuan);
                }

                $.ajax({
                    url: urlVerifikasi,
                    type: "POST",
                    data: formData,
                    contentType: false,   // wajib untuk upload file
                    processData: false,   // wajib untuk upload file
                    cache: false,
                    success: (res) => {
                        if (res.status) {
                            Swal.fire("Berhasil!", res.message, "success");
                            $("#modal-verifikasi").modal('hide');

                            if (window.table) {
                                window.table.ajax.reload(null, false);
                            }

                            // if (res.$status == '1') {
                            //     $.ajax({
                            //         url: pengajuanSendEmail,
                            //         method: "POST",
                            //         data: { _token: tokenVerifikasi },
                            //     });
                            // }
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal Menyimpan",
                                html: errorMessages || "Terjadi Kesalahan.",
                            });
                        }
                    },
                    error: (xhr) => {
                        let errorMessages = "";
                        if (xhr.responseJSON?.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(
                                (messages) => {
                                    errorMessages += messages.join("<br>") + "<br>";
                                }
                            );
                        }

                        Swal.fire({
                            icon: "error",
                            title: "Gagal Menyimpan",
                            html: errorMessages || "Terjadi Kesalahan.",
                        });
                    },
                });
            }
        });
    }

    updateForm();
    $(".iframe-mou").html( generatePreview(fileUrlMoU) );
    $(".iframe-draft").html( generatePreview(fileUrlDraft) );
    $(".iframe-mitra").html( generatePreview(fileUrlMitra) );


});
</script>


        
