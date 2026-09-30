@extends('layouts.app')

@section('title', 'Tindak Lanjut')
@section('page_title', 'Tindak Lanjut Temuan')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-radius:1rem 1rem 0 0">
        <div class="small text-secondary">
            <i class="bi bi-info-circle me-1"></i>
            Daftar temuan yang memerlukan tindak lanjut oleh unit, diurutkan dari tingkat temuan tertinggi dan jatuh tempo terdekat.
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">Open + Progress</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="progress" {{ request('status') === 'progress' ? 'selected' : '' }}>Progress</option>
            </select>
            <button class="btn btn-sm btn-brand"><i class="bi bi-funnel"></i></button>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small">
                        <th class="ps-3">Temuan</th>
                        <th>Unit</th>
                        <th>Kategori</th>
                        <th class="text-center">Tingkat</th>
                        <th class="text-center">Status</th>
                        <th>Jatuh Tempo</th>
                        <th class="pe-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($findings as $finding)
                        @php($overdue = $finding->due_date && $finding->due_date->isPast())
                        <tr class="{{ $overdue ? 'table-danger' : '' }}">
                            <td class="ps-3">
                                <div class="fw-semibold">{{ $finding->finding_number }}</div>
                                <div class="small text-secondary">{{ Str::limit($finding->description, 60) }}</div>
                            </td>
                            <td class="small">{{ $finding->unit->name }}</td>
                            <td class="small"><i class="bi {{ $finding->category->icon }} me-1 text-brand"></i>{{ str_replace('Audit ', '', $finding->category->name) }}</td>
                            <td class="text-center"><span class="badge badge-rounded bg-{{ $finding->severityColor() }}">{{ strtoupper($finding->severity) }}</span></td>
                            <td class="text-center"><span class="badge badge-rounded bg-{{ $finding->statusColor() }}">{{ strtoupper($finding->status) }}</span></td>
                            <td class="small {{ $overdue ? 'fw-bold text-danger' : '' }}">
                                {{ $finding->due_date?->translatedFormat('d M Y') ?? '-' }}
                                @if($overdue)<div class="xsmall">TERLAMBAT</div>@endif
                            </td>
                            <td class="pe-3 text-end">
                                @if($finding->status === 'open')
                                    <a href="{{ route('findings.show', $finding) }}#isi-tindak-lanjut" class="btn btn-sm btn-brand rounded-pill">
                                        <i class="bi bi-reply me-1"></i>Tindak Lanjut
                                    </a>
                                @else
                                    <a href="{{ route('findings.show', $finding) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                        <i class="bi bi-eye me-1"></i>Lihat
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">
                                <i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i>
                                Tidak ada temuan yang perlu ditindaklanjuti. 🎉
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($findings->hasPages())
        <div class="card-footer bg-white py-2" style="border-radius:0 0 1rem 1rem">
            <div class="d-flex justify-content-center">{{ $findings->links('pagination::bootstrap-5') }}</div>
        </div>
    @endif
</div>
@endsection
