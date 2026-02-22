 <div id="kerma_lembaga" class="content-section p-3">
     <div class="container">
         <div class="title-bar"></div>
         <h3 class="title-dashboard">Kerja Sama Lembaga</h3>
         <div class="row">
             <div class="col-12 mb-3">
                 <div class="table-responsive" style="overflow-x: auto;">
                     <table class="table table-hover align-middle custom-table" id="dataTable2">
                         <thead class="table-dark">
                             <tr>
                                 <th>No</th>
                                 <th>Lembaga</th>
                                 <th class="text-center">Jumlah Kerja Sama Produktif</th>
                                 <th class="text-center">Jumlah Kerja Sama</th>
                                 <th class="text-center">Aksi</th>
                             </tr>
                         </thead>
                         <tbody>
                             @foreach ($KerjaSamaLembaga as $item)
                                 <tr>
                                     <td>{{ $loop->iteration }}
                                     </td>
                                     <td class="text-start">{{ $item->nama_fakultas }}</td>
                                     <td class="text-center">{{ $item->jumlah_produktivitas }}</td>
                                     <td class="text-center">{{ $item->jumlah_kerma }}</td>
                                     <td class="text-center">
                                         <button class="btn btn-primary"
                                             onclick="detailKermaLembaga('{{ $item->nama_fakultas }}','Detail Kerja Sama Lembaga '+'{{ ucwords($item->nama_fakultas) }}')"><i
                                                 class="bx bx-link-external"></i></button>
                                     </td>
                                 </tr>
                             @endforeach
                         </tbody>
                         <tfoot>
                            <tr>
                                <td colspan="2" class="text-end">Total Kerja Sama</td>
                                <td class="text-center fw-bold" id="totalProduktivitas">0</td>
                                <td class="text-center fw-bold" id="totalKerma">0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                     </table>
                 </div>
             </div>
         </div>
     </div>
 </div>


 <script>
     let urlDetailKermaLembaga = @json(route('home.detailInstansi'))

     function detailKermaLembaga($id_lmbg, judul) {
         $("#modal-detail #DetailLabel").html(judul);
         $("#modal-detail").modal("show");

         $("#konten-detail").html(`
                    <div class="d-flex justify-content-center my-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);

         $.ajax({
             url: urlDetailKermaLembaga,
             type: "get",
             data: {
                 placeState: $id_lmbg,
                 _token: "{{ csrf_token() }}",
             },
             success: function(response) {
                 $("#konten-detail").html(response.view);
             },
             error: function(xhr, status, error) {},
         });
     }
 </script>

 <script>
$(document).ready(function () {
    let table = $('#dataTable2').DataTable({
        footerCallback: function () {
            let api = this.api();

            // Helper untuk konversi ke integer
            let intVal = function (i) {
                return typeof i === 'string'
                    ? i.replace(/[^0-9]/g, '') * 1
                    : typeof i === 'number'
                    ? i
                    : 0;
            };

            // Kolom index:
            // 2 = Jumlah Kerja Sama Produktif
            // 3 = Jumlah Kerja Sama
            let totalProduktivitas = api
                .column(2, { search: 'applied' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            let totalKerma = api
                .column(3, { search: 'applied' })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            $('#totalProduktivitas').html(totalProduktivitas);
            $('#totalKerma').html(totalKerma);
        }
    });
});
</script>

