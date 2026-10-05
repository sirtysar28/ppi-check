@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page_title', 'Pengaturan Aplikasi')

@push('styles')
<style>
    .settings-tabs .nav-link {
        border-radius: 999px;
        color: #48645d;
        font-weight: 600;
        font-size: .85rem;
        padding: .45rem 1rem;
        border: 1px solid transparent;
    }
    .settings-tabs .nav-link.active {
        background: var(--ppi-teal-light);
        color: var(--ppi-teal-dark);
        border-color: rgba(13, 122, 102, .25);
    }
    .logo-preview {
        width: 96px;
        height: 96px;
        border-radius: 1rem;
        background: #fff;
        border: 1px dashed #cfe0db;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
    }
    .logo-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
</style>
@endpush

@section('content')
<ul class="nav nav-pills settings-tabs gap-2 mb-3 no-print" role="tablist">
    <li class="nav-item">
        <button class="nav-link {{ !request()->query('tab') || request()->query('tab') === 'umum' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#tab-umum" type="button" role="tab">
            <i class="bi bi-sliders me-1"></i> Umum
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ request()->query('tab') === 'smtp' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#tab-smtp" type="button" role="tab">
            <i class="bi bi-envelope-gear me-1"></i> SMTP Email
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ request()->query('tab') === 'logo' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#tab-logo" type="button" role="tab">
            <i class="bi bi-image me-1"></i> Logo Aplikasi
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ request()->query('tab') === 'password' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#tab-password" type="button" role="tab">
            <i class="bi bi-shield-lock me-1"></i> Ganti Password
        </button>
    </li>
</ul>

<div class="tab-content">
    {{-- ============ TAB UMUM ============ --}}
    <div class="tab-pane fade show {{ !request()->query('tab') || request()->query('tab') === 'umum' ? 'active' : '' }}" id="tab-umum" role="tabpanel">
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
                            <div class="form-text">Nama akan tampil pada header sidebar, footer &amp; laporan PDF.</div>
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
                        <i class="bi bi-speedometer me-1 text-brand"></i> Ambang Batas Predikat (Nilai Kepatuhan)
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

        <form id="settings-form" method="POST" action="{{ route('settings.update') }}#umum">
            @csrf
            @method('PUT')
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-brand rounded-pill px-4">
                    <i class="bi bi-check-lg me-1"></i> Simpan Pengaturan Umum
                </button>
            </div>
        </form>
    </div>

    {{-- ============ TAB SMTP ============ --}}
    <div class="tab-pane fade {{ request()->query('tab') === 'smtp' ? 'show active' : '' }}" id="tab-smtp" role="tabpanel">
        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="bi bi-envelope-gear me-1 text-brand"></i> Konfigurasi SMTP (Pengiriman Email)
                    </div>
                    <div class="card-body">
                        <p class="text-secondary small">Konfigurasi ini dipakai aplikasi untuk mengirim email (mis. email percobaan &amp; notifikasi). Tidak perlu mengubah file <code>.env</code>.</p>

                        <div class="row g-3">
                            <div class="col-12 col-sm-4">
                                <label class="form-label small fw-semibold">Mailer <span class="text-danger">*</span></label>
                                <select name="mail_mailer" class="form-select" form="smtp-form">
                                    @foreach(['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'log' => 'Log (testing)'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('mail_mailer', $settings['mail_mailer']) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-8">
                                <label class="form-label small fw-semibold">SMTP Host <span class="text-danger">*</span></label>
                                <input type="text" name="mail_host" class="form-control" form="smtp-form"
                                       placeholder="cth: smtp.gmail.com" value="{{ old('mail_host', $settings['mail_host']) }}">
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="form-label small fw-semibold">Port <span class="text-danger">*</span></label>
                                <input type="number" name="mail_port" class="form-control" form="smtp-form"
                                       placeholder="587" value="{{ old('mail_port', $settings['mail_port']) }}">
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="form-label small fw-semibold">Enkripsi</label>
                                <select name="mail_encryption" class="form-select" form="smtp-form">
                                    @foreach(['' => 'Tanpa Enkripsi', 'tls' => 'TLS', 'ssl' => 'SSL'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('mail_encryption', $settings['mail_encryption']) === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label small fw-semibold">Username (SMTP)</label>
                                <input type="text" name="mail_username" class="form-control" form="smtp-form" autocomplete="off"
                                       placeholder="email@domain.com" value="{{ old('mail_username', $settings['mail_username']) }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-semibold">Password (SMTP)</label>
                                <input type="password" name="mail_password" class="form-control" form="smtp-form" autocomplete="new-password"
                                       placeholder="{{ $settings['mail_password'] ? 'Tersimpan — biarkan kosong bila tidak diubah' : 'Password / app password' }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-semibold">Alamat Pengirim <span class="text-danger">*</span></label>
                                <input type="email" name="mail_from_address" class="form-control @error('mail_from_address') is-invalid @enderror" form="smtp-form"
                                       placeholder="ppi@rumahsakit.co.id" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" required>
                                @error('mail_from_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label small fw-semibold">Nama Pengirim <span class="text-danger">*</span></label>
                                <input type="text" name="mail_from_name" class="form-control" form="smtp-form"
                                       value="{{ old('mail_from_name', $settings['mail_from_name']) }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="bi bi-send-check me-1 text-brand"></i> Email Percobaan
                    </div>
                    <div class="card-body">
                        <p class="text-secondary small">Simpan konfigurasi terlebih dahulu, lalu kirim email percobaan untuk memastikan SMTP berfungsi.</p>

                        @error('smtp_test')
                            <div class="alert alert-danger small py-2">{{ $message }}</div>
                        @enderror

                        <form method="POST" action="{{ route('settings.smtp.test') }}#smtp" id="smtp-test-form">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Kirim ke <span class="text-danger">*</span></label>
                                <input type="email" name="test_email_to" class="form-control" placeholder="email@domain.com"
                                       value="{{ auth()->user()->email }}" required>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 w-100">
                                <i class="bi bi-send me-1"></i> Kirim Email Percobaan
                            </button>
                        </form>

                        <div class="alert alert-light border small mt-3 mb-0" style="border-radius:.8rem">
                            <b>Tips Gmail:</b> gunakan <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">App Password</a>, bukan password akun biasa. Host <code>smtp.gmail.com</code>, port <code>587</code>, TLS.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="smtp-form" method="POST" action="{{ route('settings.smtp.update') }}#smtp">
            @csrf
            @method('PUT')
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-brand rounded-pill px-4">
                    <i class="bi bi-check-lg me-1"></i> Simpan Konfigurasi SMTP
                </button>
            </div>
        </form>
    </div>

    {{-- ============ TAB LOGO ============ --}}
    <div class="tab-pane fade {{ request()->query('tab') === 'logo' ? 'show active' : '' }}" id="tab-logo" role="tabpanel">
        <div class="row g-3">
            <div class="col-12 col-lg-7">
                <div class="card h-100">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="bi bi-image me-1 text-brand"></i> Ganti Logo Aplikasi
                    </div>
                    <div class="card-body">
                        <p class="text-secondary small">Logo tampil pada sidebar aplikasi, halaman login, dan halaman depan. Format PNG / JPG / WEBP / SVG, maksimal 2MB. Disarankan gambar persegi (rasio 1:1) dengan latar transparan.</p>

                        <form method="POST" action="{{ route('settings.logo.update') }}#logo" enctype="multipart/form-data">
                            @csrf
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="logo-preview">
                                    <img src="{{ \App\Models\Setting::logoUrl() }}" alt="Logo aplikasi">
                                </div>
                                <div class="flex-grow-1" style="min-width:220px">
                                    <label for="logo" class="form-label small fw-semibold">Pilih Berkas Logo <span class="text-danger">*</span></label>
                                    <input type="file" id="logo" name="logo" class="form-control @error('logo') is-invalid @enderror"
                                           accept=".png,.jpg,.jpeg,.webp,.svg,image/*" required onchange="previewLogo(this)">
                                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">Ukuran maksimal 2MB.</div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-3 flex-wrap">
                                <button type="submit" class="btn btn-brand rounded-pill px-4">
                                    <i class="bi bi-upload me-1"></i> Unggah Logo
                                </button>
                                @if($settings['app_logo'])
                                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4"
                                            formaction="{{ route('settings.logo.reset') }}#logo" formmethod="POST"
                                            onclick="this.form.action='{{ route('settings.logo.reset') }}'; this.form.removeAttribute('enctype'); this.form.querySelector('#logo').removeAttribute('required');">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Kembali ke Logo Bawaan
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card h-100 border-0" style="background:linear-gradient(180deg,#0a5c4d,#073d33);color:#e8f5f1">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center gap-2 py-5">
                        <div class="bg-white rounded-4 p-2" style="width:64px;height:64px">
                            <img src="{{ \App\Models\Setting::logoUrl() }}" alt="Logo" style="width:100%;height:100%;object-fit:contain">
                        </div>
                        <div class="fw-bold">PPI Check</div>
                        <div style="font-size:.75rem;opacity:.75">Audit &amp; Surveilans PPI</div>
                        <div class="small mt-2" style="opacity:.8">Pratinjau tampilan logo pada sidebar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TAB PASSWORD ============ --}}
    <div class="tab-pane fade {{ request()->query('tab') === 'password' ? 'show active' : '' }}" id="tab-password" role="tabpanel">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header bg-white fw-semibold py-3">
                        <i class="bi bi-shield-lock me-1 text-brand"></i> Ganti Password
                    </div>
                    <div class="card-body">
                        <p class="text-secondary small">Ganti password akun <b>{{ auth()->user()->name }}</b> ({{ auth()->user()->email }}).</p>

                        <form method="POST" action="{{ route('settings.password.update') }}#password">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Password Saat Ini <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" required>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" autocomplete="new-password" minlength="8" required>
                                @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Minimal 8 karakter dan berbeda dengan password lama.</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="new_password_confirmation" class="form-control" autocomplete="new-password" minlength="8" required>
                            </div>

                            <button type="submit" class="btn btn-brand rounded-pill px-4 w-100">
                                <i class="bi bi-key me-1"></i> Simpan Password Baru
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewLogo(input) {
        const preview = document.querySelector('.logo-preview img');
        if (input.files && input.files[0]) {
            preview.src = URL.createObjectURL(input.files[0]);
        }
    }

    // Buka tab sesuai fragment URL (#smtp, #logo, #password)
    document.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.replace('#', '');
        if (!hash) return;
        const trigger = document.querySelector(`[data-bs-target="#tab-${hash}"]`);
        if (trigger) new bootstrap.Tab(trigger).show();
    });
</script>
@endpush
