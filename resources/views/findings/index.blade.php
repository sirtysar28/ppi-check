@extends('layouts.app')

@section('title', 'Monitoring Temuan')
@section('page_title', 'Monitoring Temuan')

@section('content')
{{-- Statistik status --}}
<div class="row g-3 mb-3">
    <div class="col-4">
        <a href="{{ route('findings.index', array_merge(request()->query(), ['status' => 'open'])) }}" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-unlock-fill"></i></div>
                    <div>
                        <div class="text-secondary small">Open</div>
                        <div class="fs-3 fw-bold text-danger lh-1">{{ $stats['open'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-4">
        <a href="{{ route('findings.index', array_merge(request()->query(), ['status' => 'progress'])) }}" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="text-secondary small">Progress</div>
                        <div class="fs-3 fw-bold text-warning lh-1">{{ $stats['progress'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-4">
        <a href="{{ route('findings.index', array_merge(request()->query(), ['status' => 'closed'])) }}" class="text-decoration-none">
            <div class="card stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check2-circle"></i></div>
                    <div>
                        <div class="text-secondary small">Closed</div>
                        <div class="fs-3 fw-bold text-success lh-1">{{ $stats['closed'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3" style="border-radius:1rem 1rem 0 0">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small mb-1 text-secondary">Cari</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="No. temuan / deskripsi / unit" value="{{ request('q') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1 text-secondary">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(\App\Models\Finding::STATUSES as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1 text-secondary">Tingkat</label>
                <select name="severity" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(\App\Models\Finding::SEVERITIES as $key => $label)
                        <option value="{{ $key }}" {{ request('severity') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1 text-secondary">Kategori</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ str_replace('Audit ', '', $cat->name) }}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->role !== \App\Models\User::ROLE_UNIT)
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1 text-secondary">Unit</label>
                <select name="unit_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->code }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-12 col-md-1 d-grid">
                <button class="btn btn-brand btn-sm"><i class="bi bi-funnel"></i></button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small">
                        <th class="ps-3">No. Temuan</th>
                        <th>Deskripsi</th>
                        <th>Unit</th>
                        <th>Kategori</th>
                        <th class="text-center">Tingkat</th>
                        <th class="text-center">Status</th>
                        <th>Batas Tindak Lanjut</th>
                        <th class="pe-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($findings as $finding)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $finding->finding_number }}</td>
                            <td class="small" style="max-width:280px">{{ Str::limit($finding->description, 70) }}</td>
                            <td class="small">{{ $finding->unit->name }}</td>
                            <td class="small"><i class="bi {{ $finding->category->icon }} me-1 text-brand"></i>{{ str_replace('Audit ', '', $finding->category->name) }}</td>
                            <td class="text-center"><span class="badge badge-rounded bg-{{ $finding->severityColor() }}">{{ strtoupper($finding->severity) }}</span></td>
                            <td class="text-center"><span class="badge badge-rounded bg-{{ $finding->statusColor() }}">{{ strtoupper($finding->status) }}</span></td>
                            <td class="small {{ $finding->due_date && $finding->due_date->isPast() && $finding->status !== 'closed' ? 'text-danger fw-bold' : '' }}">
                                {{ $finding->due_date?->translatedFormat('d M Y') ?? '-' }}
                            </td>
                            <td class="pe-3 text-end">
                                <a href="{{ route('findings.show', $finding) }}" class="btn btn-sm btn-outline-brand" style="border-color:var(--ppi-teal); color:var(--ppi-teal)"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-secondary py-5"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada temuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($findings->hasPages())
        <div class="card-footer bg-white py-2" style="border-radius:0 0 1rem 1rem">
            <div class="d-flex justify-content-center">
                {{ $findings->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection
