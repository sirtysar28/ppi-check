@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card text-center h-100">
            <div class="card-body p-4">
                <div class="mx-auto mb-3 d-grid place-items-center" style="width:86px;height:86px;border-radius:50%;background:var(--ppi-teal-light);color:var(--ppi-teal-dark);font-size:2.2rem;font-weight:800;display:grid;place-items:center">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="h5 fw-bold mb-1">{{ $user->name }}</h2>
                <span class="badge badge-rounded bg-brand-light text-brand mb-2">{{ $user->role_label }}</span>
                <div class="text-secondary small">
                    <div class="mb-1"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</div>
                    @if($user->unit)
                        <div class="mb-1"><i class="bi bi-hospital me-1"></i>{{ $user->unit->name }}</div>
                    @endif
                    @if($user->profession)
                        <div class="mb-1"><i class="bi bi-person-workspace me-1"></i>{{ $user->profession->name }}</div>
                    @endif
                    @if($user->phone)
                        <div class="mb-1"><i class="bi bi-telephone me-1"></i>{{ $user->phone }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-person-gear me-1 text-brand"></i> Ubah Profil
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label small fw-semibold">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label small fw-semibold">No. HP <span class="text-secondary fw-normal">(opsional)</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                   value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="text-secondary small mb-3">
                        <i class="bi bi-key me-1"></i> Ubah Password <span class="fw-normal">(kosongkan bila tidak ingin mengubah)</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="current_password" class="form-label small fw-semibold">Password Saat Ini</label>
                            <div class="input-group">
                                <input :type="showCurrent ? 'text' : 'password'" class="form-control @error('current_password') is-invalid @enderror" id="current_password"
                                       name="current_password" autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" @click="showCurrent = !showCurrent"
                                        :aria-label="showCurrent ? 'Sembunyikan password' : 'Lihat password'" :title="showCurrent ? 'Sembunyikan password' : 'Lihat password'">
                                    <i class="bi" :class="showCurrent ? 'bi-eye-slash' : 'bi-eye'"></i>
                                </button>
                                @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="new_password" class="form-label small fw-semibold">Password Baru</label>
                            <div class="input-group">
                                <input :type="showNew ? 'text' : 'password'" class="form-control @error('new_password') is-invalid @enderror" id="new_password"
                                       name="new_password" autocomplete="new-password" minlength="8">
                                <button class="btn btn-outline-secondary" type="button" @click="showNew = !showNew"
                                        :aria-label="showNew ? 'Sembunyikan password' : 'Lihat password'" :title="showNew ? 'Sembunyikan password' : 'Lihat password'">
                                    <i class="bi" :class="showNew ? 'bi-eye-slash' : 'bi-eye'"></i>
                                </button>
                                @error('new_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="new_password_confirmation" class="form-label small fw-semibold">Konfirmasi Password</label>
                            <div class="input-group">
                                <input :type="showConfirm ? 'text' : 'password'" class="form-control" id="new_password_confirmation"
                                       name="new_password_confirmation" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" @click="showConfirm = !showConfirm"
                                        :aria-label="showConfirm ? 'Sembunyikan password' : 'Lihat password'" :title="showConfirm ? 'Sembunyikan password' : 'Lihat password'">
                                    <i class="bi" :class="showConfirm ? 'bi-eye-slash' : 'bi-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-brand rounded-pill px-4">
                            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
