@extends('layouts.app')

@section('contents')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="col-sm-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body">
                            <div class="d-flex mb-4 justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    Delegasi User
                                </h5>
                                <div>
                                    @include('superadmin.users.import')
                                    <button class="btn btn-primary shadow-sm" id="btn-tambah">
                                        <i class="fas fa-user-plus me-2"></i> Tambah User
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <div class="table-responsive" style="overflow-x: auto;">
                                <table class="table table-hover align-middle custom-table" id="dataTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Opsi</th>
                                            <th>Nama</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Jabatan</th>
                                            <th>Status Tempat</th>
                                            <th>ID Lembaga</th>
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

    <div class="modal fade" id="modal-tambah" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('user-management.store') }}" method="post" id="formUser">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="nama"><b>Nama</b></label>
                                    <input type="text" class="form-control" placeholder="Masukkan Nama" name="nama"
                                        id="nama">
                                    <input type="hidden" name="uuid" id="uuid">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="username"><b>Username</b></label>
                                    <input type="text" class="form-control" placeholder="Masukkan Username"
                                        name="username" id="username">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="email"><b>Email</b></label>
                                    <input type="text" class="form-control" placeholder="Masukkan Email" name="email"
                                        id="email">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="lembaga"><b>Tempat Lembaga</b></label>
                                    <select name="lembaga" id="lembaga" class="form-control">
                                        <option value="">Pilih Lembaga</option>
                                        @foreach ($lembaga_ums as $ums)
                                            <option value="{{ $ums->id_lmbg }}" data-nama_lmbg="{{ $ums->nama_lmbg }}"
                                                data-place_state="{{ $ums->place_state }}">
                                                {{ $ums->nama_lmbg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="place_state"><b>Place State</b></label>
                                    <select name="place_state" id="place_state" class="form-control" disabled>
                                        <option value="">Place State</option>
                                        @foreach ($lembaga_ums as $ums)
                                            <option value="{{ $ums->id_lmbg }}">{{ $ums->nama_lmbg }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="mb-3 form-group">
                                    <label for="jabatan"><b>Jabatan</b></label>
                                    <select name="jabatan" id="jabatan" class="form-control">
                                        <option value="">Pilih Jabatan</option>
                                        @foreach ($jabatans as $jabatan)
                                            <option value="{{ $jabatan->uuid }}"
                                                data-nama_jabatan="{{ $ums->nama_jabatan }}">{{ $jabatan->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6" id="password_wrapper">
                                <div class="mb-3 form-group">
                                    <label for="password"><b>Password</b></label>
                                    <input type="password" class="form-control" placeholder="Masukkan Password"
                                        name="password" id="password">
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
    </div>

    <div class="modal fade" id="modal-assign" aria-labelledby="assignUserLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('user-management.assignRole') }}" method="post" id="formAssign">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignUserLabel">Assign User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_uuid" id="user_uuid">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Pilih Role:</label>
                                <div id="role-container">
                                    @foreach ($roles as $role)
                                        <div class="form-check">
                                            <input class="form-check-input role-checkbox" type="checkbox" name="roles[]"
                                                value="{{ $role->id }}" id="role_{{ $role->id }}">
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    @endforeach
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
    

@endsection

@push('scripts')
    <script>
        var getData = "{{ route('user-management.getData') }}";
        var role = @json($role);
        const $username = @json($username);
        </script>
    
    <script src="{{ asset('js/superadmin/user_management/index.js') }}"></script>
@endpush
