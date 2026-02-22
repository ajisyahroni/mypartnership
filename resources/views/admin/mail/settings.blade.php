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
                                        <i class="fa-solid fa-folder-open text-warning"></i>
                                    </span>{{ @$page_title }}
                                </h5>
                                <a href="{{ route('mail.isi_pesan') }}" class="btn btn-primary"> <i
                                        class="fas fa-tools me-2"></i>Setting Tampilan
                                    Pesan</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mt-4">
                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="table table-hover align-middle custom-table" id="dataTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Host</th>
                                                <th>Port</th>
                                                <th>User</th>
                                                <th>Nama</th>
                                                <th>Penerima Email</th>
                                                <th>Subjek Reply</th>
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
    </div>
    <div class="modal fade" id="modal-edit" aria-labelledby="DetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DetailLabel">Edit Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('mail.store_setting') }}" id="formInput" method="post">
                    <div class="modal-body">
                        @csrf
                        <input type="text" name="id_setting" id="id_setting" hidden>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="host">Host</label>
                                    <input type="text" class="form-control" id="host" name="host"
                                        placeholder="Masukkan Host">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="port">Port</label>
                                    <input type="text" class="form-control" id="port" name="port"
                                        placeholder="Masukkan Port">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="user">User</label>
                                    <input type="text" class="form-control" id="user" name="user"
                                        placeholder="Masukkan User">
                                </div>
                            </div>
                            {{-- <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="pass">Pass</label>
                                    <input type="password" class="form-control" id="pass" name="pass"
                                        placeholder="Masukkan Password">
                                </div>
                            </div> --}}
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="pass">Pass</label>

                                    <div class="input-group">
                                        <input type="password" class="form-control" id="pass" name="pass"
                                            placeholder="Masukkan Password">

                                        <button type="button" class="btn btn-outline-secondary" id="togglePass">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Masukkan Email">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="nama">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        placeholder="Masukkan Nama">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="reply_to">Reply To</label>
                                    <input type="text" class="form-control" id="reply_to" name="reply_to"
                                        placeholder="Masukkan Reply To">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="subjek_reply_to">Subjek Reply To</label>
                                    <input type="text" class="form-control" id="subjek_reply_to" name="subjek_reply_to"
                                        placeholder="Masukkan Subjek Reply To">
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="alert alert-primary d-flex align-items-center p-3 justify-content-between" role="alert"
                                    style="background-color: #cde6f8; border-left: 6px solid #325ff3;">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 text-white d-flex align-items-center justify-content-center"
                                            style="background-color: #325ff3; width: 30px; height: 30px; border-radius: 4px;">
                                            <i class="bx bx-info-circle"></i>
                                        </div>
                                        <div class="text-dark">
                                            Masukkan email penerima admin untuk menerima notifikasi email dari sistem. Pisahkan dengan koma jika lebih dari satu. <br>
                                            <b>Contoh : email1@gmail.com, email2@gmail.com, email3@gmail.com </b>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label for="email_receiver">Email Receiver</label>
                                    <textarea name="email_receiver" class="form-control" id="email_receiver" cols="30" rows="5"
                                        placeholder="Masukkan Email Receiver (pisahkan dengan koma)"></textarea>
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
        var getData = "{{ route('mail.getDataSetting') }}"
        var urlSwitchStatus = "{{ route('mail.switch_status') }}"
        
        $("#togglePass").on("click", function () {
            let input = $("#pass");
            let icon = $(this).find("i");

            if (input.attr("type") === "password") {
                input.attr("type", "text");
                icon.removeClass("bx-show").addClass("bx-hide");
            } else {
                input.attr("type", "password");
                icon.removeClass("bx-hide").addClass("bx-show");
            }
        });

    </script>
    <script src="{{ asset('js/admin/mail/settings.js') }}"></script>
@endpush
