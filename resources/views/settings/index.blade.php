@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page_title', 'Parameter &amp; Profil Aplikasi')

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-hospital me-1 text-brand"></i> Profil Fasilitas
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="facility_name" class="form-label small fw-semibold">Nama Fasilitas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('facility_name') is-invalid @enderror" id="facility_name" name="facility_name"
                           form="settings-form" value="{{ old('facility_name', $settings['facility_name']) }}" required>
                    @error('facility_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Nama akan tampil pada header sidebar &amp; laporan PDF.</div>
                </div>
                <div class="mb-3">
                    <label for="facility_address" class="form-label small fw-semibold">Alamat</label>
                    <textarea class="form-control @error('facility_address') is-invalid @enderror" id="facility_address" name="facility_address"
                              form="settings-form" rows="2">{{ old('facility_address', $settings['facility_address']) }}</textarea>
                    @error('facility_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-sliders me-1 text-brand"></i> Ambang Batas Predikat (Nilai Kepatuhan)
            </div>
            <div class="card-body">
                <p class="text-secondary small">Menentukan predikat otomatis audit. Minimal untuk mendapat predikat tersebut (%, &ge;). Urutan harus: Sangat Baik &ge; Baik &ge; Cukup.</p>

                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <label for="threshold_sangat_baik" class="form-label small fw-semibold">Sangat Baik</label>
                        <div class="input-group">
                            <input type="number" step="0.1" min="0" max="100" class="form-control @error('threshold_sangat_baik') is-invalid @enderror"
                                   id="threshold_sangat_baik" name="threshold_sangat_baik" form="settings-form"
                                   value="{{ old('threshold_sangat_baik', $settings['threshold_sangat_baik']) }}" required>
                            <span class="input-group-text">%</span>
                            @error('threshold_sangat_baik')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label for="threshold_baik" class="form-label small fw-semibold">Baik</label>
                        <div class="input-group">
                            <input type="number" step="0.1" min="0" max="100" class="form-control @error('threshold_baik') is-invalid @enderror"
                                   id="threshold_baik" name="threshold_baik" form="settings-form"
                                   value="{{ old('threshold_baik', $settings['threshold_baik']) }}" required>
                            <span class="input-group-text">%</span>
                            @error('threshold_baik')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <label for="threshold_cukup" class="form-label small fw-semibold">Cukup</label>
                        <div class="input-group">
                            <input type="number" step="0.1" min="0" max="100" class="form-control @error('threshold_cukup') is-invalid @enderror"
                                   id="threshold_cukup" name="threshold_cukup" form="settings-form"
                                   value="{{ old('threshold_cukup', $settings['threshold_cukup']) }}" required>
                            <span class="input-group-text">%</span>
                            @error('threshold_cukup')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label for="follow_up_deadline_days" class="form-label small fw-semibold">Batas Waktu Tindak Lanjut</label>
                        <div class="input-group">
                            <input type="number" min="1" max="90" class="form-control @error('follow_up_deadline_days') is-invalid @enderror"
                                   id="follow_up_deadline_days" name="follow_up_deadline_days" form="settings-form"
                                   value="{{ old('follow_up_deadline_days', $settings['follow_up_deadline_days']) }}" required>
                            <span class="input-group-text">hari</span>
                            @error('follow_up_deadline_days')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-text">Batas waktu unit menindaklanjuti temuan sejak temuan dibuat.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="settings-form" method="POST" action="{{ route('settings.update') }}">
    @csrf
    @method('PUT')
    <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn btn-brand rounded-pill px-4">
            <i class="bi bi-check-lg me-1"></i> Simpan Pengaturan
        </button>
    </div>
</form>
@endsection
