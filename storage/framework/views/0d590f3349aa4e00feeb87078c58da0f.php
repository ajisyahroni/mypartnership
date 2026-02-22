<script>
    window.imageUrl = '<?php echo e(asset('/storage/')); ?>';
    window.BASE_URL = '<?php echo e(url('')); ?>';

    let csrfToken = $('meta[name="csrf-token"]').attr('content');
</script>
<script>
    $(document).ready(function() {
        $(".sidebar-link.has-arrow").on("click", function() {
            $(this).next(".collapse").slideToggle();
        });

        const $fileInput = $('#fileInput');
        const $uploadButton = $('#uploadButton');
        const $fileNameDiv = $('#fileName');
        const $previewImage = $('#previewImage');
        const $imagePreview = $('#imagePreview');

        // Menampilkan file input ketika tombol upload diklik
        $uploadButton.on('click', function() {
            $fileInput.click();
        });

        // Menangani pemilihan file dan menampilkan nama file dan preview gambar
        $fileInput.on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $previewImage.show();
                    $previewImage.attr('src', e.target.result);
                    $imagePreview.css('background-color', 'transparent');
                };
                reader.readAsDataURL(file);

                // Menampilkan nama file di atas tombol upload
                $fileNameDiv.text(file.name);
            } else {
                $previewImage.hide();
                $imagePreview.css('background-color', 'rgba(0, 0, 0, 0.1)');
                $fileNameDiv.text('');
            }

            // toggleSaveButton();
        });

    });

</script>
<?php /**PATH /var/www/resources/views/layouts/components/main.blade.php ENDPATH**/ ?>