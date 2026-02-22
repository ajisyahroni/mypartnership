@extends('layouts.app')

@section('contents')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="me-2">
                                        <i class="fas fa-tools text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                                <a href="{{ route('mail.setting') }}" class="btn btn-danger">
                                    <i class="fas fa-arrow-left me-2"></i>kembali
                                </a>

                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="mt-4">
                                <!-- Search Bar -->
                                <div id="search-template">
                                    <div class="InputContainer">
                                        <input type="text" name="text" class="input" id="sidebarSearchPesan"
                                            placeholder="Cari Pesan Email...">
                                        <label for="input" class="labelforsearch">
                                            <svg viewBox="0 0 512 512" class="searchIcon">
                                                <path
                                                    d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z">
                                                </path>
                                            </svg>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="row isiKonten">
                                    @foreach ($dataSubjek as $index => $item)
                                        <div class="col-6 col-md-4 mb-3">
                                            <div class="card">
                                                <div class="card-header text-center" id="subjek-{{ $index }}">
                                                    <h5>{{ $item }}</h5>
                                                </div>

                                                <div class="card-body">
                                                    <div class="content-wrapper" id="content-{{ $index }}">
                                                        <span class="text-center">{!! $dataIsiPesan[$index] !!}</span>
                                                    </div>

                                                    <div class="mt-2 text-center">
                                                        <button class="btn btn-sm btn-warning btn-edit"
                                                            data-index="{{ $index }}"
                                                            data-field="{{ $fields[$index] }}">
                                                            <i class="bx bx-edit"></i>
                                                        </button>

                                                        <button class="btn btn-sm btn-primary btn-save d-none"
                                                            data-index="{{ $index }}"
                                                            data-field="{{ $fields[$index] }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>

                                                        <button class="btn btn-sm btn-danger btn-cancel d-none"
                                                            data-index="{{ $index }}">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <!-- Tambahkan div ini untuk pesan 'tidak ditemukan' -->
                                    <div id="not-found-message" class="text-center text-muted mt-3" style="display: none;">
                                        <h4>Data tidak ditemukan.</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var SimpanPesan = "{{ route('mail.store_isi_pesan') }}";
    </script>

    <script>
        $(document).ready(function() {
            $('#sidebarSearchPesan').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                var found = false;

                $('.isiKonten .card').each(function() {
                    var title = $(this).find('.card-header h5').text()
                        .toLowerCase(); // cukup cari di .card-header h5

                    if (title.indexOf(searchText) > -1) {
                        $(this).parent().show(); // show .col-6 col-md-4
                        found = true; // ada yang ditemukan
                    } else {
                        $(this).parent().hide();
                    }
                });

                // Kalau tidak ditemukan, tampilkan pesan
                if (found) {
                    $('#not-found-message').hide();
                } else {
                    $('#not-found-message').show();
                }
            });
        });
    </script>


    <script>
        $(function() {
            $('.btn-edit').on('click', function() {
                var index = $(this).data('index');
                var contentDiv = $('#content-' + index);
                var subjekDiv = $('#subjek-' + index);

                var currentContent = contentDiv.find('span').html();
                var currentSubjek = subjekDiv.find('h5').text();

                // Simpan konten lama untuk cancel
                contentDiv.data('original-content', currentContent);
                subjekDiv.data('original-subjek', currentSubjek);

                // Ganti ke mode edit
                contentDiv.html('<textarea id="editor-' + index + '">' + currentContent + '</textarea>');
                subjekDiv.html('<input type="text" class="form-control form-control-sm" id="input-subjek-' +
                    index + '" value="' + currentSubjek + '">');

                $('#editor-' + index).summernote({
                    // height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link']],
                        ['view', ['codeview']]
                    ]
                });

                $(this).addClass('d-none');
                $('.btn-save[data-index="' + index + '"]').removeClass('d-none');
                $('.btn-cancel[data-index="' + index + '"]').removeClass('d-none');
            });

            $('.btn-save').on('click', function() {
                var index = $(this).data('index');
                var field = $(this).data('field');
                var contentDiv = $('#content-' + index);
                var subjekDiv = $('#subjek-' + index);

                var newContent = $('#editor-' + index).summernote('code');
                var newSubjek = $('#input-subjek-' + index).val();

                var $btn = $(this);
                var originalIcon = $btn.html();

                // Spinner loading
                $btn.html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: SimpanPesan,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        field: field,
                        content: newContent,
                        subjek: newSubjek // ← Tambahkan subjek
                    },
                    success: function(res) {
                        if (res.status) {
                            contentDiv.html('<span class="text-center">' + newContent +
                                '</span>');
                            subjekDiv.html('<h5>' + newSubjek + '</h5>');

                            $('.btn-save[data-index="' + index + '"]').addClass('d-none').html(
                                originalIcon);
                            $('.btn-cancel[data-index="' + index + '"]').addClass('d-none');
                            $('.btn-edit[data-index="' + index + '"]').removeClass('d-none');

                            toastr.success('Berhasil disimpan!');
                        } else {
                            toastr.error('Gagal menyimpan data');
                            $btn.html(originalIcon);
                        }
                    },
                    error: function() {
                        toastr.error('Terjadi kesalahan');
                        $btn.html(originalIcon);
                    }
                });
            });

            $('.btn-cancel').on('click', function() {
                var index = $(this).data('index');
                var contentDiv = $('#content-' + index);
                var subjekDiv = $('#subjek-' + index);

                var originalContent = contentDiv.data('original-content');
                var originalSubjek = subjekDiv.data('original-subjek');

                contentDiv.html('<span class="text-center">' + originalContent + '</span>');
                subjekDiv.html('<h5>' + originalSubjek + '</h5>');

                $('.btn-save[data-index="' + index + '"]').addClass('d-none');
                $('.btn-cancel[data-index="' + index + '"]').addClass('d-none');
                $('.btn-edit[data-index="' + index + '"]').removeClass('d-none');
            });
        });
    </script>
    <script src="{{ asset('js/admin/mail/isi_pesan.js') }}"></script>
@endpush
