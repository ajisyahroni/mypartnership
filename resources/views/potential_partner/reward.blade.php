@extends('layouts.app')

@section('contents')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <!-- Skor Instansi Card -->
                <div class="col-xl-3 col-md-6 col-12 mb-4 mt-3">
                    <div class="card shadow-sm border-0 rounded-4 text-center">
                        <div class="card-header bg-primary text-white rounded-top-4">
                            <h6 class="mb-0 text-light d-flex justify-content-center align-items-center">
                                <i class="fas fa-trophy me-2"></i> MY REWARD
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="icon-circle mx-auto mb-3">
                                <i class='bx bx-reward text-primary' style="font-size:30px!important"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">3</h4>
                            <p class="text-muted mb-0">Reward Point</p>
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-sm btn-primary">MY REWARD</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/potential_partner/reward.js') }}"></script>
@endpush
