@extends('layouts.app')

@section('title', 'Riwayat Monitoring Limbah Tajam')
@section('page_title', 'Riwayat Monitoring Limbah Benda Tajam')

@section('content')
<div class="card mb-3 no-print">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label xsmall fw-semibold mb-1">Cari</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="No. monitoring / petugas / ruangan" value="{{ request('q') }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label xsmall fw-semibold mb-1">Ruangan</label>
                <select name="unit_id" class="form-select form-select-sm">
                    <option value="">Semua Ruangan</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label xsmall fw-semibold mb-1">Dari</label>
                <input type="date" name="start" class="form-control form-control-sm" value="{{ request('start') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label xsmall fw-semibold mb-1">Sampai</label>
                <input type="date" name="end" class="form-control form-control-sm" value="{{ request('end') }}">
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button class="btn btn-brand btn-sm rounded-pill px-3 flex-fill"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('monitoring-limbah-tajam.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Reset"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-radius:1rem 1rem 0 0">
        <span class="fw-semibold"><i class="bi bi-eyedropper me-2 text-brand"></i>Data Monitoring ({{ $monitorings->total() }})</span>
        <a href="{{ route('monitoring-limbah-tajam.create') }}" class="btn btn-brand btn-sm rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> Isi Lembar Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small">
                        <th class="ps-3">No. Monitoring</th>
                        <th>Tanggal</th>
                        <th>Ruangan</th>
                        <th>Petugas</th>
                        <th class="text-center">Ya / Tidak</th>
                        <th class="text-center">Persentase</th>
                        <th class="text-center">Predikat</th>
                        <th class="pe-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monitorings as $m)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $m->monitoring_number }}</td>
                            <td class="small">{{ $m->monitoring_date->translatedFormat('d M Y') }}</td>
                            <td class="small">{{ $m->unit?->name }}</td>
                            <td class="small">{{ $m->officer_name }}</td>
                            <td class="text-center small">
                                <span class="text-success fw-semibold">{{ $m->conform_items }}</span> /
                                <span class="text-danger fw-semibold">{{ $m->nonconform_items }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $m->compliance_percentage }}%</td>
                            <td class="text-center">
                                <span class="badge badge-rounded bg-{{ $m->gradeColor() }}">{{ $m->grade }}</span>
                            </td>
                            <td class="pe-3 text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('monitoring-limbah-tajam.show', $m) }}" class="btn btn-sm btn-outline-secondary" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('monitoring-limbah-tajam.pdf', $m) }}" class="btn btn-sm btn-outline-secondary" title="Unduh PDF" target="_blank"><i class="bi bi-file-earmark-pdf"></i></a>
                                    @can('manage-masters')
                                        <form method="POST" action="{{ route('monitoring-limbah-tajam.destroy', $m) }}" onsubmit="return confirm('Hapus data monitoring {{ $m->monitoring_number }}?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-secondary py-5">Belum ada data monitoring. <a href="{{ route('monitoring-limbah-tajam.create') }}">Isi lembar pertama &rarr;</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($monitorings->hasPages())
        <div class="card-footer bg-white">{{ $monitorings->links() }}</div>
    @endif
</div>
@endsection
