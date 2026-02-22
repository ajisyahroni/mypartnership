@extends('layouts.app')

@section('contents')
    <style>
        .file-upload-container {
            width: 100%;
            max-width: 500px;
        }

        .file-upload {
            position: relative;
            border: 2px dashed #b8bcbf;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background-color: #ffffff;
            transition: background-color 0.3s ease-in-out;
        }

        .file-upload:hover {
            background-color: #e2e6ea;
        }

        .file-input {
            display: none;
        }

        .file-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
        }

        .upload-icon {
            font-size: 50px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .file-upload p {
            margin: 0;
            font-size: 16px;
            color: #6c757d;
        }

        .file-upload.dragover {
            background-color: #007bff;
            color: white;
        }

        .file-name-container {
            margin-top: 10px;
            display: none;
            flex-direction: column;
            align-items: center;
            min-height: 40px;
            text-align: center;
        }

        .alert-primary {
            padding: 10px;
            border-radius: 5px;
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
            width: 100%;
            position: relative;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px;
            width: 100%;
        }

        .delete-btn {
            background: #ff4d4d;
            color: white;
            border: none;
            padding: 2px 8px;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #cc0000;
        }
    </style>
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="me-2">
                                        <i class="fa-solid fa-folder-open text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                                @if ($role == 'admin')
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <button id="btnTambah" class="btn btn-primary shadow-sm">
                                            <i class="fas fa-plus-circle me-2"></i>
                                            Tambah Dokumen Pendukung
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mt-4">
                                @if (count(@$dokumenPendukung) == 0)
                                    <div class="alert alert-danger w-100 p-3 text-center">
                                        <span>Belum Ada Dokumen Pendukung</span>
                                    </div>
                                @else
                                    <div class="accordion custom-accordion" id="customAccordion">
                                        @foreach (@$dokumenPendukung as $item)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header">
                                                    <div class="d-flex justify-content-between align-items-center w-100">
                                                        <button class="accordion-button flex-grow-1" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse{{ $item->id }}"
                                                            aria-expanded="false">
                                                            {{ $item->nama_dokumen }}
                                                            @php
                                                                $filePath =
                                                                    $item->link_dokumen ??
                                                                    asset('storage/' . rawurlencode($item->file_dokumen));
                                                            @endphp
                                                            <a href="{{ $filePath }}" target="_blank" download
                                                                class="btn btn-sm btn-light ms-2 me-3"
                                                                data-title-tooltip="Download File"
                                                                onclick="event.stopPropagation();">
                                                                <i class="bx bx-download"></i>
                                                            </a>
                                                            @if (session('current_role') == 'admin')
                                                                <div class="text-center" onclick="event.stopPropagation();">
                                                                    <label class="toggle-switch">
                                                                        <input type="checkbox" class="check-status"
                                                                            data-id="{{ $item->id }}"
                                                                            {{ $item->is_active == '1' ? 'checked' : '' }}>
                                                                        <div class="toggle-switch-background">
                                                                            <div class="toggle-switch-handle"></div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        </button>
                                                    </div>
                                                </h2>

                                                <div id="collapse{{ $item->id }}" class="accordion-collapse collapse">
                                                    <div class="accordion-body">
                                                        <div class="card mb-3">
                                                            <div class="card-body">
                                                                @if (session('current_role') == 'admin')
                                                                    <div class="col-12 d-flex gap-2 mb-3">
                                                                        <button class="btn w-100 btn-warning btn-edit"
                                                                            data-uuid="{{ $item->uuid }}"
                                                                            data-link_dokumen="{{ $item->link_dokumen }}"
                                                                            data-nama_dokumen="{{ $item->nama_dokumen }}">
                                                                            <i class="bx bx-edit"></i> Edit
                                                                        </button>
                                                                        <button class="btn w-100 btn-danger btn-hapus"
                                                                            data-uuid="{{ $item->uuid }}"
                                                                            data-nama_dokumen="{{ $item->nama_dokumen }}">
                                                                            <i class="bx bx-trash"></i> Hapus
                                                                        </button>
                                                                    </div>
                                                                @endif

                                                                <div class="col-12 iframe-container" 
                                                                    data-uuid="{{ $item->uuid }}"
                                                                    data-loaded="false">
                                                                    <div class="loading-spinner text-center py-5">
                                                                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                                                            <span class="visually-hidden">Loading...</span>
                                                                        </div>
                                                                        <p class="mt-3 text-muted fw-medium">Memuat dokumen...</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-form" aria-labelledby="DetailLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="DetailLabel">Form Dokumen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('dokumenPendukung.store') }}" enctype="multipart/form-data" method="post"
                            id="formInput">
                            @csrf
                            <div class="modal-body">
                                <div class="row p-3">
                                    <input type="hidden" name="uuid" id="uuid">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="nama_dokumen" class="form-label">Nama Dokumen<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen"
                                                placeholder="Masukkan Nama Dokumen" value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="link_dokumen" class="form-label">Link Dokumen</label>
                                            <input type="text" class="form-control" id="link_dokumen" name="link_dokumen"
                                                placeholder="Masukkan Link Dokumen" value="">
                                        </div>
                                        <div class="mb-3 d-flex align-items-center">
                                            <hr class="flex-grow-1 me-2">
                                            <span class="text-muted">Atau</span>
                                            <hr class="flex-grow-1 ms-2">
                                        </div>
                                        <div class="mb-3">
                                            <label for="upload_file" class="form-label">Upload File</label>
                                            <div class="file-upload-container">
                                                <div class="file-upload" id="dropArea">
                                                    <input multiple class="file-input" name="file_dokumen" id="fileInput"
                                                        accept=".pdf, .docx, .doc"
                                                        type="file" />
                                                    <label class="file-label" for="fileInput">
                                                        <i class="upload-icon">📁</i>
                                                        <p>Drag & Drop your files here or click to upload</p>
                                                    </label>
                                                </div>
                                                <div class="file-name-container alert-primary" id="fileNames">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/admin/referensi/dokumen_pendukung/index.js') }}"></script>
@endsection

@push('scripts')
    <script>
        var getData = "{{ route('dokumenPendukung.getData') }}"
        var UrlsetDokumen = @json(route('dokumenPendukung.setDokumen'));
    </script>
    <script>
        $(document).ready(function() {
            let fileInput = $("#fileInput");
            let dropArea = $("#dropArea");
            let fileNamesDiv = $("#fileNames");

            dropArea.on("dragover", function(e) {
                e.preventDefault();
                $(this).addClass("dragover");
            });

            dropArea.on("dragleave", function() {
                $(this).removeClass("dragover");
            });

            dropArea.on("drop", function(e) {
                e.preventDefault();
                $(this).removeClass("dragover");
                fileInput[0].files = e.originalEvent.dataTransfer.files;
                updateFileNames();
            });

            fileInput.on("change", updateFileNames);

            function updateFileNames() {
                let files = fileInput[0].files;
                fileNamesDiv.empty();
                if (files.length > 0) {
                    $.each(files, function(index, file) {
                        let fileItem = $("<div>").addClass("file-item");
                        let fileNameSpan = $("<span>").text(file.name);
                        let deleteButton = $("<button>")
                            .text("Hapus")
                            .addClass("delete-btn")
                            .on("click", function() {
                                removeFile(index);
                            });

                        fileItem.append(fileNameSpan, deleteButton);
                        fileNamesDiv.append(fileItem);
                    });
                    fileNamesDiv.show();
                } else {
                    fileNamesDiv.hide();
                }
            }

            function removeFile(index) {
                let dt = new DataTransfer();
                let files = fileInput[0].files;

                $.each(files, function(i, file) {
                    if (i !== index) {
                        dt.items.add(file);
                    }
                });

                fileInput[0].files = dt.files;
                updateFileNames();
            }
        });
    </script>
@endpush
