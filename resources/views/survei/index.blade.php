@extends('layouts.app')

@section('contents')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <style>
                            span.badge {
                                font-size: 10px !important;
                            }
                        </style>
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <span class="me-2">
                                        <i class="fa-solid fa-folder-open text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mt-4">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="internal-tab" data-bs-toggle="tab"
                                            data-bs-target="#internal" type="button" role="tab"
                                            aria-controls="internal" aria-selected="true">
                                            Internal
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="eksternal-tab" data-bs-toggle="tab"
                                            data-bs-target="#eksternal" type="button" role="tab"
                                            aria-controls="eksternal" aria-selected="false">
                                            Eksternal
                                        </button>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content mt-3" id="myTabContent">
                                    <!-- Tab Timeline -->
                                    <div class="tab-pane fade show active" id="internal" role="tabpanel"
                                        aria-labelledby="internal-tab">
                                        <div class="konten-internal"></div>
                                    </div>
                                    <!-- Tab Timeline -->
                                    <div class="tab-pane fade" id="eksternal" role="tabpanel"
                                        aria-labelledby="eksternal-tab">
                                        <div class="konten-eksternal"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modal-detail" aria-labelledby="DetailLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="DetailLabel">Detail Kerja Sama</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="konten-detail"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="surveiModal" tabindex="-1" aria-labelledby="surveiModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="surveiModalLabel">Pemberitahuan Pengisian Survei</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="surveiNotifikasi">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var getDataInternal = "{{ route('survei.getDataInternal') }}"
        var getDataEksternal = "{{ route('survei.getDataEksternal') }}"
    </script>
    <script src="{{ asset('js/survei/index.js') }}"></script>
@endpush
