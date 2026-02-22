<div class="collapse mt-3 mb-3 px-5" id="filterContent">
    <div class="filter-box">
        <div class="py-3">
            <h5><i class="fas fa-filter"></i> Filter Options</h5>
        </div>
        <form action="{{ route('hibah.getData') }}" method="get" id="formFilterPengajuan">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-file-alt"></i> Judul Proposal</label>
                    <select class="form-select select2" name="judul_proposal" id="judul_proposal">
                        {!! $filterHibah['judul_proposal'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-handshake"></i> Nama Institusi Mitra</label>
                    <select class="form-select select2" name="nama_institusi" id="nama_institusi">
                        {!! $filterHibah['institusi'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-gift"></i> Jenis Hibah</label>
                    <select class="form-select select2" name="jenis_hibah" id="jenis_hibah">
                        {!! $filterHibah['jenis_hibah'] !!}</select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-university"></i> Fakultas</label>
                    <select class="form-select select2" name="fakultas" id="fakultas">
                        {!! $filterHibah['fakultas'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-graduation-cap"></i> Program Studi</label>
                    <select class="form-select select2" name="program_studi" id="program_studi">
                        {!! $filterHibah['program_studi'] !!}
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><i class="fas fa-clipboard-check"></i> Status</label>
                    <select class="form-select select2" name="status" id="status">
                        {!! $filterHibah['status'] !!}
                    </select>
                </div>
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
            judul_proposal: $("#judul_proposal"),
            nama_institusi: $("#nama_institusi"),
            jenis_hibah: $("#jenis_hibah"),
            fakultas: $("#fakultas"),
            program_studi: $("#program_studi"),
            status: $("#status"),
        };
        // getReferensi();

        $(".btn-reset-pengajuan").click(function() {
            $.each(inputs, function(key, $el) {
                $el.val('').trigger('change');
            });
        })

        // function getReferensi() {
        //     $.each(inputs, function(key, $el) {
        //         $el.append('<option value="">Loading....</option>');
        //     });

        //     $.ajax({
        //         url: "{{ route('getReferensiFilterHibah') }}",
        //         type: "GET",
        //         dataType: "json",
        //         success: (res) => {
        //             inputs['judul_proposal'].html(res.judul_proposal);
        //             inputs['nama_institusi'].html(res.institusi);
        //             inputs['jenis_hibah'].html(res.jenis_hibah);
        //             inputs['fakultas'].html(res.fakultas);
        //             inputs['program_studi'].html(res.program_studi);
        //             inputs['status'].html(res.status);
        //         },
        //         error: (xhr) => {
        //             let errorMessages = "";
        //             if (xhr.responseJSON?.errors) {
        //                 Object.values(xhr.responseJSON.errors).forEach(
        //                     (messages) => {
        //                         errorMessages +=
        //                             messages.join("<br>") + "<br>";
        //                     }
        //                 );
        //             }
        //             console.log(errorMessages);
        //         },
        //     });
        // }

    })
</script>
