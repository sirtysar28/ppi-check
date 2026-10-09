@extends('layouts.app')

@section('title', 'Riwayat Audit')
@section('page_title', 'Riwayat Audit')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3" style="border-radius:1rem 1rem 0 0">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label small mb-1 text-secondary">Cari</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="No. audit / unit / petugas..." value="{{ request('q') }}">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small mb-1 text-secondary">Kategori</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small mb-1 text-secondary">Unit</label>
                <select name="unit_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label small mb-1 text-secondary">Predikat</label>
                <select name="grade" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(['Sangat Baik', 'Baik', 'Cukup', 'Perlu Perbaikan'] as $g)
                        <option value="{{ $g }}" {{ request('grade') === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1 text-secondary">Dari</label>
                <input type="date" name="start" class="form-control form-control-sm" value="{{ request('start') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1 text-secondary">Sampai</label>
                <input type="date" name="end" class="form-control form-control-sm" value="{{ request('end') }}">
            </div>
            @if(auth()->user()->role === \App\Models\User::ROLE_AUDITOR)
                <div class="col-12 col-md-4 col-lg-2">
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="mine" id="mine" value="1" {{ request('mine') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label small" for="mine">Milik Saya</label>
                    </div>
                </div>
            @endif
            <div class="col-12 col-md-2 col-lg-1 d-grid">
                <button class="btn btn-brand btn-sm"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </form>
    </div>

    <div class="card-body px-0 py-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small">
                        <th class="ps-3">No. Audit</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Unit</th>
                        <th>Auditor</th>
                        <th class="text-center">Skor</th>
                        <th>Predikat</th>
                        <th class="text-center">Temuan</th>
                        <th class="pe-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $audit)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $audit->audit_number }}</td>
                            <td class="small">{{ $audit->audit_date->translatedFormat('d M Y') }} <span class="text-secondary">({{ $audit->shift_label }})</span></td>
                            <td><span class="badge bg-brand-light text-brand badge-rounded"><i class="bi {{ $audit->category->icon }} me-1"></i>{{ str_replace('Audit ', '', $audit->category->name) }}</span></td>
                            <td class="small">{{ $audit->unit->name }}</td>
                            <td class="small">{{ $audit->auditor->name }}</td>
                            <td class="text-center fw-bold">{{ $audit->compliance_percentage }}%</td>
                            <td><span class="badge badge-rounded bg-{{ \App\Support\Ppi::gradeColor($audit->grade) }}">{{ $audit->grade }}</span></td>
                            <td class="text-center">
                                @if($audit->findings->count() > 0)
                                    <span class="badge badge-rounded bg-danger-subtle text-danger">{{ $audit->findings->count() }}</span>
                                @else
                                    <span class="text-secondary small">-</span>
                                @endif
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('audits.show', $audit) }}" class="btn btn-sm btn-outline-brand" style="border-color:var(--ppi-teal); color:var(--ppi-teal)">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada data audit.
                                @can('conduct-audit')
                                    <a href="{{ route('audits.create') }}" class="btn btn-brand btn-sm rounded-pill mt-2"><i class="bi bi-plus-lg me-1"></i>Mulai Audit</a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($audits->hasPages())
        <div class="card-footer bg-white py-2" style="border-radius:0 0 1rem 1rem">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-secondary">Menampilkan {{ $audits->firstItem() }}–{{ $audits->lastItem() }} dari {{ $audits->total() }} audit</div>
                {{ $audits->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection
