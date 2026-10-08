<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a5c4d">
    <meta name="description" content="PPI Check — Aplikasi Audit & Surveilans Pencegahan dan Pengendalian Infeksi. Audit cuci tangan, APD, dan penanganan limbah benda tajam dalam satu aplikasi.">

    <title>PPI Check | Audit &amp; Surveilans Pencegahan dan Pengendalian Infeksi</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">
    @vite('resources/css/app.css')
</head>
<body class="landing-body">

<!-- =============== Navbar =============== -->
@if (session('success'))
<div class="container-lg mt-3">
    <div class="alert alert-success alert-dismissible fade show mb-0 small" role="alert" style="border-radius:.9rem">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
</div>
@endif
<nav class="landing-navbar">
    <div class="container-lg d-flex align-items-center gap-2 py-2">
        <img src="{{ \App\Models\Setting::logoUrl() }}" alt="Logo PPI Check" class="landing-nav-logo">
        <div class="lh-sm">
            <div class="fw-bold text-white">PPI Check</div>
            <div class="small" style="color:rgba(255,255,255,.6)">Audit &amp; Surveilans PPI</div>
        </div>
        <a href="{{ route('login') }}" class="btn btn-light btn-sm rounded-pill fw-semibold ms-auto px-3 px-sm-4">
            <i class="bi bi-box-arrow-in-right me-1"></i><span class="d-none d-sm-inline">Masuk ke Aplikasi</span><span class="d-sm-none">Masuk</span>
        </a>
    </div>
</nav>

<!-- =============== Hero =============== -->
<header class="landing-hero">
    <div class="container-lg">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6 text-center text-lg-start">
                <span class="hero-chip"><i class="bi bi-shield-fill-check"></i> Sistem Informasi PPI Rumah Sakit</span>
                <h1 class="hero-title mt-3">
                    Pencegahan &amp; Pengendalian Infeksi dalam <span class="text-underline">Genggaman</span>
                </h1>
                <p class="hero-subtitle mx-auto mx-lg-0">
                    PPI Check memudahkan tim PPI melakukan <b>audit kepatuhan</b> cuci tangan, pemakaian APD,
                    dan penanganan limbah benda tajam — lengkap dari <b>monitoring temuan</b> sampai <b>tindak lanjut terverifikasi</b>.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-lg-start mt-4">
                    <a href="{{ route('login') }}" class="btn btn-warning fw-bold rounded-pill px-4 py-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Mulai Masuk
                    </a>
                    <a href="#fitur" class="btn btn-outline-light rounded-pill px-4 py-2">
                        <i class="bi bi-grid-1x2 me-1"></i> Lihat Fitur
                    </a>
                </div>

                <div class="row g-2 g-sm-3 mt-4 pt-2 justify-content-center justify-content-lg-start">
                    <div class="col-6 col-sm-4">
                        <div class="hero-stat">
                            <i class="bi bi-clipboard2-check-fill"></i>
                            <b>3+</b><span>Instrumen Audit</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="hero-stat">
                            <i class="bi bi-people-fill"></i>
                            <b>4</b><span>Peran Pengguna</span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="hero-stat">
                            <i class="bi bi-graph-up-arrow"></i>
                            <b>Real-time</b><span>Monitoring Temuan</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mockup HP -->
            <div class="col-lg-6">
                <div class="hero-mockup-wrap">
                    <div class="hero-mockup">
                        <div class="mockup-notch"></div>
                        <div class="mockup-head">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ \App\Models\Setting::logoUrl() }}" alt="">
                                <div class="lh-sm">
                                    <div class="text-white fw-bold small">Audit Cuci Tangan</div>
                                    <div class="tiny text-white-50">IGD &bull; Shift Pagi</div>
                                </div>
                                <span class="badge bg-success ms-auto rounded-pill">92%</span>
                            </div>
                        </div>
                        <div class="mockup-body">
                            <div class="mock-progress"><span style="width:92%"></span></div>
                            <div class="mock-item ok"><i class="bi bi-check-circle-fill"></i> Mencuci tangan 6 langkah</div>
                            <div class="mock-item ok"><i class="bi bi-check-circle-fill"></i> Sabun &amp; handrub tersedia</div>
                            <div class="mock-item ok"><i class="bi bi-check-circle-fill"></i> Mengeringkan tangan dengan tissue</div>
                            <div class="mock-item bad"><i class="bi bi-x-circle-fill"></i> Perhiasan masih digunakan</div>
                            <div class="mock-chip"><i class="bi bi-exclamation-triangle-fill"></i> 1 temuan dibuat otomatis</div>
                        </div>
                    </div>

                    <div class="float-card float-a">
                        <i class="bi bi-clipboard2-check-fill"></i>
                        <div><b>Kejujuran Data</b><span>Audit langsung di unit</span></div>
                    </div>
                    <div class="float-card float-b">
                        <i class="bi bi-arrow-repeat"></i>
                        <div><b>Tindak Lanjut</b><span>Terverifikasi auditor</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- =============== Fitur =============== -->
<section class="landing-section" id="fitur">
    <div class="container-lg">
        <div class="text-center mb-4 mb-md-5">
            <span class="section-chip">Fitur Utama</span>
            <h2 class="section-title">Semua kebutuhan PPI, satu aplikasi</h2>
            <p class="section-subtitle mx-auto">Dirancang mengikuti alur kerja nyata tim PPI rumah sakit — dari observasi di unit hingga laporan periodik.</p>
        </div>

        <div class="row g-3 g-md-4">
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#e0f2fe;color:#0369a1"><i class="bi bi-droplet-half"></i></div>
                    <h3>Audit Kepatuhan Cuci Tangan</h3>
                    <p>Observasi 6 langkah cuci tangan, ketersediaan fasilitas, dan kepatuhan petugas dengan skor otomatis.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#dcfce7;color:#15803d"><i class="bi bi-shield-check"></i></div>
                    <h3>Audit Pemakaian APD</h3>
                    <p>Periksa kelengkapan dan kesesuaian Alat Pelindung Diri sesuai risiko area layanan.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#fef3c7;color:#b45309"><i class="bi bi-recycle"></i></div>
                    <h3>Audit Penanganan Limbah Benda Tajam</h3>
                    <p>Ketersediaan safety box, pembuangan benda tajam yang aman, hingga pemantauan pengangkutan.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#fee2e2;color:#b91c1c"><i class="bi bi-exclamation-triangle"></i></div>
                    <h3>Monitoring Temuan</h3>
                    <p>Setiap jawaban "Tidak" pada audit menjadi temuan bernomor otomatis yang terpantau statusnya.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#ede9fe;color:#6d28d9"><i class="bi bi-arrow-repeat"></i></div>
                    <h3>Tindak Lanjut &amp; Verifikasi</h3>
                    <p>Unit menindaklanjuti temuan, auditor memverifikasi — semuanya terdokumentasi rapi.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:#e6f4f1;color:#0d7a66"><i class="bi bi-file-earmark-bar-graph"></i></div>
                    <h3>Laporan Otomatis</h3>
                    <p>Rekap kepatuhan per unit/instrumen siap unduh dalam format Excel &amp; PDF.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =============== Alur Kerja =============== -->
<section class="landing-section section-tinted" id="alur">
    <div class="container-lg">
        <div class="text-center mb-4 mb-md-5">
            <span class="section-chip">Alur Kerja</span>
            <h2 class="section-title">Dari observasi sampai tuntas</h2>
        </div>

        <div class="row g-3 g-md-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-num">1</div>
                    <i class="bi bi-clipboard2-pulse step-icon"></i>
                    <h3>Audit di Unit</h3>
                    <p>Auditor mengisi checklist observasi langsung dari HP atau tablet di unit layanan.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-num">2</div>
                    <i class="bi bi-exclamation-triangle step-icon"></i>
                    <h3>Temuan Terdata</h3>
                    <p>Jawaban "Tidak" otomatis menjadi temuan bernomor dengan status terbuka.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-num">3</div>
                    <i class="bi bi-arrow-repeat step-icon"></i>
                    <h3>Tindak Lanjut Unit</h3>
                    <p>Unit terkait mengirimkan bukti perbaikan melalui aplikasi.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="step-card">
                    <div class="step-num">4</div>
                    <i class="bi bi-patch-check-fill step-icon"></i>
                    <h3>Verifikasi &amp; Laporan</h3>
                    <p>Auditor memverifikasi hasil perbaikan, rekap tersedia untuk manajemen.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =============== Peran =============== -->
<section class="landing-section" id="peran">
    <div class="container-lg">
        <div class="text-center mb-4 mb-md-5">
            <span class="section-chip">Multi Peran</span>
            <h2 class="section-title">Satu aplikasi, empat peran</h2>
        </div>
        <div class="row g-3 g-md-4 justify-content-center">
            <div class="col-12 col-sm-6 col-lg-3"><div class="role-card"><i class="bi bi-person-gear"></i><b>Super Admin</b><span>Kelola pengguna &amp; seluruh master data</span></div></div>
            <div class="col-12 col-sm-6 col-lg-3"><div class="role-card"><i class="bi bi-heart-pulse"></i><b>Admin PPI</b><span>Atur instrumen audit &amp; pantau seluruh unit</span></div></div>
            <div class="col-12 col-sm-6 col-lg-3"><div class="role-card"><i class="bi bi-clipboard2-check"></i><b>Auditor</b><span>Melakukan audit &amp; verifikasi tindak lanjut</span></div></div>
            <div class="col-12 col-sm-6 col-lg-3"><div class="role-card"><i class="bi bi-hospital"></i><b>Unit / Petugas</b><span>Memantau temuan &amp; mengirim tindak lanjut</span></div></div>
        </div>
    </div>
</section>

<!-- =============== CTA =============== -->
<section class="landing-cta">
    <div class="container-lg text-center">
        <img src="{{ \App\Models\Setting::logoUrl() }}" alt="Logo PPI Check" class="cta-logo mb-3">
        <h2 class="text-white fw-bold">Siap meningkatkan kepatuhan PPI?</h2>
        <p class="mb-4" style="color:rgba(255,255,255,.75)">Masuk sekarang dan mulai audit pertama Anda hari ini.</p>
        <a href="{{ route('login') }}" class="btn btn-light btn-lg rounded-pill fw-bold px-5">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Aplikasi
        </a>
    </div>
</section>

<!-- =============== Footer =============== -->
<footer class="landing-footer">
    <div class="container-lg text-center">
        <div class="fw-bold text-brand"><i class="bi bi-shield-fill-check me-1"></i> PPI Check</div>
        <div class="small text-secondary mt-1">Aplikasi Audit &amp; Surveilans Pencegahan dan Pengendalian Infeksi</div>
        <div class="small text-secondary mt-2">
            &copy; <span data-year>{{ date('Y') }}</span> {{ config('app.name') }} &mdash; Seluruh hak cipta dilindungi.
        </div>
        <div class="small mt-1">
            <a href="https://digimagine.web.id" target="_blank" rel="noopener" class="text-decoration-none text-secondary">
                Powered by <b class="text-brand">Digimagine</b>
            </a>
        </div>
    </div>
</footer>

<!-- =============== Mobile sticky CTA =============== -->
<div class="mobile-cta-bar d-lg-none">
    <a href="{{ route('login') }}" class="btn btn-warning w-100 fw-bold rounded-pill py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Aplikasi
    </a>
</div>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => navigator.serviceWorker.register('{{ asset('sw.js') }}'));
    }

    // Tahun berjalan (footer)
    document.querySelectorAll('[data-year]').forEach(el => el.textContent = new Date().getFullYear());
</script>
</body>
</html>
