 <div id="rekap" class="content-section p-3">
     <div class="container">
         <div class="title-bar"></div>
         <h3 class="title-dashboard">Jumlah Kerja Sama Aktif</h3>
         <div class="row">
             <!-- Skor Prodi 1 Tahun Terakhir -->
             <div class="col-md-6 mb-3">
                 <div class="ranking-card p-3 text-center">
                     <span class="title-dashboard">Dalam Negeri</span>
                     <div class="point-box platinum" onclick="detailKerma('Dalam Negeri','Kerja Sama Dalam Negeri')">
                         {{ round(@$KermaDN, 2) }}
                     </div>
                 </div>
             </div>

             <!-- Skor Rata Rata 1 Tahun Terakhir -->
             <div class="col-md-6 mb-3">
                 <div class="ranking-card p-3 text-center">
                     <span class="title-dashboard">Luar Negeri</span>
                     <div class="point-box platinum " onclick="detailKerma('Luar Negeri','Kerja Sama Luar Negeri')">
                         {{ round(@$KermaLN, 2) }}
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>


 <script>
     const UrlDetailKerma = "{{ route('home.detailKerma') }}";

     function detailKerma($type, judul) {
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
             url: UrlDetailKerma,
             type: "get",
             data: {
                 type: $type,
                 q: @json($q),
                 ps: @json($placeState),
                 _token: "{{ csrf_token() }}",
             },
             success: function(response) {
                 $("#konten-detail").html(response.view);
             },
             error: function(xhr, status, error) {},
         });
     }
 </script>
