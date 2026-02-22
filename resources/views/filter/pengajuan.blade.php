<div class="collapse mt-3 mb-3 px-5" id="filterPengajuan">
    <div class="filter-box">
        <div class="py-3">
            <h5><i class="fas fa-filter"></i> Filter Options</h5>
        </div>
        <form action="{{ route('pengajuan.getData') }}" method="get" id="formFilterPengajuan">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-building"></i> Nama
                        Institusi</label>
                    <select class="form-select" name="nama_institusi" id="nama_institusi">
                        {!! $filterPengajuan['institusi'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-level-up-alt"></i>
                        Tingkat Kerja
                        Sama</label>
                    <select class="form-select select2" name="tingkat_kerjasama" id="tingkat_kerjasama">
                        {{-- <option value="">Pilih Tingkat</option> --}}
                        {!! $filterPengajuan['tingkat_kerjasama'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-graduation-cap"></i>
                        Prodi / Unit
                        / Fakultas</label>
                    <select class="form-select select2" name="lembaga_ums" id="select2_lembaga">
                        {!! $filterPengajuan['lembaga'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="bx bx-globe"></i>
                        Lingkup</label>
                    <select class="form-select select2" name="dn_ln" id="dn_ln">
                        {!! $filterPengajuan['dn_ln'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-none" id="negara_wrapper">
                    <label class="form-label"><i class="bx bx-globe"></i>
                        Negara Mitra</label>
                    <select class="form-select select2" name="negara_mitra" id="negara_mitra">
                        {!! $filterPengajuan['negara'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-none" id="wilayah_wrapper">
                    <label class="form-label"><i class="bx bx-globe"></i>
                        Wilayah Mitra</label>
                    <select class="form-select select2" name="wilayah_mitra" id="wilayah_mitra">
                        {!! $filterPengajuan['wilayah_mitra'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-check-user"></i>
                        Status Verifikasi</label>
                    <select class="form-select select2" name="status_verifikasi" id="status_verifikasi">
                        {!! $filterPengajuan['status_verifikasi'] !!}
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-handshake"></i> Jenis
                        Kerja
                        Sama</label>
                    <select class="form-select select2" name="jenis_dokumen" id="jenis_dokumen">
                        {{-- <option value="">Pilih Jenis</option> --}}
                        {!! $filterPengajuan['jenis_dokumen'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-university"></i>
                        Jenis Institusi
                        Mitra</label>
                    <select class="form-select select2" name="jenis_institusi_mitra" id="jenis_institusi_mitra">
                        {{-- <option value="">Pilih Institusi</option> --}}
                        {!! $filterPengajuan['jenis_institusi_mitra'] !!}
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-calendar-alt"></i>
                        Tahun Mulai
                        Kerja Sama</label>
                    <select class="form-select select2" name="tahun" id="tahun">
                        {!! $filterPengajuan['tahun'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-handshake"></i>
                        Status Kerja Sama</label>
                    <select class="form-select select2" name="stats_kerma" id="stats_kerma">
                        {!! $filterPengajuan['stats_kerma'] !!}
                    </select>
                </div>
                {{-- <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-file-circle-check"></i>
                        Status Dokumen</label>
                    <select class="form-select select2" name="status_dokumen" id="status_dokumen">
                        {!! $filterPengajuan['status_dokumen'] !!}
                    </select>
                </div> --}}
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-danger btn-sm me-3 btn-reset-pengajuan"><i
                        class="bx bx-reset"></i> Reset
                    Filter</button>
                <button type="submit" class="btn btn-success btn-sm"><i class="bx bx-check"></i> Apply Filter</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        let inputs = {
            nama_institusi: $("#nama_institusi"),
            dn_ln: $("#dn_ln"),
            tingkat_kerjasama: $("#tingkat_kerjasama"),
            negara_mitra: $("#negara_mitra"),
            wilayah_mitra: $("#wilayah_mitra"),
            status: $("#status"),
            status_verifikasi: $("#status_verifikasi"),
            jenis_dokumen: $("#jenis_dokumen"),
            jenis_institusi_mitra: $("#jenis_institusi_mitra"),
            select2_lembaga: $("#select2_lembaga"),
            tahun: $("#tahun"),
            stats_kerma: $("#stats_kerma"),
            status_dokumen: $("#status_dokumen"),
        };

        $(".btn-reset-pengajuan").click(function() {
            $.each(inputs, function(key, $el) {
                $el.val('').trigger('change');
            });
        })

        $("#dn_ln").change(function() {
            var selectedValue = $(this).val();
            // Sembunyikan semua dropdown dulu
            $("#wilayah_wrapper").addClass("d-none").hide();
            $("#negara_wrapper").addClass("d-none").hide();

            // Tampilkan dropdown sesuai pilihan radio
            if (selectedValue === "Dalam Negeri") {
                $("#wilayah_wrapper").removeClass("d-none").fadeIn();
            } else if (selectedValue === "Luar Negeri") {
                $("#negara_wrapper").removeClass("d-none").fadeIn();
            }
        })

    })


</script>
