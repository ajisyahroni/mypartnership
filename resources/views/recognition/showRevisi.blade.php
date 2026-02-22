<div class="alert alert-danger d-flex align-items-center p-3 justify-content-between" role="alert"
    style="background-color: #f8cdcd; border-left: 6px solid #f33232;">
    <div class="d-flex align-items-center">
        <div class="me-3 text-white d-flex align-items-center justify-content-center"
            style="background-color: #f33232; width: 30px; height: 30px; border-radius: 4px;">
            <i class="bx bx-info-circle"></i>
        </div>
        <div class="text-dark">
            {{ $catatan }}
        </div>
    </div>
    @if ($dataRecognition->add_by == auth()->user()->username)
        <a href="{{ $urlEdit }}" class="btn btn-danger">
            <i class="bx bx-edit"></i> Perbaiki Data
        </a>
    @endif
</div>
