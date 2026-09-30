@extends('layouts.app')

@section('title', 'Detail Unit ' . $unit->name)
@section('page_title', 'Dashboard Unit')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

        <div class="card mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="small text-secondary">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard</a>
                    </div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $unit->name }}</h3>
                    <div class="small text-secondary">Kode: {{ $unit->code }} @if($unit->head_name) · Kepala: {{ $unit->head_name }} @endif</div>
                </div>
                <div class="text-center">
                    <div class="display-5 fw-bold {{ $overall >= \App\Support\Ppi::thresholds()['baik'] ? 'text-success' : ($overall >= \App\Support\Ppi::thresholds()['cukup'] ? 'text-warning' : 'text-danger') }}">{{ $overall }}%</div>
                    <div class="small text-secondary">Overall Compliance</div>
                </div>
                <form method="GET" class="d-flex gap-2 align-items-end no-print">
                    <div>
                        <label class="form-label xsmall text-secondary mb-1">Dari</label>
                        <input type="date" name="start" value="{{ $start?->format('Y-m-d') }}" class="form-control form-control-sm">
                    </div>
                    <div>
                        <label class="form-label xsmall text-secondary mb-1">Sampai</label>
                        <input type="date" name="end" value="{{ $end?->format('Y-m-d') }}" class="form-control form-control-sm">
                    </div>
                    <button class="btn btn-sm btn-brand"><i class="bi bi-funnel"></i></button>
                </form>
            </div>
        </div>

        {{-- Kepatuhan per kategori --}}
        <div class="row g-3 mb-3">
            @foreach($perCategory as $pc)
                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-semibold"><i class="bi {{ $pc['category']->icon }} me-1 text-brand"></i>{{ str_replace('Audit ', '', $pc['category']->name) }}</span>
                                <span class="badge bg-brand-light text-brand badge-rounded">{{ $pc['count'] }}x</span>
                            </div>
                            <div class="fs-4 fw-bold {{ $pc['avg'] >= \App\Support\Ppi::thresholds()['baik'] ? 'text-success' : ($pc['avg'] >= \App\Support\Ppi::thresholds()['cukup'] ? 'text-warning' : 'text-danger') }}">{{ $pc['avg'] }}%</div>
                            <div class="progress progress-kepatuhan mt-2">
                                <div class="progress-bar {{ $pc['avg'] >= \App\Support\Ppi::thresholds()['baik'] ? 'bg-success' : ($pc['avg'] >= \App\Support\Ppi::thresholds()['cukup'] ? 'bg-warning' : 'bg-danger') }}" style="width:{{ min(100, $pc['avg']) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Statistik --}}
        <div class="row g-3 mb-3">
            <div class="col-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-brand-light text-brand"><i class="bi bi-clipboard2-check"></i></div>
                        <div><div class="text-secondary small">Jumlah Audit</div><div class="fs-4 fw-bold">{{ $audits->count() }}</div></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                        <div><div class="text-secondary small">Jumlah Temuan</div><div class="fs-4 fw-bold">{{ $statFindings['total'] }}</div></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-unlock"></i></div>
                        <div><div class="text-secondary small">Temuan Open</div><div class="fs-4 fw-bold text-danger">{{ $statFindings['open'] }}</div></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check2-circle"></i></div>
                        <div><div class="text-secondary small">Temuan Closed</div><div class="fs-4 fw-bold text-success">{{ $statFindings['closed'] }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat audit unit --}}
        <div class="card">
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-clock-history me-2 text-brand"></i>Riwayat Audit Unit ({{ $audits->count() }})
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3">No. Audit</th>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Auditor</th>
                                <th class="text-center">Skor</th>
                                <th>Predikat</th>
                                <th class="pe-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audits as $audit)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $audit->audit_number }}</td>
                                    <td class="small">{{ $audit->audit_date->translatedFormat('d M Y') }}</td>
                                    <td class="small">{{ str_replace('Audit ', '', $audit->category->name) }}</td>
                                    <td class="small">{{ $audit->auditor->name }}</td>
                                    <td class="text-center fw-bold">{{ $audit->compliance_percentage }}%</td>
                                    <td><span class="badge badge-rounded bg-{{ \App\Support\Ppi::gradeColor($audit->grade) }}">{{ $audit->grade }}</span></td>
                                    <td class="pe-3 text-end"><a href="{{ route('audits.show', $audit) }}" class="btn btn-sm btn-outline-brand" style="border-color:var(--ppi-teal);color:var(--ppi-teal)"><i class="bi bi-eye"></i></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-secondary py-5">Belum ada audit untuk unit ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
