<div class="container mt-4">
    <div class="text-center">
        <p class="text-muted small border-bottom pb-2 mb-3">
            {{ @$urlKuesioner }}
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href=" {{ @$urlKuesioner }}" target="_blank" class="btn btn-primary">
                <i class="fa-solid fa-file-lines me-1"></i> BUKA HALAMAN KUESIONER
            </a>

            <button onclick="copyLink()" class="btn btn-info text-white">
                <i class="fa-solid fa-clipboard me-1"></i> SALIN LINK KUESIONER
            </button>
        </div>
    </div>
</div>

<!-- JS for Copy to Clipboard -->
<script>
    function copyLink() {
        const link = '{{ @$urlKuesioner }}';
        navigator.clipboard.writeText(link).then(() => {
            alert('Link berhasil disalin!');
        }).catch(err => {
            alert('Gagal menyalin link.');
        });
    }
</script>
