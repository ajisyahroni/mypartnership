 @if (!empty($dataLaporanHibah))

     <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
         <div class="container-fluid">
             <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSectionLinks">
                 <span class="navbar-toggler-icon"></span>
             </button>
             <div class="collapse navbar-collapse" id="navbarSectionLinks">
                 <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                     <li class="nav-item"><a class="nav-link scroll-to" href="#section-informasiLaporan">Informasi
                             Umum</a></li>
                     <li class="nav-item"><a class="nav-link scroll-to" href="#section-pendanaanLaporan">Detail
                             Pendanaan</a></li>
                 </ul>
             </div>
         </div>
     </nav>

     <div class="scrollDetail" style="height: 50vh;overflow-y:auto;">
         <h5 id="section-informasiLaporan" class="mt-4">Detail Pendanaan Laporan</h5>
         <table id="tabelLaporanHibah" class="table table-hover table-bordered">
             <tbody>
                 <tr>
                     <td>Detail Aktivitas Kerja Sama</td>
                     <td>:</td>
                     <td style="text-align: justify;">{!! @$dataLaporanHibah->detail_aktivitas !!}</td>
                 </tr>
                 <tr>
                     <td>Target Output dan Outcome</td>
                     <td>:</td>
                     <td style="text-align: justify;">{!! @$dataLaporanHibah->target_laporan !!}</td>
                 </tr>
                 <tr>
                     <td>Hasil dan Dampak Kerja Sama</td>
                     <td>:</td>
                     <td style="text-align: justify;">{!! @$dataLaporanHibah->hasil_laporan !!}</td>
                 </tr>
                 <tr>
                     <td>Rencana Tindak Lanjut Kerja Sama</td>
                     <td>:</td>
                     <td style="text-align: justify;">{!! @$dataLaporanHibah->rencana_tindak_lanjut !!}</td>
                 </tr>
                 <tr>
                     <td>File Dokumen Pendukung</td>
                     <td>:</td>
                     <td>
                         @if (@$dataLaporanHibah->file_pendukung)
                             @php $fileUrl = encodedFileUrl($dataLaporanHibah->file_pendukung); @endphp
                             <a href="{{ $fileUrl }}" class="btn btn-sm btn-primary" target="_blank"><i
                                     class="bx bx-download"></i></a><br><br>
                             <iframe src="{{ $fileUrl }}" frameborder="0" id="iframe_file_pendukung"
                                 style="width: 100%;height: 500px;"></iframe>
                         @else
                             <small class="text-danger">Belum Ada File Upload</small>
                         @endif
                     </td>
                 </tr>

                 <tr>
                     <td>File Dokumentasi</td>
                     <td>:</td>
                     <td>
                         @if (@$dataLaporanHibah->file_dokumentasi)
                             @php $fileUrl = encodedFileUrl($dataLaporanHibah->file_dokumentasi); @endphp
                             <a href="{{ $fileUrl }}" class="btn btn-sm btn-primary" target="_blank"><i
                                     class="bx bx-download"></i></a><br><br>
                             <iframe src="{{ $fileUrl }}" frameborder="0" id="iframe_file_dokumentasi"
                                 style="width: 100%;height: 500px;"></iframe>
                         @else
                             <small class="text-danger">Belum Ada File Upload</small>
                         @endif
                     </td>
                 </tr>

                 <tr>
                     <td>File Laporan Kegiatan</td>
                     <td>:</td>
                     <td>
                         @if (@$dataLaporanHibah->file_laporan_kegiatan)
                             @php $fileUrl = encodedFileUrl($dataLaporanHibah->file_laporan_kegiatan); @endphp
                             <a href="{{ $fileUrl }}" class="btn btn-sm btn-primary" target="_blank"><i
                                     class="bx bx-download"></i></a><br><br>
                             <iframe src="{{ $fileUrl }}" frameborder="0" id="iframe_file_laporan_kegiatan"
                                 style="width: 100%;height: 500px;"></iframe>
                         @else
                             <small class="text-danger">Belum Ada File Upload</small>
                         @endif
                     </td>
                 </tr>

                 <tr>
                     <td>File Transaksi</td>
                     <td>:</td>
                     <td>
                         @if (@$dataLaporanHibah->file_transaksi)
                             @php $fileUrl = encodedFileUrl($dataLaporanHibah->file_transaksi); @endphp
                             <a href="{{ $fileUrl }}" class="btn btn-sm btn-primary" target="_blank"><i
                                     class="bx bx-download"></i></a><br><br>
                             <iframe src="{{ $fileUrl }}" frameborder="0" id="iframe_file_transaksi"
                                 style="width: 100%;height: 500px;"></iframe>
                         @else
                             <small class="text-danger">Belum Ada File Upload</small>
                         @endif
                     </td>
                 </tr>

                 <tr>
                     <td>File Tambahan</td>
                     <td>:</td>
                     <td>
                         @if (@$dataLaporanHibah->file_tambahan)
                             @php $fileUrl = encodedFileUrl($dataLaporanHibah->file_tambahan); @endphp
                             <a href="{{ $fileUrl }}" class="btn btn-sm btn-primary" target="_blank"><i
                                     class="bx bx-download"></i></a><br><br>
                             <iframe src="{{ $fileUrl }}" frameborder="0" id="iframe_file_tambahan"
                                 style="width: 100%;height: 500px;"></iframe>
                         @else
                             <small class="text-danger">Belum Ada File Upload</small>
                         @endif
                     </td>
                 </tr>

             </tbody>
         </table>
         <br><br>

         <h5 id="section-pendanaanLaporan" class="mt-4">Informasi Umum Laporan</h5>
         <table id="tabelDetailPendanaan" class="table table-hover">
             <thead>
                 <tr>
                     <th colspan="6" class="text-center">DETAIL PENDANAAN</th>
                 </tr>
                 <tr>
                     <th>No</th>
                     <th>Jenis Pengeluaran</th>
                     <th>Jumlah</th>
                     <th>Biaya Satuan</th>
                     <th>Biaya Total</th>
                 </tr>
             </thead>
             <tbody>
                 @php
                     $jenis = json_decode(@$dataLaporanHibah->jenis_pengeluaran);
                     $jumlah = json_decode(@$dataLaporanHibah->jumlah_pengeluaran);
                     $satuan = json_decode(@$dataLaporanHibah->biaya_satuan);
                     $total = json_decode(@$dataLaporanHibah->biaya_total);

                     $totalPendanaan = 0;
                 @endphp
                 @if (empty($jenis))
                     <tr>
                         <td colspan="7">Belum Ada Data</td>
                     </tr>
                 @else
                     @foreach ($jenis as $index => $jns)
                         <tr>
                             <td>{{ $loop->iteration }}</td>
                             <td>{{ $jns }}</td>
                             <td>{{ $jumlah[$index] ?? '' }}
                             </td>
                             <td>
                                 {{ $satuan[$index] ? rupiah($satuan[$index]) : '' }}
                             </td>
                             <td>
                                 {{ $total[$index] ? rupiah($total[$index]) : '' }}
                             </td>
                         </tr>
                         @php
                             $totalPendanaan += $total[$index];
                         @endphp
                     @endforeach
                 @endif
             </tbody>
             <tfoot>
                 <tr>
                     <td colspan="4" style="text-align: right;">Total</td>
                     <td>Rp. {{ rupiah($totalPendanaan) }}</td>
                 </tr>
             </tfoot>
         </table>
     </div>
 @else
     <div class="text-center p-3">
         <span class="text-center">Belum Mengisi Laporan</span>
     </div>
 @endif
