<div class="container mt-5">
    <div class="row mb-3 align-items-center">
        <div class="col-sm-3 text-dark fw-bold">
            Kirim ke
        </div>
        <div class="col-sm-9 fw-semibold text-primary">
            {{ $dataKuesioner->pic_kegiatan != '' && $dataKuesioner->pic_kegiatan != null ? $dataKuesioner->pic_kegiatan : 'Tidak ada Email Mitra' }}
        </div>
    </div>

    <div class="row mb-3 align-items-start">
        <div class="col-sm-3 text-dark fw-bold">
            Isi Pesan <span class="text-danger">*</span>
        </div>
        <div class="col-sm-9">
            <textarea name="isi_pesan" id="isi_pesan" class="form-control" rows="20" required>
                        {!! $isiPesan ?? '' !!}
                    </textarea>
        </div>
    </div>
</div>
<input type="hidden" name="id_kuesioner" value="{{ $dataKuesioner->id_kuesioner }}">



<script>
    $(document).ready(function() {
        // Inisialisasi Summernote
        $('#isi_pesan').summernote({
            height: 500,
            placeholder: 'Tulis deskripsi singkat di sini...',
            callbacks: {
                onImageUpload: function(files) {
                    uploadImage(files[0]);
                }
            }
        });
    });

    function uploadImage(file) {
        let data = new FormData();
        data.append("image", file);

        $.ajax({
            url: '/upload-gambar-summernote', // Ganti ini dengan route Laravel kamu
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // pastikan token tersedia
            },
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function(url) {
                $('#deskripsi_singkat').summernote('insertImage', url);
            },
            error: function(xhr, status, error) {
                alert('Upload gagal: ' + error);
            }
        });
    }
</script>
