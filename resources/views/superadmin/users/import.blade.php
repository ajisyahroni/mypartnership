<!-- Modal: Semua fitur berada di dalam modal -->
<button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fas fa-upload me-2"></i> Import Data</button>
                                        

<div class="modal fade" id="importModal">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Import Excel</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row g-3">
                        <!-- LEFT -->
                        <div class="col-lg-7 col-12">
                            {{-- Error Alert --}}
                            <div id="alertError" class="alert alert-danger d-none"></div>
                            {{-- Upload Form --}}
                            <form id="uploadForm" class="mb-3">
                                @csrf
                                <label class="fw-bold">Upload Excel</label>
                                <input type="file" name="file" class="form-control mb-2" accept=".xls, .xlsx" />
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        {{-- <a class="btn btn-dark w-100" target="_blank" href="{{ asset('template/tempate_import_user.xlsx') }}">Download Template</a> --}}
                                        <a class="btn btn-dark w-100" target="_blank" href="{{ route('import-user.downloadTemplate') }}">Download Template</a>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <button class="btn btn-success w-100" id="btnUpload">Upload</button>
                                    </div>
                                </div>
                            </form>

                            {{-- Search --}}
                            <input type="text" id="searchInput" class="form-control form-control-sm mb-3" placeholder="Cari data...">

                            {{-- Preview Table --}}
                            <div class="table-responsive" style="max-height: 350px; overflow-y: auto; border: 1px solid #ddd;">

                                <table class="table table-bordered table-sm mb-0" id="previewTable" style="font-size: 13px;">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>No</th>
                                            <th>Uniid/Username</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>Prodi</th>
                                            <th>Super Unit</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                            <button class="btn btn-primary w-100 mt-3" style="display: none;" id="btnSaveAll">Simpan</button>
                            <button class="btn btn-danger w-100 mt-3" style="display: none;" id="btnClearPreview">Bersihkan Preview</button>
                            

                        </div>
                        <!-- RIGHT -->
                        <div class="col-lg-5 col-12">

                            <div class="p-3 border rounded" style="background: #fafafa;">
                                <h5 class="mb-3">Edit Data</h5>

                                <form id="editForm">
                                    @csrf
                                    <input type="hidden" id="edit_id">

                                    <label>Uniid</label>
                                    <input type="text" id="edit_uniid" class="form-control form-control-sm mb-2">

                                    <label>Nama</label>
                                    <input type="text" id="edit_nama" class="form-control form-control-sm mb-2">

                                    <label>Jabatan</label>
                                    <input type="text" id="edit_jabatan" class="form-control form-control-sm mb-2">

                                    <label>Prodi</label>
                                    <input type="text" id="edit_prodi" class="form-control form-control-sm mb-3">

                                    <label>Super Unit</label>
                                    <input type="text" id="edit_superunit" class="form-control form-control-sm mb-3">

                                    <button class="btn btn-warning w-100" id="btnUpdateRow">Update Data</button>
                                </form>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- ERROR MODAL -->
    <div class="modal fade" id="errorModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Data Gagal Disimpan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                    <p class="mb-3">Beberapa baris gagal diproses. Silakan cek daftar di bawah:</p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" id="errorTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;font-size:12px;">No</th>
                                    <th style="min-width: 120px;font-size:12px;">UNI ID</th>
                                    <th style="min-width: 140px;font-size:12px;">Nama</th>
                                    <th style="min-width: 140px;font-size:12px;">Jabatan</th>
                                    <th style="min-width: 120px;font-size:12px;">Prodi</th>
                                    <th style="min-width: 140px;font-size:12px;">Superunit</th>
                                    <th style="min-width: 180px;font-size:12px;">Reason</th>
                                </tr>
                            </thead>
                            <tbody class="small"></tbody>
                        </table>
                    </div>

                    <a href="#" target="_blank" id="btnDownloadError" class="btn btn-warning mt-3">
                        Download Excel
                    </a>

                </div>

            </div>
        </div>
    </div>

<style>
    .loading-text {
        text-align: center;
        font-style: italic;
        padding: 10px;
        color: #777;
    }

    /* Header tabel sticky */
    #previewTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
    }

    /* Tabel lebih kecil */
    #previewTable td, 
    #previewTable th {
        padding: 6px 8px !important;
        font-size: 13px;
    }

    /* Scroll smooth */
    .table-responsive {
        scrollbar-width: thin;
    }

    /* Mobile optimization */
    @media(max-width: 768px){
        .modal-xl {
            width: 95%;
            margin: auto;
        }
    }

</style>


<script>
        $(function(){
            const errorDownloadUrl = @json(route('import-user.downloadFailedRows'));
            const downloadTemplate = @json(route('import-user.downloadTemplate'));
            const previewUrl = "{{ route('import-user.preview') }}";
            const updateUrl = "{{ route('import-user.updateRow') }}";
            const deleteUrl = "{{ route('import-user.deleteRow') }}";
            const saveAllUrl = "{{ route('import-user.saveAll') }}";
            const clearUrl = "{{ route('import-user.clearPreview') }}";

            function escapeHtml(text) {
                return $('<div/>').text(text).html();
            }

            function renderTable(rows) {
                const tbody = $('#previewTable tbody');
                tbody.empty();

                rows.forEach(function (r, idx) {

                    let errorClass = r.error ? "table-danger" : "";
                    let errorText = r.error ? ` title="${r.error}"` : "";

                    const tr = $(`<tr class="${errorClass}"${errorText}></tr>`);

                    tr.append(`<td>${idx + 1}</td>`);
                    tr.append(`<td>${escapeHtml(r.uniid)}</td>`);
                    tr.append(`<td>${escapeHtml(r.nama)}</td>`);
                    tr.append(`<td>${escapeHtml(r.jabatan)}</td>`);
                    tr.append(`<td>${escapeHtml(r.prodi)}</td>`);
                    tr.append(`<td>${escapeHtml(r.superunit)}</td>`);

                    let actionBtn = `
                        <td>
                            <button class='btn btn-sm btn-warning btn-edit' data-id='${r.id}'>Edit</button>
                            <button class='btn btn-sm btn-danger btn-delete' data-id='${r.id}'>Hapus</button>
                        </td>
                    `;

                    tr.append(actionBtn);
                    tbody.append(tr);
                });
                 $("#btnSaveAll").show();
                 $("#btnClearPreview").show();
            }

            $('#importModal').on('show.bs.modal', function () {
                
                // Reset upload form
                $('#uploadForm')[0].reset();

                // Reset search input
                $('#searchInput').val('');

                // Reset alert error
                $('#alertError').addClass('d-none').html('');

                // Clear preview table
                $('#previewTable tbody').html('');

                // Hide save & clear buttons
                $('#btnSaveAll').hide();
                $('#btnClearPreview').hide();

                // Reset edit form
                $('#editForm')[0].reset();
                $('#edit_id').val('');
            });


            // UPLOAD EXCEL -----------------------------------------------
           $("#uploadForm").submit(function (e) {
                e.preventDefault();

                let formData = new FormData(this);

                $("#btnUpload")
                    .prop("disabled", true)
                    .html(`<span class="spinner-border spinner-border-sm"></span> Processing...`);

                $("#previewTable tbody").html(`
                    <tr>
                        <td colspan="5" class="loading-text text-center">
                            <div class="spinner-border spinner-border-sm"></div> Loading data preview...
                        </td>
                    </tr>
                `);

                $.ajax({
                    url: previewUrl,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function (res) {

                        if (!res.success) {
                            $("#alertError").removeClass('d-none').text(res.message);

                            $("#previewTable tbody").html(`
                                <tr>
                                    <td colspan="5" class="text-danger text-center fw-bold">
                                        ${res.message}
                                    </td>
                                </tr>
                            `);

                            return;
                        }

                        $("#alertError").addClass('d-none');

                        // Render tabel jika berhasil
                        renderTable(res.rows);
                    },

                    error: function (xhr) {
                        let msg = "Gagal memproses file";

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }

                        $("#alertError").removeClass('d-none').text(msg);

                        $("#previewTable tbody").html(`
                            <tr>
                                <td colspan="5" class="text-danger text-center fw-bold">
                                    ${msg}
                                </td>
                            </tr>
                        `);
                    },

                    complete: function () {
                        $("#btnUpload")
                            .prop("disabled", false)
                            .html("Upload");
                    }
                });
            });




            // EDIT BUTTON -------------------------------------------------
            $(document).on("click", ".btn-edit", function() {
                const id = $(this).data("id");

                let tr = $(this).closest("tr");
                let row = tr.children("td");

                $("#edit_id").val(id);
                $("#edit_uniid").val(row.eq(1).text());
                $("#edit_nama").val(row.eq(2).text());
                $("#edit_jabatan").val(row.eq(3).text());
                $("#edit_prodi").val(row.eq(4).text());
                $("#edit_superunit").val(row.eq(5).text());

                // simpan TR agar update tidak reload halaman
                $("#editForm").data("targetRow", tr);
            });

            // UPDATE ROW ---------------------------------------------------
           $("#editForm").submit(function(e){
                e.preventDefault();

                $("#btnUpdateRow")
                    .prop("disabled", true)
                    .html(`<span class="spinner-border spinner-border-sm"></span> Processing...`);

                let id = $("#edit_id").val();
                let uniid = $("#edit_uniid").val();
                let nama = $("#edit_nama").val();
                let jabatan = $("#edit_jabatan").val();
                let prodi = $("#edit_prodi").val();
                let superunit = $("#edit_superunit").val();

                $.post(updateUrl, {
                    _token: "{{ csrf_token() }}",
                    id, uniid, nama, jabatan, prodi, superunit
                })
                .done(function(res){

                    if (res.success) {

                        let tr = $("#editForm").data("targetRow");

                        tr.find("td").eq(1).text(uniid);
                        tr.find("td").eq(2).text(nama);
                        tr.find("td").eq(3).text(jabatan);
                        tr.find("td").eq(4).text(prodi);
                        tr.find("td").eq(5).text(superunit);

                        tr.addClass('table-success');
                        setTimeout(() => tr.removeClass('table-success'), 1200);
                    }
                    else {
                        $("#alertError").removeClass('d-none').text(res.message);
                    }
                })
                .fail(function() {
                    $("#alertError").removeClass("d-none").text("Gagal update data.");
                })
                .always(function () {
                    $("#btnUpdateRow")
                        .prop("disabled", false)
                        .html(`Update Data`);
                });
            });

            // DELETE ROW ---------------------------------------------------
           $(document).on("click", ".btn-delete", function () {
                let id = $(this).data("id");
                let btn = $(this);

                if (!confirm("Yakin ingin menghapus data ini?")) {
                    return;
                }

                btn.prop("disabled", true)
                .html(`<span class="spinner-border spinner-border-sm"></span>`);

                $.post(deleteUrl, {
                    _token: "{{ csrf_token() }}",
                    id: id,
                })
                .done(function (res) {
                    if (res.success) {

                        // animasi fade out row
                        btn.closest("tr").css("background", "#ffdddd").fadeOut(600, function () {
                            $(this).remove();
                        });
                    }
                })
                .fail(function () {
                    alert("Gagal menghapus data.");
                })
                .always(function () {
                    btn.prop("disabled", false).html("Hapus");
                });
            });


            $("#btnSaveAll").click(function () {
                let btn = $(this);

                showLoading("Proses Menyimpan data..");

                btn.prop("disabled", true)
                    .html(`<span class="spinner-border spinner-border-sm"></span> Menyimpan...`);

                $.post(saveAllUrl, {
                    _token: "{{ csrf_token() }}",
                })
                    .done(function (res) {

                        closeLoading();

                        let failed = res.failed_rows ?? [];

                        if (failed.length > 0) {
                            let tbody = $("#errorTable tbody");
                            tbody.empty();

                            failed.forEach((item, i) => {
                                let r = item.row;
                                tbody.append(`
                                    <tr>
                                        <td>${i + 1}</td>
                                        <td>${r.uniid}</td>
                                        <td>${r.nama}</td>
                                        <td>${r.jabatan}</td>
                                        <td>${r.prodi}</td>
                                        <td>${r.superunit}</td>
                                        <td>${item.reason}</td>
                                    </tr>
                                `);
                            });

                            // set link download
                            $("#btnDownloadError").attr("href", errorDownloadUrl);
                            $("#errorModal").modal("show");
                            setTimeout(() => {
                                Swal.fire("Perhatian", "Ada beberapa data gagal disimpan", "warning");
                            }, 500);

                        } else {
                            setTimeout(() => {
                                Swal.fire("Sukses", "Semua data berhasil disimpan", "success");
                            }, 300);
                        }

                        $("#importModal").modal('hide');
                        $("#previewTable tbody").empty();      
                        $("#btnSaveAll").hide();               
                        $("#btnClearPreview").hide();         
                        $("#edit_id").val('');
                        $("#edit_uniid").val('');
                        $("#edit_nama").val('');
                        $("#edit_jabatan").val('');
                        $("#edit_prodi").val('');
                        $("#edit_superunit").val('');
                        $("#alertError").addClass("d-none").text('');
                        $("#uploadForm")[0].reset();

                    })
                    .fail(function () {
                        closeLoading();
                        Swal.fire("Error", "Gagal menyimpan data.", "error");
                    })
                    .always(function () {
                        closeLoading();
                        btn.prop("disabled", false)
                            .html("Simpan Semua");
                    });
            });



            // SEARCH TABLE --------------------------------------------------
            $("#searchInput").keyup(function(){
                let keyword = $(this).val().toLowerCase();

                $("#previewTable tbody tr").filter(function(){
                    $(this).toggle($(this).text().toLowerCase().indexOf(keyword) > -1);
                });
            });

             // ==========================
             // 6. Clear Preview (opsional)
             // ==========================
             $("#btnClearPreview").on("click", function () {
                 $("#btnClearPreview")
                     .prop("disabled", true)
                     .html(`<span class="spinner-border spinner-border-sm"></span> Processing...`);
                    
                $("#btnSaveAll").hide();

                 $("#previewTable tbody").html(`
                    <tr>
                        <td colspan="5" class="loading-text text-center">
                            <div class="spinner-border spinner-border-sm"></div> Bersihkan data preview...
                        </td>
                    </tr>
                `);

                $.ajax({
                    url: clearUrl,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function () {
                        $("#previewTable tbody").empty();
                        $("#btnClearPreview").hide();
                        $("#btnClearPreview")
                            .prop("disabled", false)
                            .html("Bersihkan Preview");
                    }
                });
            });

        });

    </script>