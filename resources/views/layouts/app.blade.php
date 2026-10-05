<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a5c4d">

    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'PPI Check') }}</title>

    {{-- Favicon & PWA --}}
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.min.css') }}">

    {{-- Font Inter (footer & elemen kecil) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<div class="app-wrapper" x-data="{ sidebarOpen: false }">

    {{-- ================= Sidebar ================= --}}
    <aside class="app-sidebar" :class="sidebarOpen && 'show'">
        <div class="brand">
            <img src="{{ \App\Models\Setting::logoUrl() }}" alt="Logo PPI Check">
            <div>
                <div class="title">PPI Check</div>
                <div class="subtitle">Audit &amp; Surveilans PPI</div>
            </div>
            <button class="btn btn-sm text-white ms-auto d-lg-none" @click="sidebarOpen = false" aria-label="Tutup menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="flex-grow-1 overflow-y-auto pb-3">
            <div class="nav-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('dashboard.unit') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            @can('conduct-audit')
            <div class="nav-label">Audit PPI</div>
            @foreach(\App\Models\AuditCategory::where('is_active', true)->get() as $cat)
                <a href="{{ route('audits.create', ['category' => $cat->code]) }}"
                   class="nav-link {{ request()->routeIs('audits.create') && request('category') === $cat->code ? 'active' : '' }}">
                    <i class="bi {{ $cat->icon }}"></i> {{ $cat->name }}
                </a>
            @endforeach
            <a href="{{ route('audits.index') }}" class="nav-link {{ request()->routeIs('audits.index') || request()->routeIs('audits.show') ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i> Riwayat Audit
            </a>
            @endcan

            <div class="nav-label">Surveilans</div>
            @php($openFindings = \App\Models\Finding::query()->when(auth()->user()->role === \App\Models\User::ROLE_UNIT, fn($q) => $q->where('unit_id', auth()->user()->unit_id))->whereIn('status', ['open','progress'])->count())
            <a href="{{ route('findings.index') }}" class="nav-link {{ request()->routeIs('findings.*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle"></i> Monitoring Temuan
                @if($openFindings > 0)
                    <span class="badge bg-danger ms-auto">{{ $openFindings }}</span>
                @endif
            </a>
            <a href="{{ route('followups.index') }}" class="nav-link {{ request()->routeIs('followups.index') ? 'active' : '' }}">
                <i class="bi bi-arrow-repeat"></i> Tindak Lanjut
            </a>
            <a href="{{ route('monitoring-limbah-tajam.index') }}" class="nav-link {{ request()->routeIs('monitoring-limbah-tajam.*') ? 'active' : '' }}">
                <i class="bi bi-eyedropper"></i> Monitoring Limbah Tajam
            </a>

            <div class="nav-label">Laporan</div>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan &amp; Rekap
            </a>

            @can('manage-masters')
            <div class="nav-label">Master Data</div>
            <a href="{{ route('masters.units.index') }}" class="nav-link {{ request()->routeIs('masters.units.*') ? 'active' : '' }}">
                <i class="bi bi-hospital"></i> Unit / Ruangan
            </a>
            @can('manage-users')
            <a href="{{ route('masters.users.index') }}" class="nav-link {{ request()->routeIs('masters.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> User
            </a>
            @endcan
            <a href="{{ route('masters.professions.index') }}" class="nav-link {{ request()->routeIs('masters.professions.*') ? 'active' : '' }}">
                <i class="bi bi-person-workspace"></i> Profesi
            </a>
            <a href="{{ route('masters.apd-types.index') }}" class="nav-link {{ request()->routeIs('masters.apd-types.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> Jenis APD
            </a>
            <a href="{{ route('masters.apd-actions.index') }}" class="nav-link {{ request()->routeIs('masters.apd-actions.*') ? 'active' : '' }}">
                <i class="bi bi-activity"></i> Tindakan APD
            </a>
            <a href="{{ route('masters.waste-types.index') }}" class="nav-link {{ request()->routeIs('masters.waste-types.*') ? 'active' : '' }}">
                <i class="bi bi-trash3"></i> Jenis Limbah
            </a>
            <a href="{{ route('masters.instruments.index') }}" class="nav-link {{ request()->routeIs('masters.instruments.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-pulse"></i> Instrumen Audit
            </a>

            <div class="nav-label">Pengaturan</div>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Pengaturan Aplikasi
            </a>
            @endcan
        </nav>

        <div class="sidebar-footer">
            <i class="bi bi-shield-fill-check"></i>
            {{ \App\Models\Setting::get('facility_name', config('app.name')) }}
        </div>
    </aside>

    @if(auth()->user()->role !== \App\Models\User::ROLE_UNIT)
    <div class="offcanvas-backdrop fade show d-lg-none" x-show="sidebarOpen" x-cloak style="background:rgba(0,0,0,.45)" @click="sidebarOpen=false"></div>
    @endif

    {{-- ================= Main ================= --}}
    <div class="app-main">

        <header class="app-topbar">
            <button class="btn btn-outline-secondary btn-sm d-lg-none" @click="sidebarOpen = true" aria-label="Buka menu">
                <i class="bi bi-list fs-5"></i>
            </button>

            <div class="d-none d-sm-block">
                <div class="fw-bold text-brand" style="font-size:1.02rem">@yield('page_title', 'Dashboard')</div>
                <div class="text-secondary" style="font-size:.75rem">{{ now()->translatedFormat('l, d F Y') }}</div>
            </div>

            <div class="ms-auto d-flex align-items-center gap-2">
                {{-- Notifikasi --}}
                @include('partials.notifications')

                @can('conduct-audit')
                <a href="{{ route('audits.create') }}" class="btn btn-brand btn-sm rounded-pill px-3">
                    <i class="bi bi-plus-lg me-1"></i><span class="d-none d-md-inline">Audit Baru</span><span class="d-md-none">Audit</span>
                </a>
                @endcan

                <div class="dropdown">
                    <button class="btn d-flex align-items-center gap-2 border-0" data-bs-toggle="dropdown">
                        <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="text-start d-none d-md-block">
                            <div class="fw-semibold" style="font-size:.85rem; line-height:1.1">{{ auth()->user()->name }}</div>
                            <div class="text-secondary" style="font-size:.7rem">{{ auth()->user()->role_label }}</div>
                        </div>
                        <i class="bi bi-chevron-down small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" style="border-radius:.9rem">
                        <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="app-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:.9rem">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:.9rem">
                    <div class="fw-semibold mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> Terdapat kesalahan:</div>
                    <ul class="mb-0" style="font-size:.85rem">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="app-footer no-print">
            <div class="footer-left">
                <span>&copy; <span data-year>{{ date('Y') }}</span> {{ config('app.name', 'PPI Check') }}</span>
                <span class="footer-sep"></span>
                <span>Aplikasi Audit &amp; Surveilans Pencegahan dan Pengendalian Infeksi (PPI)</span>
            </div>
            <div class="footer-right">
                <span>{{ \App\Models\Setting::get('facility_name', config('app.name')) }}</span>
                <span class="footer-sep"></span>
                <span>Powered by <a href="https://digimagine.web.id" target="_blank" rel="noopener">Digimagine</a></span>
            </div>
        </footer>
    </div>

    {{-- ================= Mobile bottom nav (4 ikon) ================= --}}
    <nav class="mobile-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard*') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> Beranda
        </a>
        @can('conduct-audit')
        <a href="{{ route('audits.create') }}" class="{{ request()->routeIs('audits.create') ? 'active' : '' }}">
            <i class="bi bi-plus-square"></i> Audit
        </a>
        @else
        <a href="{{ route('audits.index') }}" class="{{ request()->routeIs('audits.index') || request()->routeIs('audits.show') ? 'active' : '' }}">
            <i class="bi bi-journal-check"></i> Riwayat
        </a>
        @endcan
        <a href="{{ route('findings.index') }}" class="{{ request()->routeIs('findings.*') ? 'active' : '' }}">
            <span class="nav-icon-wrap">
                <i class="bi bi-exclamation-triangle"></i>
                @if($openFindings > 0)<span class="nav-badge">{{ $openFindings > 99 ? '99+' : $openFindings }}</span>@endif
            </span>
            Temuan
        </a>
        <a href="{{ route('followups.index') }}" class="{{ request()->routeIs('followups.index') ? 'active' : '' }}">
            <i class="bi bi-arrow-repeat"></i> Tindak
        </a>
    </nav>
</div>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" defer></script>
<script src="{{ asset('assets/js/alpine.min.js') }}" defer></script>
<script src="{{ asset('assets/js/chart.umd.js') }}" defer></script>
<script>
    // Register Service Worker (PWA)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('{{ asset('sw.js') }}');
        });
    }

    // Tahun berjalan (footer)
    document.querySelectorAll('[data-year]').forEach(el => el.textContent = new Date().getFullYear());

    // Efek motion saat halaman siap: progress bar terisi + angka count-up
    window.addEventListener('load', () => {
        document.querySelectorAll('.progress .progress-bar').forEach(el => {
            const w = el.style.width;
            if (!w) return;
            el.style.width = '0%';
            requestAnimationFrame(() => requestAnimationFrame(() => {
                el.style.transition = 'width 1s cubic-bezier(.22,.9,.35,1)';
                el.style.width = w;
            }));
        });

        document.querySelectorAll('[data-countup]').forEach(el => {
            const target = parseFloat(el.dataset.countup);
            if (isNaN(target)) return;
            const dec = parseInt(el.dataset.decimals || 0, 10);
            const dur = 1200;
            const t0 = performance.now();
            const step = now => {
                const p = Math.min((now - t0) / dur, 1);
                const eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
                el.textContent = (target * eased).toFixed(dec).replace('.', ',');
                if (p < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        });
    });
</script>
@stack('scripts')
</body>
</html>
