<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a5c4d">
    <title>Masuk | {{ config('app.name', 'PPI Check') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">
    @vite('resources/css/app.css')
</head>
<body>
<div class="login-page">
    <div class="card login-card">
        <div class="card-body p-4 p-md-5" x-data="{ showPassword: false }">

            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PPI Check" class="login-logo mb-3">
                <h1 class="h4 fw-bold mb-1">PPI Check</h1>
                <p class="text-secondary small mb-0">Aplikasi Audit &amp; Surveilans<br>Pencegahan dan Pengendalian Infeksi</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success py-2 small" style="border-radius:.8rem" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 small" style="border-radius:.8rem" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" autocomplete="off">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white" style="border-radius:.65rem 0 0 .65rem"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                               name="email" value="{{ old('email') }}" placeholder="nama@rumahsakit.id"
                               required autofocus style="border-radius:0 .65rem .65rem 0; padding-block:.55rem">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Password + tombol intip --}}
                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white" style="border-radius:.65rem 0 0 .65rem"><i class="bi bi-key"></i></span>
                        <input :type="showPassword ? 'text' : 'password'" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" placeholder="Masukkan password" required
                               autocomplete="current-password" style="border-radius:0; padding-block:.55rem">
                        <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Sembunyikan password' : 'Lihat password'"
                                :title="showPassword ? 'Sembunyikan password' : 'Lihat password'"
                                style="border-radius:0 .65rem .65rem 0">
                            <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Captcha --}}
                <div class="mb-3">
                    <label for="captcha" class="form-label small fw-semibold">Kode Keamanan (Captcha)</label>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="captcha-box">
                            <span id="captchaImage" aria-hidden="true">
                                <img src="{{ route('captcha') }}?t={{ time() }}" alt="Captcha" width="190" height="64">
                            </span>
                            <button class="btn btn-sm btn-outline-secondary border-0" type="button" id="refreshCaptcha"
                                    title="Ganti kode captcha" aria-label="Ganti kode captcha"
                                    style="font-size:1.05rem">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                    <input type="text" class="form-control mt-2 @error('captcha') is-invalid @enderror" id="captcha"
                           name="captcha" placeholder="Ketik 5 karakter di atas" required
                           maxlength="5" autocomplete="off" autocapitalize="characters" spellcheck="false"
                           style="padding-block:.55rem">
                    <div class="form-text">Kode tidak membedakan huruf besar/kecil.</div>
                    @error('captcha')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small" for="remember">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-brand w-100 py-2 fw-semibold" style="border-radius:.75rem">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </form>

            <div class="text-center mt-4">
                <div class="border-top pt-3 small text-secondary">
                    <i class="bi bi-info-circle me-1"></i>
                    Hubungi Admin PPI untuk mendapatkan akses aplikasi
                </div>
                <a href="{{ route('landing') }}" class="small text-decoration-none mt-2 d-inline-block">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                </a>
            </div>

            <div class="text-center mt-3 small text-secondary">
                &copy; <span data-year>{{ date('Y') }}</span> {{ config('app.name') }} &middot;
                <a href="https://digimagine.web.id" target="_blank" rel="noopener" class="text-decoration-none">
                    Powered by <b class="text-brand">Digimagine</b>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Refresh captcha via fetch (tanpa reload halaman)
    document.getElementById('refreshCaptcha').addEventListener('click', async function () {
        const res = await fetch('{{ route('captcha') }}?t=' + Date.now(), { headers: { 'X-Requested-With': 'fetch' } });
        const svg = await res.text();
        document.getElementById('captchaImage').innerHTML = svg;
        document.getElementById('captcha').value = '';
        document.getElementById('captcha').focus();
    });

    // Tahun berjalan
    document.querySelectorAll('[data-year]').forEach(el => el.textContent = new Date().getFullYear());
</script>

{{-- Alpine.js: tombol intip password & interaksi form --}}
<script src="{{ asset('assets/js/alpine.min.js') }}" defer></script>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => navigator.serviceWorker.register('{{ asset('sw.js') }}'));
    }
</script>
</body>
</html>
