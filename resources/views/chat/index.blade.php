@extends('layouts.app')

@section('contents')
    <style>
        .chat-message {
            display: flex;
            margin-bottom: 10px;
            width: 100%;
        }

        .chat-message.sent {
            justify-content: flex-end;
        }

        .chat-message.received {
            justify-content: flex-start;
        }

        .chat-bubble {
            max-width: 60%;
            padding: 10px;
            border-radius: 10px;
            font-size: 14px;
            word-wrap: break-word;
            position: relative;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .sent .chat-bubble {
            background-color: #3c83ce;
            color: white;
            text-align: right;
            border-bottom-right-radius: 0;
        }

        .received .chat-bubble {
            background-color: #e9ecef;
            color: black;
            text-align: left;
            border-bottom-left-radius: 0;
        }

        .chat-bubble small {
            font-size: 10px;
            display: block;
            margin-top: 5px;
            opacity: 0.8;
        }

        #chat-box {
            display: flex;
            flex-direction: column;
            padding: 10px;
        }

        #user-list {
            max-height: 350px;
            overflow-y: auto;
            padding: 0;
        }

        #user-list .list-group-item {
            padding: 10px;
            margin: 5px 0;
            background-color: #f8f9fa;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }

        #userSearch {
            width: 100%;
            margin-bottom: 10px;
            font-size: 14px;
        }

        #user-list .badge {
            font-size: 12px;
            padding: 4px 8px;
            margin-top: 5px;
        }

        .hide-user {
            display: none !important;
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
                                <a href="{{ route('pengajuan.home') }}" class="btn btn-danger btn-sm shadow-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <!-- Custom Scrollbar -->
                        <div class="card-body">
                            <div class="container mt-4">
                                <div class="row">
                                    <!-- List User -->
                                    <div class="col-md-4 border border-primary rounded-3">
                                        <div class="row">
                                            <!-- Header -->
                                            <div class="col-12 bg-primary text-white p-3 rounded-top">
                                                <h4 class="mb-0">Users</h4>
                                            </div>

                                            <!-- User List -->
                                            <div class="col-12 bg-light p-3">
                                                <!-- Pencarian User -->
                                                <div class="mb-3">
                                                    <input type="text" id="userSearch" class="form-control"
                                                        placeholder="Cari Nama, Email, atau Jabatan" />
                                                </div>

                                                <!-- User List -->
                                                <div class="bg-light p-2" style="max-height: 400px; overflow-y: auto;">
                                                    <ul class="list-group" id="user-list">
                                                        @foreach ($users as $user)
                                                            <li class="list-group-item user-item d-flex align-items-center border-0 p-2"
                                                                data-id="{{ $user->id }}"
                                                                style="cursor: pointer; background: #f8f9fa; transition: 0.3s;">

                                                                <!-- Avatar -->
                                                                <img src="{{ $user->avatar_google ? $user->avatar_google : asset('/assets/images/profile/user-1.jpg') }}"
                                                                    alt="User Avatar"
                                                                    style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;"
                                                                    class="me-2 border border-secondary">

                                                                <!-- Info -->
                                                                <div class="d-flex flex-column">
                                                                    <div class="d-flex align-items-center">
                                                                        <strong class="text-dark me-1"
                                                                            style="font-size: 12px;">
                                                                            {{ $user->name }}
                                                                        </strong>

                                                                        @if ($user->getChat && count($user->getChat) > 0)
                                                                            <span class="badge bg-warning ms-2"
                                                                                style="font-size: 10px;">
                                                                                {{ count($user->getChat) }} Pesan Baru
                                                                            </span>
                                                                        @endif
                                                                    </div>

                                                                    <!-- Email -->
                                                                    <small class="text-muted"
                                                                        style="font-size: 10px;">{{ $user->email }}</small>

                                                                    <!-- Status Badge -->
                                                                    <div class="mt-1">
                                                                        @if ($user->hasRole('admin'))
                                                                            <span class="badge bg-primary"
                                                                                style="font-size: 10px!important;">Admin</span>
                                                                            <span class="badge bg-danger"
                                                                                style="font-size: 10px!important;">{{ $user->jabatan }}</span>
                                                                        @elseif($user->hasRole('verifikator'))
                                                                            <span class="badge bg-success"
                                                                                style="font-size: 10px!important;">Verifikator</span>
                                                                            <span class="badge bg-danger"
                                                                                style="font-size: 10px!important;">{{ $user->jabatan }}</span>
                                                                        @elseif($user->hasRole('user'))
                                                                            <span class="badge bg-secondary"
                                                                                style="font-size: 10px!important;">Pengusul</span>
                                                                            <span class="badge bg-danger"
                                                                                style="font-size: 10px!important;">{{ $user->jabatan }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Chat Box -->
                                    <div class="col-md-8">
                                        <h4>Chat</h4>
                                        <div class="chat-box border p-3" id="chat-box"
                                            style="height: 300px; overflow-y: auto; background: #ffffff;"></div>
                                        <input type="hidden" id="receiver_id" value="">
                                        <input type="hidden" id="id_mou" value="{{ @$id_mou }}">
                                        <textarea id="message" class="form-control mt-2" placeholder="Ketik Pesan..."></textarea>
                                        <button id="sendMessage" class="btn btn-primary mt-2 send-message">Kirim</button>
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
        $(document).ready(function() {
            $('#userSearch').on('keyup', function() {
                let search = $(this).val().toLowerCase().trim();
                let found = false;

                $('#not-found-message').remove();

                $('#user-list li.user-item').each(function() {

                    let userName = $(this).find('strong').first().text().toLowerCase();
                    let userEmail = $(this).find('small').first().text().toLowerCase();
                    let userJabatan = $(this).find('.badge.bg-danger').first().text().toLowerCase();

                    // Kondisi Pencarian
                    if (
                        userName.includes(search) ||
                        userEmail.includes(search) ||
                        userJabatan.includes(search)
                    ) {
                        $(this).removeClass('hide-user');
                        found = true;
                    } else {
                        $(this).addClass('hide-user');
                    }
                });

                // Jika tidak ada yang cocok
                if (!found) {
                    $('#user-list').append(`
                        <li id="not-found-message" class="list-group-item text-center text-muted border-0">
                            User tidak ditemukan
                        </li>
                    `);
                }
            });
        });

    </script>
    <script>
        var sender = @json($sender)
    </script>
    <script src="{{ asset('js/chat/index.js') }}"></script>
@endpush
