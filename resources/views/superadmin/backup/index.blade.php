@extends('layouts.app')

{{-- @push('styles')
    <link rel="stylesheet" href="//cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
@endpush --}}
@section('contents')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body">
                            <div class="d-flex mb-4 justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    BackUp 
                                </h5>
                                <div>
                                    <button type="button" class="btn btn-primary" id="btn-backupDatabase">Backup Database</button>
                                    <button type="button"  class="btn btn-secondary" id="btn-backupFiles">Backup Files</button>
                                </div>
                            </div>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle custom-table" id="dataTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama File</th>
                                            <th>Type File</th>
                                            <th>Ukuran File</th>
                                            <th>Tanggal Back Up</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="modal-tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('role-permission.store') }}" method="post" id="formInput">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3 form-group">
                                    <label for="nama"><b>Nama Role</b></label>
                                    <input type="text" class="form-control" placeholder="Masukkan Nama" name="nama"
                                        id="nama">
                                    <input type="hidden" name="uuid" id="uuid">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

@endsection

@push('scripts')
    <script>
        var getData = "{{ route('backup.getData') }}"
    </script>
    <script src="{{ asset('js/superadmin/backup/index.js') }}"></script>
@endpush
