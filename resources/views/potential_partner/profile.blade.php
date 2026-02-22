@extends('layouts.app')

@section('contents')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row pt-4">
                <div class="card profile-card p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-3 text-center">
                            <img src="{{ asset('images/logo_ums.png') }}" alt="Profile Image" class="profile-img">
                        </div>
                        <div class="col-md-9">
                            <h5 class="mb-4">Profile Information</h5>
                            <form>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Unid</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control-plaintext" value="int.offi">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Full Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control-plaintext"
                                            value="International Office UMS">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Email</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control-plaintext" value="int.office@ums.ac.id">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-sm-3 col-form-label">Phone Number</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control-plaintext" value="+62xxxxxxxxxx">
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-primary"><i
                                            class="bx bx-save me-2"></i>SIMPAN</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/potential_partner/profile.js') }}"></script>
@endpush
