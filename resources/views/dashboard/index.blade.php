@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard PPI')

@section('content')

{{-- ============ Filter ============ --}}
<div class="card mb-3 no-print">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small mb-1 text-secondary">Dari Tanggal</label>
                <input type="date" name="start" value="{{ $filters['start'] }}" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small mb-1 text-secondary">Sampai Tanggal</label>
                <input type="date" name="end" value="{{ $filters['end'] }}" class="form-control form-control-sm">
            </div>
            @if(auth()->user()->role !== \App\Models\User::ROLE_UNIT)
            <div class="col-12 col-md-4 col-lg-2">
                <label class="form-label small mb-1 text-secondary">Unit</label>
                <select name="unit_id" class="form-select form-select-sm">
                    <option value="">Semua Unit</option>
                    @foreach(\App\Models\Unit::where('is_active', true)->orderBy('name')->get() as $unit)
                        <option value="{{ $unit->id }}" {{ (string)$filters['unit_id'] === (string)$unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small mb-1 text-secondary">Kategori</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach(\App\Models\AuditCategory::where('is_active', true)->get() as $cat)
                        <option value="{{ $cat->id }}" {{ (string)$filters['category_id'] === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small mb-1 text-secondary">Auditor</label>
                <select name="auditor_id" class="form-select form-select-sm">
                    <option value="">Semua Auditor</option>
                    @foreach(\App\Models\User::where('role', \App\Models\User::ROLE_AUDITOR)->orderBy('name')->get() as $aud)
                        <option value="{{ $aud->id }}" {{ (string)$filters['auditor_id'] === (string)$aud->id ? 'selected' : '' }}>{{ $aud->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-1">
                <label class="form-label small mb-1 text-secondary">Shift</label>
                <select name="shift" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach(\App\Models\Audit::SHIFTS as $key => $label)
                        <option value="{{ $key }}" {{ $filters['shift'] === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4 col-lg-1 d-grid">
                <button class="btn btn-brand btn-sm" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- ============ Kartu Ringkasan ============ --}}
<div class="row g-3 mb-3 reveal-group">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-brand-light text-brand"><i class="bi bi-clipboard2-check"></i></div>
                <div>
                    <div class="text-secondary small">Total Audit</div>
                    <div class="fs-3 fw-bold text-brand lh-1"><span data-countup="{{ $totalAudits }}">0</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-brand-light text-brand"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="text-secondary small">Kepatuhan Rata-rata</div>
                    <div class="fs-3 fw-bold text-brand lh-1"><span data-countup="{{ $avgOverall }}" data-decimals="{{ str_contains((string) $avgOverall, '.') ? 1 : 0 }}">0</span>%</div>
                </div>
            </div>
        </div>
    </div>
    @foreach($perCategory as $pc)
        <div class="col-6 col-lg-2">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-secondary"><i class="bi {{ $pc['category']->icon }}"></i> {{ str_replace('Audit ', '', $pc['category']->name) }}</span>
                    </div>
                    <div class="fs-3 fw-bold lh-1 {{ $pc['avg'] >= \App\Support\Ppi::thresholds()['baik'] ? 'text-success' : ($pc['avg'] >= \App\Support\Ppi::thresholds()['cukup'] ? 'text-warning' : 'text-danger') }}">{{ $pc['avg'] }}%</div>
                    <div class="progress progress-kepatuhan mt-2">
                        <div class="progress-bar {{ $pc['avg'] >= \App\Support\Ppi::thresholds()['baik'] ? 'bg-success' : ($pc['avg'] >= \App\Support\Ppi::thresholds()['cukup'] ? 'bg-warning' : 'bg-danger') }}"
                             style="width: {{ min(100, $pc['avg']) }}%"></div>
                    </div>
                    <div class="small text-secondary mt-1">{{ $pc['count'] }} audit</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ============ Grafik ============ --}}
<div class="row g-3 mb-3 reveal-group">
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-graph-up me-1 text-brand"></i>Tren Kepatuhan PPI</h6>
                    <span class="badge bg-brand-light text-brand badge-rounded">6 bulan</span>
                </div>
                <div class="chart-box chart-box-trend">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-exclamation-diamond me-1 text-brand"></i>Status Temuan</h6>
                <div class="chart-box chart-box-donut">
                    <canvas id="findingChart"></canvas>
                </div>
                <div class="row text-center mt-3 g-2">
                    <div class="col-4">
                        <div class="fw-bold fs-5 text-danger">{{ $findingStats['open'] }}</div>
                        <div class="small text-secondary">Open</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-5 text-warning">{{ $findingStats['progress'] }}</div>
                        <div class="small text-secondary">Progress</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-5 text-success">{{ $findingStats['closed'] }}</div>
                        <div class="small text-secondary">Closed</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3 reveal-group">
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-hospital me-1 text-brand"></i>Kepatuhan per Unit</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="small text-secondary">
                                <th>Unit</th>
                                <th class="text-center">Audit</th>
                                <th style="min-width:140px">Kepatuhan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perUnit as $row)
                                <tr>
                                    <td>
                                        <a href="{{ route('dashboard.unit', $row['unit']) }}" class="fw-semibold text-decoration-none text-brand">
                                            {{ $row['unit']->name }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $row['count'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress progress-kepatuhan flex-grow-1">
                                                <div class="progress-bar {{ $row['avg'] >= \App\Support\Ppi::thresholds()['baik'] ? 'bg-success' : ($row['avg'] >= \App\Support\Ppi::thresholds()['cukup'] ? 'bg-warning' : 'bg-danger') }}" style="width: {{ min(100, $row['avg']) }}%"></div>
                                            </div>
                                            <span class="small fw-semibold">{{ $row['avg'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-secondary py-4">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="row g-3">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-1 text-brand"></i>Audit Terbaru</h6>
                            <a href="{{ route('audits.index') }}" class="small text-decoration-none">Lihat semua</a>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($recentAudits as $audit)
                                <a href="{{ route('audits.show', $audit) }}" class="list-group-item list-group-item-action px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold small">{{ $audit->audit_number }} · {{ $audit->unit->name }}</div>
                                        <div class="xsmall text-secondary"><i class="bi {{ $audit->category->icon }} me-1"></i>{{ $audit->category->name }} · {{ $audit->audit_date->translatedFormat('d M Y') }}</div>
                                    </div>
                                    <span class="badge badge-rounded bg-{{ \App\Support\Ppi::gradeColor($audit->grade) }}">{{ $audit->compliance_percentage }}%</span>
                                </a>
                            @empty
                                <div class="text-center text-secondary py-4 small">Belum ada audit</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-1 text-danger"></i>Temuan Aktif</h6>
                            <a href="{{ route('findings.index') }}" class="small text-decoration-none">Lihat semua</a>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($recentFindings as $finding)
                                <a href="{{ route('findings.show', $finding) }}" class="list-group-item list-group-item-action px-0">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold small">{{ $finding->finding_number }}</span>
                                        <span class="badge badge-rounded bg-{{ $finding->severityColor() }}">{{ strtoupper($finding->severity) }}</span>
                                    </div>
                                    <div class="xsmall text-secondary">{{ Str::limit($finding->description, 60) }} · {{ $finding->unit->name }}</div>
                                </a>
                            @empty
                                <div class="text-center text-secondary py-4 small">Tidak ada temuan aktif 🎉</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trend = @json($trend);
        const ctx1 = document.getElementById('trendChart');
        if (ctx1) {
            const cvs = ctx1.getContext('2d');
            const grad = cvs.createLinearGradient(0, 0, 0, 280);
            grad.addColorStop(0, 'rgba(13,122,102,.30)');
            grad.addColorStop(1, 'rgba(13,122,102,0)');

            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: trend.map(t => t.label),
                    datasets: [{
                        label: 'Kepatuhan (%)',
                        data: trend.map(t => t.avg),
                        borderColor: '#0d7a66',
                        backgroundColor: grad,
                        fill: 'start',
                        tension: .42,
                        spanGaps: true,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0d7a66',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#0d7a66',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1500, easing: 'easeOutQuart' },
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%', padding: 6 }, grid: { color: 'rgba(0,0,0,.05)' } },
                        x: { grid: { display: false } }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0a5c4d', padding: 10, cornerRadius: 10, displayColors: false,
                            callbacks: { label: c => ' Kepatuhan: ' + c.parsed.y + '%' }
                        }
                    }
                }
            });
        }

        const stats = @json($findingStats);
        const ctx2 = document.getElementById('findingChart');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Open', 'Progress', 'Closed'],
                    datasets: [{
                        data: [stats.open, stats.progress, stats.closed],
                        backgroundColor: ['#dc3545', '#ffc107', '#198754'],
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    animation: { animateRotate: true, animateScale: true, duration: 1400, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, padding: 14 } },
                        tooltip: { backgroundColor: '#0a5c4d', padding: 10, cornerRadius: 10 }
                    }
                }
            });
        }
    });
</script>
@endpush
