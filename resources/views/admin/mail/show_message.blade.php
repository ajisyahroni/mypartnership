<h4>Isi Pesan</h4>
<div class="alert alert-secondary d-flex align-items-center p-3" role="alert"
    style="background-color: #e9e9e9; border-left: 6px solid #b0b0b0;">
    <div class="text-dark text-center">
        {!! $dataMail->pesan_sent ?? 'Tidak Pesan' !!}
    </div>
</div>

<h4>Isi Debug</h4>
@if ($dataMail->status_sent == 'Sukses')
    <div class="alert alert-danger d-flex align-items-center p-3" role="alert"
        style="background-color: #9ef4c9; border-left: 6px solid #2dcd57;">
        <div class="me-3 text-white d-flex align-items-center justify-content-center"
            style="background-color: #2dcd57; width: 30px; height: 30px; border-radius: 4px;">
            <i class="bx bx-info-circle"></i>
        </div>
        <div class="text-dark">
            {!! $dataMail->debug_error ?? 'Tidak ada Debug' !!}
        </div>
    </div>
@else
    <div class="alert alert-danger d-flex align-items-center p-3" role="alert"
        style="background-color: #f8cdcd; border-left: 6px solid #f33232;">
        <div class="me-3 text-white d-flex align-items-center justify-content-center"
            style="background-color: #f33232; width: 30px; height: 30px; border-radius: 4px;">
            <i class="bx bx-info-circle"></i>
        </div>
        <div class="text-dark">
            {!! $dataMail->debug_error ?? 'Tidak ada Debug' !!}
        </div>
    </div>
@endif
