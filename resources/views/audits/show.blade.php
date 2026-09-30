@extends('layouts.app')

@section('title', 'Hasil Audit ' . $audit->audit_number)
@section('page_title', 'Hasil Audit')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

        {{-- Ringkasan skor --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-brand-light text-brand badge-rounded"><i class="bi {{ $audit->category->icon }} me-1"></i>{{ $audit->category->name }}</span>
                            <span class="badge badge-rounded bg-{{ $audit->gradeColor() }}">{{ $audit->grade }}</span>
                        </div>
                        <h4 class="fw-bold mb-0">{{ $audit->audit_number }}</h4>
                        <div class="small text-secondary mt-1">
                            {{ $audit->unit->name }} · {{ $audit->audit_date->translatedFormat('d F Y') }} · Shift {{ $audit->shift_label }}
                            @if($audit->officer_name) · Petugas: {{ $audit->officer_name }} @endif
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="display-5 fw-bold {{ $audit->compliance_percentage >= \App\Support\Ppi::thresholds()['baik'] ? 'text-success' : ($audit->compliance_percentage >= \App\Support\Ppi::thresholds()['cukup'] ? 'text-warning' : 'text-danger') }}">
                            {{ $audit->compliance_percentage }}%
                        </div>
                        <div class="small text-secondary">Nilai Kepatuhan</div>
                    </div>
                    <div class="d-flex gap-2 no-print">
                        <a href="{{ route('audits.pdf', $audit) }}" class="btn btn-outline-danger rounded-pill btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
                        @can('conduct-audit')
                            <a href="{{ route('audits.create', ['category' => $audit->category->code]) }}" class="btn btn-brand rounded-pill btn-sm"><i class="bi bi-plus-lg me-1"></i>Audit Lagi</a>
                        @endcan
                    </div>
                </div>
                <hr>
                <div class="row text-center g-2 small">
                    <div class="col-3 col-md-2">
                        <div class="fw-bold fs-5">{{ $audit->total_items }}</div>
                        <div class="text-secondary">Total Item</div>
                    </div>
                    <div class="col-3 col-md-2">
                        <div class="fw-bold fs-5 text-success">{{ $audit->conform_items }}</div>
                        <div class="text-secondary">Sesuai</div>
                    </div>
                    <div class="col-3 col-md-2">
                        <div class="fw-bold fs-5 text-danger">{{ $audit->nonconform_items }}</div>
                        <div class="text-secondary">Tidak Sesuai</div>
                    </div>
                    <div class="col-3 col-md-2">
                        <div class="fw-bold fs-5 text-secondary">{{ $audit->na_items }}</div>
                        <div class="text-secondary">N/A</div>
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-center">
                        <div class="progress progress-kepatuhan w-100">
                            <div class="progress-bar {{ $audit->compliance_percentage >= \App\Support\Ppi::thresholds()['baik'] ? 'bg-success' : ($audit->compliance_percentage >= \App\Support\Ppi::thresholds()['cukup'] ? 'bg-warning' : 'bg-danger') }}" style="width:{{ min(100, $audit->compliance_percentage) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info detail --}}
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0"><i class="bi bi-info-circle me-2 text-brand"></i>Informasi Audit</div>
                    <div class="card-body p-0">
                        <table class="table mb-0 small">
                            <tbody>
                                <tr><td class="text-secondary ps-3" style="width:42%">Auditor</td><td class="fw-semibold">{{ $audit->auditor->name }}</td></tr>
                                @if($audit->officer_name)<tr><td class="text-secondary ps-3">Petugas Diaudit</td><td class="fw-semibold">{{ $audit->officer_name }}</td></tr>@endif
                                @if($audit->profession)<tr><td class="text-secondary ps-3">Profesi</td><td class="fw-semibold">{{ $audit->profession->name }}</td></tr>@endif
                                @if($audit->action_type)<tr><td class="text-secondary ps-3">Jenis Tindakan</td><td class="fw-semibold">{{ $audit->action_type }}</td></tr>@endif
                                @if($audit->apdType)<tr><td class="text-secondary ps-3">Jenis APD</td><td class="fw-semibold">{{ $audit->apdType->name }}</td></tr>@endif
                                @if($audit->wasteType)<tr><td class="text-secondary ps-3">Jenis Limbah</td><td class="fw-semibold">{{ $audit->wasteType->name }}</td></tr>@endif
                                @if($audit->notes)<tr><td class="text-secondary ps-3">Catatan</td><td>{{ $audit->notes }}</td></tr>@endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                        <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Temuan ({{ $audit->findings->count() }})
                    </div>
                    <div class="card-body">
                        @forelse($audit->findings as $finding)
                            <a href="{{ route('findings.show', $finding) }}" class="d-block text-decoration-none border rounded-3 p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold small text-dark">{{ $finding->finding_number }}</span>
                                    <span class="badge badge-rounded bg-{{ $finding->severityColor() }}">{{ strtoupper($finding->severity) }}</span>
                                </div>
                                <div class="small text-secondary">{{ Str::limit($finding->description, 70) }}</div>
                                <div class="mt-1"><span class="badge badge-rounded bg-{{ $finding->statusColor() }}">{{ strtoupper($finding->status) }}</span></div>
                            </a>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-emoji-smile fs-2 text-success d-block mb-2"></i>
                                <span class="text-secondary small">Tidak ada temuan. Semua item sesuai! 🎉</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Checklist detail --}}
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-list-check me-2 text-brand"></i>Detail Checklist
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3" style="width:46px">No</th>
                                <th>Parameter</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audit->answers->sortBy('question.order') as $answer)
                                <tr>
                                    <td class="ps-3 text-secondary">{{ $answer->question->order ?? '-' }}</td>
                                    <td>{{ $answer->question->question }}</td>
                                    <td class="text-center">
                                        @if($answer->answer === 'ya')
                                            <span class="badge badge-rounded bg-success-subtle text-success"><i class="bi bi-check-lg"></i> Sesuai</span>
                                        @elseif($answer->answer === 'tidak')
                                            <span class="badge badge-rounded bg-danger-subtle text-danger"><i class="bi bi-x-lg"></i> Tidak Sesuai</span>
                                        @else
                                            <span class="badge badge-rounded bg-secondary-subtle text-secondary">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @can('manage-masters')
        <div class="d-flex justify-content-end mb-4 no-print">
            <form method="POST" action="{{ route('audits.destroy', $audit) }}" onsubmit="return confirm('Hapus audit {{ $audit->audit_number }}? Tindakan ini tidak dapat dibatalkan.')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash me-1"></i>Hapus Audit</button>
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection
