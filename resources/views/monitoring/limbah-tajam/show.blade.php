@extends('layouts.app')

@section('title', 'Detail Monitoring ' . $monitoring->monitoring_number)
@section('page_title', 'Monitoring Limbah Benda Tajam')

@push('styles')
<style>
    .monitoring-container {
        background: #fff;
        padding: 24px;
        border-radius: 8px;
        color: #222;
    }

    .monitoring-title {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .monitoring-info {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 18px;
    }

    .monitoring-info .label { font-weight: 600; font-size: .8rem; color: #6c8880; }
    .monitoring-info .value { font-weight: 600; border-bottom: 1px solid #ccc; padding: 4px 2px; }

    .monitoring-table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 13px; }
    .monitoring-table th, .monitoring-table td { border: 1px solid #333; padding: 8px; vertical-align: middle; }
    .monitoring-table th { text-align: center; font-weight: bold; background: #f0f0f0; }
    .monitoring-table .col-no { width: 5%; text-align: center; }
    .monitoring-table .col-statement { width: 60%; }
    .monitoring-table .col-yes, .monitoring-table .col-no-answer { width: 7%; text-align: center; }
    .monitoring-table .col-description { width: 21%; }
    .monitoring-table td.statement { text-align: justify; line-height: 1.5; }
    .monitoring-table td.answer { text-align: center; }

    .monitoring-result {
        margin-top: 12px;
        padding: 12px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 13px;
    }

    .monitoring-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; }

    @media (max-width: 768px) {
        .monitoring-container { padding: 12px; }
        .monitoring-title { font-size: 16px; }
        .monitoring-info { grid-template-columns: 1fr; gap: 10px; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .monitoring-table { min-width: 750px; }
    }

    @media print {
        @page { size: A4 portrait; margin: 12mm; }
        body { background: #fff !important; }
        .monitoring-container { padding: 0; border: none; box-shadow: none; }
        .monitoring-actions, .no-print { display: none !important; }
        .monitoring-title { font-size: 15px; }
        .monitoring-table { font-size: 10px; }
        .monitoring-table th, .monitoring-table td { padding: 5px; }
        .monitoring-table tr { break-inside: avoid; }
        .mark-answer { font-size: 12px; }
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xxl-10">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 no-print">
            <a href="{{ route('monitoring-limbah-tajam.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Riwayat
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('monitoring-limbah-tajam.pdf', $monitoring) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
                </a>
                <button type="button" class="btn btn-brand btn-sm rounded-pill px-3" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak
                </button>
            </div>
        </div>

        <div class="monitoring-container">
            <h4 class="monitoring-title">Lembar Monitoring Penanganan Limbah Benda Tajam</h4>

            <div class="monitoring-info">
                <div>
                    <div class="label">Tanggal</div>
                    <div class="value">{{ $monitoring->monitoring_date->translatedFormat('d F Y') }}</div>
                </div>
                <div>
                    <div class="label">Ruangan</div>
                    <div class="value">{{ $monitoring->unit?->name }}</div>
                </div>
                <div>
                    <div class="label">Petugas</div>
                    <div class="value">{{ $monitoring->officer_name }}</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="monitoring-table">
                    <thead>
                        <tr>
                            <th class="col-no">NO</th>
                            <th class="col-statement">PERNYATAAN</th>
                            <th class="col-yes">YA</th>
                            <th class="col-no-answer">TIDAK</th>
                            <th class="col-description">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monitoring->items as $item)
                            <tr>
                                <td class="col-no">{{ $loop->iteration }}</td>
                                <td class="statement">{{ $item->statement }}</td>
                                <td class="answer mark-answer">{!! $item->answer === 'ya' ? '&#10003;' : '' !!}</td>
                                <td class="answer mark-answer">{!! $item->answer === 'tidak' ? '&#10003;' : '' !!}</td>
                                <td>{{ $item->notes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="monitoring-result">
                <strong>Perhitungan:</strong>
                <p class="mb-1">
                    Persentase = {{ $monitoring->conform_items }} / {{ $monitoring->total_items }} &times; 100% =
                    <strong>{{ $monitoring->compliance_percentage }}%</strong>
                    <span class="badge badge-rounded bg-{{ $monitoring->gradeColor() }} ms-2">{{ $monitoring->grade }}</span>
                </p>
                <small>Jumlah YA &divide; Jumlah (YA + TIDAK) &times; 100%</small>

                @if($monitoring->notes)
                    <div class="mt-2"><strong>Catatan:</strong> {{ $monitoring->notes }}</div>
                @endif

                <div class="mt-2 text-secondary" style="font-size:.75rem">
                    No. {{ $monitoring->monitoring_number }} &middot;
                    Diinput oleh {{ $monitoring->user?->name ?? '-' }} &middot;
                    {{ $monitoring->created_at->translatedFormat('d M Y H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
