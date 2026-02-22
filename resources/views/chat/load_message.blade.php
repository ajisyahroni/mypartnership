@if (!$receiver_id)
    <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
        <p class="text-center text-muted">👥 Pilih pengguna untuk memulai percakapan.</p>
    </div>
@else
    @if (count($messages) == 0)
        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
            <p class="text-center text-muted">📭 Anda Belum Memiliki Pesan di Kolom Chat Ini.</p>
        </div>
    @else
        @php
            $lastDate = null;
        @endphp

        @foreach ($messages as $item)
            @php
                $currentDate = $item->created_at->format('Y-m-d');
            @endphp

            {{-- Tampilkan header tanggal jika berbeda dari pesan sebelumnya --}}
            @if ($currentDate !== $lastDate)
                <div class="chat-date text-center">
                    <span class="badge bg-secondary" style="font-size: 12px;">
                        @if ($currentDate == now()->format('Y-m-d'))
                            Today
                        @elseif ($currentDate == now()->subDay()->format('Y-m-d'))
                            Yesterday
                        @else
                            {{ \Carbon\Carbon::parse($currentDate)->translatedFormat('l, d F Y') }}
                        @endif
                    </span>
                </div>
                @php
                    $lastDate = $currentDate;
                @endphp
            @endif

            @php
                $isSender = $item->sender_id == Auth::id() ? 'sent' : 'received';
            @endphp
            <div class="chat-message {{ $isSender }}">
                <div class="chat-bubble">
                    <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                    {{ $item->message }} <br><br>
                    @if ($isSender == 'sent')
                        @if ($item->is_seen)
                            <span class="badge bg-light" style="font-size:10px!important;color: green;">✔ Seen</span>
                        @else
                            <span class="badge bg-light" style="font-size:10px!important;color: red;">❌ Unseen</span>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach

    @endif
@endif
