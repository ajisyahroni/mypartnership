<div class="collapse mt-3 mb-3 px-5" id="filterImplementasi">
    <div class="filter-box">
        <div class="py-3">
            <h5><i class="fas fa-filter"></i> Filter Options</h5>
        </div>
        <form action="{{ route('implementasi.getData') }}" method="get" id="formFilterImplementasi">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-handshake"></i> Mitra Kerja Sama</label>
                    <select class="form-select" name="nama_institusi" id="nama_institusi">
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-list"></i> Kategori</label>
                    <select class="form-select select2" name="category" id="category">
                        <option value="">Pilih Kategori</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-user-tie"></i> Pelaksana Kegiatan</label>
                    <select class="form-select select2" name="pelaksana" id="pelaksana">
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-file-alt"></i> Judul Kegiatan</label>
                    <select class="form-select select2" name="judul" id="judul">
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-user"></i> Pelopor</label>
                    <select class="form-select select2" name="postby" id="postby">
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-calendar-alt"></i> Tahun Berakhir</label>
                    <select class="form-select select2" name="tahun" id="tahun">
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-check"></i> Status Verifikasi</label>
                    <select class="form-select select2" name="status" id="status">
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-danger btn-sm me-3 btn-reset-implementasi">
                    <i class="fas fa-undo"></i> Reset Filter
                </button>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-check"></i> Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    $(document).ready(function() {

        let inputs = {
            nama_institusi: $("#nama_institusi"),
            category: $("#category"),
            pelaksana: $("#pelaksana"),
            judul: $("#judul"),
            postby: $("#postby"),
            tahun: $("#tahun"),
            status: $("#status"),
        };
        getReferensi();

        $(".btn-reset-implementasi").click(function() {
            $.each(inputs, function(key, $el) {
                $el.val('').trigger('change');
            });
        })

        function getReferensi() {
            $.each(inputs, function(key, $el) {
                $el.append('<option value="">Loading....</option>');
            });

            $.ajax({
                url: "{{ route('getReferensiImplementasi') }}",
                type: "GET",
                dataType: "json",
                success: (res) => {
                    inputs['nama_institusi'].html(res.institusi);
                    inputs['category'].html(res.category);
                    inputs['pelaksana'].html(res.pelaksana);
                    inputs['judul'].html(res.judul);
                    inputs['postby'].html(res.postby);
                    // inputs['jenis_institusi_mitra'].html(res.jenis_institusi_mitra);
                    inputs['tahun'].html(res.tahun);
                    inputs['status'].html(res.status);
                },
                error: (xhr) => {
                    let errorMessages = "";
                    if (xhr.responseJSON?.errors) {
                        Object.values(xhr.responseJSON.errors).forEach(
                            (messages) => {
                                errorMessages +=
                                    messages.join("<br>") + "<br>";
                            }
                        );
                    }
                    console.log(errorMessages);
                },
            });
        }

    })
</script>
