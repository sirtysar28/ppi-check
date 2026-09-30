@extends('layouts.app')

@section('title', 'Laporan & Rekap')
@section('page_title', 'Laporan &amp; Rekap')

@section('content')
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('reports.excel', array_filter($filters)) }}" class="btn btn-sm btn-success rounded-pill px-3">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </a>
        <a href="{{ route('reports.pdf', array_filter($filters)) }}" class="btn btn-sm btn-danger rounded-pill px-3" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>
    </div>
</div>

{{-- Ringkasan --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-brand-light text-brand"><i class="bi bi-clipboard2-check"></i></div>
                <div>
                    <div class="text-secondary small">Total Audit</div>
                    <div class="fw-bold fs-5">{{ $audits->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="text-secondary small">Rata-rata Kepatuhan</div>
                    <div class="fw-bold fs-5">{{ $audits->count() ? round($audits->avg('compliance_percentage'), 1) : 0 }}%</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <div class="text-secondary small">Total Temuan</div>
                    <div class="fw-bold fs-5">{{ $audits->sum(fn ($a) => $a->findings->count()) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-hospital"></i></div>
                <div>
                    <div class="text-secondary small">Unit Teraudit</div>
                    <div class="fw-bold fs-5">{{ $perUnit->count() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-header bg-white py-3" style="border-radius:1rem 1rem 0 0">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1 text-secondary">Dari Tanggal</label>
                <input type="date" name="start" class="form-control form-control-sm" value="{{ $filters['start'] ?? '' }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small mb-1 text-secondary">Sampai Tanggal</label>
                <input type="date" name="end" class="form-control form-control-sm" value="{{ $filters['end'] ?? '' }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1 text-secondary">Instrumen</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1 text-secondary">Unit</label>
                <select name="unit_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ ($filters['unit_id'] ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            @can('view-all-reports')
            <div class="col-6 col-md-2">
                <label class="form-label small mb-1 text-secondary">Auditor</label>
                <select name="auditor_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach($auditors as $auditor)
                        <option value="{{ $auditor->id }}" {{ ($filters['auditor_id'] ?? '') == $auditor->id ? 'selected' : '' }}>{{ $auditor->name }}</option>
                    @endforeach
                </select>
            </div>
            @endcan
            <div class="col-6 col-md-1 d-grid">
                <button class="btn btn-brand btn-sm"><i class="bi bi-funnel"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    {{-- Rekap per unit --}}
    <div class="col-12 col-xl-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-hospital me-1 text-brand"></i> Rekap Kepatuhan per Unit
            </div>
            <div class="card-body">
                @forelse($perUnit as $row)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-semibold">{{ $row['unit']->name }}</span>
                            <span class="text-secondary">{{ $row['count'] }} audit &bull; <b class="text-brand">{{ $row['avg'] }}%</b></span>
                        </div>
                        <div class="progress progress-kepatuhan" role="progressbar" aria-valuenow="{{ $row['avg'] }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-brand" style="width: {{ $row['avg'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-secondary py-4 small"><i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada data pada rentang ini.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Rekap per instrumen --}}
    <div class="col-12 col-xl-6">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-clipboard2-pulse me-1 text-brand"></i> Rekap per Instrumen Audit
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3">Instrumen</th>
                                <th class="text-center">Jumlah Audit</th>
                                <th class="text-center">Rata-rata</th>
                                <th class="pe-3" style="width:35%">Grafik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perCategory as $row)
                                <tr>
                                    <td class="ps-3 small fw-semibold"><i class="bi {{ $row['category']->icon }} me-1 text-brand"></i>{{ $row['category']->name }}</td>
                                    <td class="text-center small">{{ $row['count'] }}</td>
                                    <td class="text-center fw-bold">{{ $row['avg'] }}%</td>
                                    <td class="pe-3">
                                        <div class="progress progress-kepatuhan" style="height:8px">
                                            <div class="progress-bar bg-brand" style="width: {{ $row['avg'] }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-secondary py-4 small">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail audit --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-list-ul me-1 text-brand"></i> Detail Audit ({{ $audits->count() }})
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3">No. Audit</th>
                                <th>Tanggal</th>
                                <th>Instrumen</th>
                                <th>Unit</th>
                                <th>Auditor</th>
                                <th class="text-center">Skor</th>
                                <th class="text-center">Predikat</th>
                                <th class="text-center pe-3">Temuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audits as $audit)
                                <tr>
                                    <td class="ps-3 small fw-semibold">{{ $audit->audit_number }}</td>
                                    <td class="small">{{ $audit->audit_date->translatedFormat('d M Y') }}</td>
                                    <td class="small">{{ str_replace('Audit ', '', $audit->category->name) }}</td>
                                    <td class="small">{{ $audit->unit->name }}</td>
                                    <td class="small">{{ $audit->auditor->name }}</td>
                                    <td class="text-center fw-bold">{{ $audit->compliance_percentage }}%</td>
                                    <td class="text-center"><span class="badge badge-rounded bg-{{ \App\Support\Ppi::gradeColor($audit->grade) }}">{{ $audit->grade }}</span></td>
                                    <td class="text-center pe-3">
                                        @if($audit->findings->count())
                                            <span class="badge badge-rounded bg-danger-subtle text-danger">{{ $audit->findings->count() }}</span>
                                        @else
                                            <span class="text-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-secondary py-4 small">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
