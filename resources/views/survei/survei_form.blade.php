<style>
    /* Centering the radio buttons container */
    .d-flex.justify-content-start {
        display: flex;
        justify-content: center;
        /* Align radio buttons horizontally */
        align-items: center;
        /* Vertically align the items */
        gap: 20px;
        /* Space between the icons */
    }

    /* Styling untuk container radio button */
    .form-check {
        position: relative;
        /* Set the container position */
        display: inline-block;
        width: 50px;
        /* Menentukan ukuran radio button */
        height: 50px;
        /* Menentukan ukuran radio button */
    }

    /* Styling untuk radio button yang sebenarnya (disembunyikan) */
    .form-check-input[type="radio"] {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
    }

    /* Styling untuk label dan gambar emoji */
    .form-check-label {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        /* Membuat lingkaran */
        background-color: #f8f9fa;
        /* Warna latar belakang */
        border: 2px solid #007bff;
        /* Border lingkaran */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Gambar emoji di tengah lingkaran */
    .form-check-label img {
        width: 30px;
        /* Ukuran gambar */
        height: 30px;
        object-fit: contain;
        /* Pastikan gambar sesuai dalam lingkaran */
    }

    /* Styling untuk radio button yang terpilih */
    .form-check-input[type="radio"]:checked+.form-check-label {
        background-color: #0a74e5;
        /* Warna latar belakang ketika terpilih */
        border-color: #0056b3;
        /* Border warna ketika terpilih */
    }

    img {
        visibility: hidden;
        /* Gambar tidak terlihat saat halaman pertama kali dimuat */
        transition: visibility 0.3s ease-in-out;
        /* Transisi halus */
    }

    /* Ketika gambar sudah dimuat, tampilkan */
    img[src] {
        visibility: visible;
    }
</style>

<div class="p-3">
    <input type="hidden" name="id_table" value="{{ $id_table }}">
    <input type="hidden" name="jenis_jawaban" value="{{ $jenis_jawaban }}">
    <!-- Skala Penilaian -->
    @foreach ($pertanyaanFeedback as $pertanyaan)
        <div class="mb-3">
            <label for="pertanyaan_{{ $pertanyaan->id }}" class="form-label">{{ $loop->iteration }}.
                {{ $pertanyaan->pertanyaan }}</label>
            <div class="d-flex justify-content-center align-items-center gap-3">
                @foreach ($skalaPenilaian as $item)
                    <div class="form-check me-3">
                        <!-- Mark the radio button as checked if there's an existing answer -->
                        <input class="form-check-input" type="radio" name="jawaban[{{ $pertanyaan->id }}]"
                            id="skala_{{ $item->id }}_{{ $pertanyaan->id }}" value="{{ $item->nama }}"
                            style="cursor: pointer;"
                            {{ optional($jawabanFeedback->where('id_pertanyaan_feedback', $pertanyaan->id)->first())->jawaban == $item->nama ? 'checked' : '' }}>
                        <label class="form-check-label" for="skala_{{ $item->id }}_{{ $pertanyaan->id }}"
                            style="cursor: pointer;">
                            <!-- Show Emoji as the icon inside radio button -->
                            <img src="{{ asset($item->gambar) }}" alt="emoji"
                                style="width: 40px; height: 40px;" onload="this.style.visibility='visible'">
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Masukan/Saran -->
    <div class="mb-5">
        <label for="masukan" class="form-label">Masukan/Saran</label>
        <textarea class="form-control" id="masukan" name="masukan" rows="3" placeholder="Ketikkan Masukan/Saran">{{ isset($jawabanSurvei) ? $jawabanSurvei->jawaban : '' }}</textarea>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Tunggu sampai gambar selesai dimuat
        $("img").on("load", function() {
            $(this).css("visibility", "visible"); // Tampilkan gambar setelah selesai dimuat
        });

        // Jika gambar tidak dimuat, set gambar fallback
        $("img").on("error", function() {
            $(this).attr("src", "path/to/default-image.jpg"); // Ganti dengan gambar default
        });
    });
</script>
