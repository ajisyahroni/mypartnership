<div class="p-3">
    <table class="table table-hover align-middle table-bordered table-sm" id="datatableSub">
        <thead class="table-light">
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Opsi</th>
                <th>Kategori</th>
                <th>Pelaksana</th>
                <th>Judul Kegiatan</th>
                <th class="text-center">Tahun Berakhir</th>
                <th>Pelapor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataPengajuan as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button data-title-tooltip="Lihat Detail" class="btn btn-sm btn-outline-info btn-subdetail"
                                data-id_ev="{{ $row->id_ev }}">
                                <i class="bx bx-show"></i>
                            </button>
                            @if (session('current_role') == 'admin' ||
                                    (session('current_role') != 'admin' && $row->postby == auth()->user()->username))
                                <a href="{{ route('implementasi.edit', ['id' => $row->id_ev]) }}"
                                    data-title-tooltip="Edit Pengajuan" class="btn btn-sm btn-outline-warning btn-edit"
                                    data-id_ev="{{ $row->id_ev }}">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <button data-title-tooltip="Hapus Implementasi"
                                    class="btn btn-sm btn-outline-danger btn-hapus" data-id_ev="{{ $row->id_ev }}">
                                    <i class="bx bx-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                    <td>
                        @php
                            $category = preg_replace('/\s*\(.*?\)/', '', $row->category);
                            $action =
                                '<span class="badge rounded-pill bg-primary" style="font-size:10px!important;">' .
                                $category .
                                '</span>';
                            echo $action;
                        @endphp
                    </td>
                    <td>{{ $row->pelaksana_prodi_unit }}</td>
                    <td>
                        @php
                            echo $row->judul != 'Lain-lain' ? $row->judul : $row->judul_lain;
                        @endphp
                    </td>
                    <td class="text-center">
                        @php
                            // echo $row->statusPengajuanKerjaSama();
                            echo $row->status_pengajuan_kerja_sama_label;
                        @endphp
                    </td>
                    <td>{{ $row->getPost->name ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


<div class="modal fade" id="modal-subdetail" aria-labelledby="DetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DetailLabel">Detail Lapor Implementasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="konten-detail"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        const modalDetail = $("#modal-subdetail");
        const formFilter = $("#formFilterImplementasi");
        let csrfToken = $('meta[name="csrf-token"]').attr("content");
        let urlHapus = "/implementasi/destroy";
        let getDetailImplementasi = "/implementasi/getDetailImplementasi";

        $("#datatableSub").on("click", ".btn-subdetail", function() {
            let id_ev = $(this).data("id_ev");
            let srcPdf = $(this).data("srcpdf");
            let encodedSrcPdf = encodeURI(srcPdf);

            modalDetail.modal("show");
            $(".konten-detail").html(`
            <div class="d-flex justify-content-center my-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
            $.ajax({
                url: getDetailImplementasi,
                method: "GET",
                data: {
                    id_ev: id_ev,
                },
                success: (response) => {
                    $(".konten-detail").html(response.html);
                },
            });
        });


        $("#datatableSub").on("click", ".btn-hapus", function() {
            hapus($(this).data("id_ev"));
        });

        $("#datatableSub").on("click", ".btn-verifikasi", function() {
            verifikasi(
                $(this).data("status"),
                $(this).data("tipe"),
                $(this).data("id_ev")
            );
        });

        function hapus(id_ev) {
            Swal.fire({
                text: "Anda yakin ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading("Menghapus data...");
                    $.ajax({
                        url: urlHapus,
                        type: "POST",
                        data: {
                            id_ev,
                            _token: csrfToken
                        },
                        dataType: "json",
                        success: (res) => {
                            Swal.fire("Berhasil!", res.message, "success");
                            table.ajax.reload(null, false);
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
    });
</script>
