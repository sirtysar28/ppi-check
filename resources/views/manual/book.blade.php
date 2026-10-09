@extends('layouts.app')

@section('title', 'Buku Manual')
@section('page_title', 'Buku Manual / Panduan Pengguna')

@push('styles')
<style>
    .manual-cover {
        background: linear-gradient(135deg, var(--ppi-teal-dark) 0%, var(--ppi-teal) 60%, #0f9b7e 100%);
        border-radius: 1.25rem;
        color: #fff;
        padding: 2rem 1.75rem;
        position: relative;
        overflow: hidden;
    }
    .manual-cover::after {
        content: "";
        position: absolute;
        inset: auto -60px -120px auto;
        width: 260px; height: 260px;
        background: rgba(255,255,255,.07);
        border-radius: 50%;
    }
    .manual-cover h1 { font-weight: 800; font-size: 1.6rem; margin-bottom: .35rem; }
    .manual-cover p { margin-bottom: 0; color: rgba(255,255,255,.85); font-size: .9rem; }
    .manual-meta { display: flex; flex-wrap: wrap; gap: .5rem 1.5rem; margin-top: 1.1rem; font-size: .8rem; color: rgba(255,255,255,.85); }
    .manual-meta i { margin-right: .35rem; }

    .manual-toc {
        background: #fff;
        border: 1px solid #e3ebe9;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
    }
    .manual-toc .toc-title { font-weight: 700; color: var(--ppi-teal-dark); display: flex; align-items: center; gap: .5rem; margin-bottom: .75rem; }
    .manual-toc ol { margin: 0; padding-left: 1.25rem; }
    .manual-toc ol li { margin: .3rem 0; }
    .manual-toc a { color: #235e53; text-decoration: none; }
    .manual-toc a:hover { color: var(--ppi-teal); text-decoration: underline; }

    .manual-section h2 {
        color: var(--ppi-teal-dark);
        font-weight: 800;
        font-size: 1.25rem;
        padding-bottom: .5rem;
        border-bottom: 2px solid var(--ppi-teal-light);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: .6rem;
        scroll-margin-top: 80px;
    }
    .manual-section h3 { font-size: 1rem; font-weight: 700; color: #173f38; margin-top: 1.25rem; }
    .manual-section h4 { font-size: .9rem; font-weight: 700; color: #2c5a51; margin-top: 1rem; }
    .manual-section p, .manual-section li { font-size: .92rem; color: #334e48; }
    .manual-card { background:#fff; border:1px solid #e3ebe9; border-radius:1rem; padding:1.25rem 1.4rem; margin-bottom:1rem; }
    .manual-card > h3:first-child, .manual-card > h4:first-child { margin-top: 0; }
    .manual-steps { counter-reset: step; list-style: none; padding-left: 0; margin: .75rem 0; }
    .manual-steps > li {
        counter-increment: step;
        position: relative;
        padding: 0 0 1rem 3rem;
        font-size: .92rem;
    }
    .manual-steps > li::before {
        content: counter(step);
        position: absolute; left: 0; top: -2px;
        width: 2rem; height: 2rem;
        border-radius: 50%;
        background: var(--ppi-teal-light);
        color: var(--ppi-teal-dark);
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem;
    }
    .manual-steps > li b { display: block; color: #173f38; }
    .manual-note {
        display: flex; gap: .65rem; align-items: flex-start;
        background: var(--ppi-teal-light);
        border-left: 4px solid var(--ppi-teal);
        border-radius: .6rem;
        padding: .7rem .9rem;
        font-size: .85rem;
        margin: .75rem 0;
    }
    .manual-warn {
        display: flex; gap: .65rem; align-items: flex-start;
        background: #fdf3e7;
        border-left: 4px solid #e28d3c;
        border-radius: .6rem;
        padding: .7rem .9rem;
        font-size: .85rem;
        margin: .75rem 0;
    }
    .manual-table { font-size: .88rem; }
    .manual-table th { background: var(--ppi-teal-light); color: var(--ppi-teal-dark); }
    .manual-menu-tree { background:#f7fbfa; border:1px dashed #bcd8d1; border-radius:.8rem; padding:1rem 1.25rem; font-size:.88rem; }
    .manual-menu-tree .mt-title { font-weight:700; color:var(--ppi-teal-dark); margin-bottom:.4rem; }
    .manual-menu-tree ul { list-style:none; padding-left:.9rem; margin:0; }
    .manual-menu-tree ul li { padding:.12rem 0; }
    .manual-menu-tree ul li::before { content: "▪"; color: var(--ppi-teal); margin-right: .5rem; }

    @media print {
        .app-sidebar, .app-topbar, .mobile-nav, .app-footer, .no-print { display: none !important; }
        .app-main { margin: 0 !important; }
        .manual-cover { border-radius: 0; }
        .manual-card { break-inside: avoid; }
        .manual-section h2 { break-after: avoid; }
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xxl-10">

        {{-- ============ SAMPUL ============ --}}
        <div class="manual-cover mb-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <img src="{{ \App\Models\Setting::logoUrl() }}" alt="Logo" style="width:64px;height:64px;background:#fff;border-radius:14px;padding:5px;object-fit:contain">
                <div>
                    <h1 id="manual-top">Buku Manual {{ ucfirst($manual === 'admin' ? 'Admin' : $manual) }} — {{ config('app.name', 'PPI Check') }}</h1>
                    <p>Aplikasi Audit &amp; Surveilans Pencegahan dan Pengendalian Infeksi (PPI)</p>
                </div>
            </div>
            <div class="manual-meta">
                <span><i class="bi bi-person-badge"></i>Peran: <b>{{ $roleLabel }}</b></span>
                <span><i class="bi bi-hospital"></i>{{ $facilityName }}</span>
                <span><i class="bi bi-calendar3"></i>Terbit: {{ now()->translatedFormat('F Y') }}</span>
                <span><i class="bi bi-tag"></i>Versi 1.0</span>
            </div>
        </div>

        {{-- Tombol cetak --}}
        <div class="d-flex justify-content-end gap-2 mb-3 no-print">
            <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill btn-sm"><i class="bi bi-printer me-1"></i>Cetak / Simpan PDF</button>
            <a href="#manual-top" class="btn btn-outline-brand rounded-pill btn-sm" style="border-color:var(--ppi-teal);color:var(--ppi-teal)"><i class="bi bi-arrow-up me-1"></i>Ke Atas</a>
        </div>

        {{-- ============ ISI MANUAL (menyesuaikan role) ============ --}}
        @include('manual.partials.' . $manual, ['roleLabel' => $roleLabel])

        <div class="manual-card mt-4 text-center">
            <div class="fw-bold text-brand"><i class="bi bi-shield-fill-check me-1"></i>{{ config('app.name', 'PPI Check') }}</div>
            <div class="small text-secondary mt-1">
                Buku manual ini spesifik untuk peran <b>{{ $roleLabel }}</b>. Pengguna dengan peran lain akan menerima buku manual yang berbeda sesuai hak aksesnya.
            </div>
        </div>
    </div>
</div>
@endsection
