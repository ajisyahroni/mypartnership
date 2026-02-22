<div id="table-implementasi" class="table-implementasi">
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table table-hover align-middle custom-table" id="dataTable">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Opsi</th>
                    <th>Kategori</th>
                    <th>Mitra Kerja Sama</th>
                    <th>Tingkat Kerja Sama</th>
                    <th>Pelaksana</th>
                    <th>Judul Kegiatan</th>
                    <th>Bentuk Kegiatan/ Manfaat</th>
                    <th>Bukti Pelaksanaan</th>
                    <th>Link Dokumen Kerja Sama</th>
                    <th>Link Lapor Kerma</th>
                    <th class="text-center">Tahun Berakhir</th>
                    <th>Pelapor</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    var getData = "{{ route('implementasi.getData') }}"
</script>
<script>
    $(document).ready(function() {
        var table = $("#dataTable").DataTable({
            paging: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 75, 100],
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            scrollX: true,
            responsive: true,
            language: {
                search: "Pencarian:",
                searchPlaceholder: "Cari Data...",
            },
            buttons: [{
                    extend: "excelHtml5",
                    text: "EXCEL",
                    className: "btn btn-sm btn-primary",
                },
                {
                    extend: "csvHtml5",
                    text: "CSV",
                    className: "btn btn-sm btn-success",
                },
            ],
            serverSide: true,
            processing: true,
            ajax: {
                url: getData, // URL endpoint untuk mengambil data
                type: "GET",
                dataType: "json",
                data: function(d) {
                    d.category = $("#filterImplementasi #category").val();
                    d.nama_institusi = $(
                        "#filterImplementasi #nama_institusi"
                    ).val();
                    d.pelaksana = $("#filterImplementasi #pelaksana").val();
                    d.judul = $("#filterImplementasi #judul").val();
                    d.postby = $("#filterImplementasi #postby").val();
                    d.tahun = $("#filterImplementasi #tahun").val();
                },
            },
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false,
                }, // Tambahkan nomor urut
                {
                    data: "action",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "category",
                    orderable: true,
                    searchable: false,
                    render: (data, type, row) =>
                        `<span class="text-dark fw-semibold mb-0">${data}</span>`,
                },
                {
                    data: "nama_institusi",
                    orderable: true,
                    searchable: true,
                    render: (data, type, row) =>
                        `<span class="text-dark fw-semibold mb-0">${data}</span>`,
                },
                {
                    data: "tingkat_kerjasama",
                    orderable: true,
                    searchable: false,
                    render: (data, type, row) =>
                        `<span class="text-dark fw-semibold mb-0">${data}</span>`,
                },
                {
                    data: "pelaksana_prodi_unit",
                    orderable: true,
                    searchable: false,
                    render: (data, type, row) =>
                        `<span class="text-dark fw-semibold mb-0">${data}</span>`,
                },
                {
                    data: "judul",
                    orderable: true,
                    searchable: true,
                    render: (data, type, row) =>
                        `<span class="text-dark fw-semibold mb-0">${data}</span>`,
                },
                {
                    data: "bentuk_kegiatan",
                    orderable: true,
                    searchable: true,
                    render: (data, type, row) =>
                        `<span class="text-dark fw-semibold mb-0">${data}</span>`,
                },
                {
                    data: "bukti_pelaksanaan",
                    class: "text-center"
                },
                {
                    data: "dokumen_kerjasama",
                    class: "text-center"
                },
                {
                    data: "lapor_kerma",
                    class: "text-center"
                },
                {
                    data: "tahun_berakhir",
                    class: "text-center",
                    orderable: true,
                    searchable: false,
                },
                {
                    data: "pelapor",
                    orderable: true,
                    searchable: true,
                    render: (data, type, row) =>
                        `<span class="text-dark fw-semibold mb-0">${data}</span>`,
                },
            ],
            createdRow: function(row, data, dataIndex) {
                $("td", row).addClass("border-bottom-0");
                if (dataIndex % 2 === 0) {
                    $(row).css("background-color", "#f8f9fa");
                } else {
                    $(row).css("background-color", "#ffffff");
                }
            },
        });
    })
</script>
