@extends('layouts.app')

@section('title', 'Monitoring Limbah Benda Tajam')
@section('page_title', 'Lembar Monitoring Penanganan Limbah Benda Tajam')

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

    .monitoring-subtitle {
        font-size: 14px;
        margin-bottom: 20px;
    }

    .monitoring-info {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 12px;
    }

    .monitoring-info label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .monitoring-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 13px;
    }

    .monitoring-table th,
    .monitoring-table td {
        border: 1px solid #333;
        padding: 8px;
        vertical-align: middle;
    }

    .monitoring-table th {
        text-align: center;
        font-weight: bold;
        background: #f0f0f0;
    }

    .monitoring-table .col-no { width: 5%; text-align: center; }
    .monitoring-table .col-statement { width: 60%; }
    .monitoring-table .col-yes,
    .monitoring-table .col-no-answer { width: 7%; text-align: center; }
    .monitoring-table .col-description { width: 21%; }

    .monitoring-table td.statement {
        text-align: justify;
        line-height: 1.5;
    }

    .monitoring-table td.answer { text-align: center; }

    .monitoring-table input[type="radio"] {
        width: 17px;
        height: 17px;
        cursor: pointer;
    }

    .monitoring-table input[type="text"] {
        width: 100%;
        min-width: 0;
        border: none;
        outline: none;
        background: transparent;
    }

    .monitoring-footer {
        margin-top: 12px;
        font-size: 13px;
    }

    .monitoring-result {
        margin-top: 12px;
        padding: 12px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .monitoring-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .monitoring-container { padding: 12px; }
        .monitoring-title { font-size: 16px; }
        .monitoring-info { grid-template-columns: 1fr; gap: 10px; }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

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
        .monitoring-info input, .monitoring-info select { color: #000; }
        .monitoring-table tr { break-inside: avoid; }
        .monitoring-table input[type="radio"] {
            appearance: none;
            width: 12px;
            height: 12px;
            border: 1px solid #333;
            border-radius: 50%;
            vertical-align: middle;
        }
        .monitoring-table input[type="radio"]:checked {
            background: #222;
            box-shadow: inset 0 0 0 3px #fff;
        }
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xxl-10">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 no-print">
            <a href="{{ route('monitoring-limbah-tajam.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Riwayat Monitoring
            </a>
        </div>

        <div class="monitoring-container">
            <h4 class="monitoring-title">
                Lembar Monitoring Penanganan Limbah Benda Tajam
            </h4>

            <p class="monitoring-subtitle">
                Beri Tanda (&#10003;) pada kolom YA dan TIDAK
            </p>

            <form action="{{ route('monitoring-limbah-tajam.store') }}" method="POST">
                @csrf

                {{-- INFORMASI MONITORING --}}
                <div class="monitoring-info">
                    <div>
                        <label for="monitoring_date">Tanggal:</label>
                        <input type="date" class="form-control form-control-sm"
                               id="monitoring_date" name="monitoring_date"
                               value="{{ old('monitoring_date', date('Y-m-d')) }}" required>
                    </div>

                    <div>
                        <label for="unit_id">Ruangan:</label>
                        <select id="unit_id" name="unit_id" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ (string) old('unit_id') === (string) $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="officer_name">Petugas:</label>
                        <input type="text" class="form-control form-control-sm"
                               id="officer_name" name="officer_name"
                               value="{{ old('officer_name') }}" placeholder="Nama petugas" required>
                        @error('officer_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                @error('items')
                    <div class="alert alert-danger small py-2">{{ $message }}</div>
                @enderror

                {{-- TABEL MONITORING --}}
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
                            @foreach($items as $index => $item)
                                <tr>
                                    <td class="col-no">{{ $index + 1 }}</td>

                                    <td class="statement">
                                        {{ $item }}
                                        <input type="hidden" name="items[{{ $index }}][pernyataan]" value="{{ $item }}">
                                    </td>

                                    <td class="answer">
                                        <input type="radio"
                                               name="items[{{ $index }}][jawaban]"
                                               value="Ya"
                                               {{ old("items.$index.jawaban") === 'Ya' ? 'checked' : '' }}
                                               aria-label="Ya untuk {{ $index + 1 }}" required>
                                    </td>

                                    <td class="answer">
                                        <input type="radio"
                                               name="items[{{ $index }}][jawaban]"
                                               value="Tidak"
                                               {{ old("items.$index.jawaban") === 'Tidak' ? 'checked' : '' }}
                                               aria-label="Tidak untuk {{ $index + 1 }}" required>
                                    </td>

                                    <td>
                                        <input type="text"
                                               name="items[{{ $index }}][keterangan]"
                                               value="{{ old("items.$index.keterangan") }}"
                                               aria-label="Keterangan item {{ $index + 1 }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- KETERANGAN DAN PERHITUNGAN --}}
                <div class="monitoring-footer">
                    <strong>Keterangan:</strong>
                    <div>Ya = Memenuhi pernyataan</div>
                    <div>Tidak = Tidak memenuhi pernyataan</div>

                    <div class="monitoring-result">
                        <strong>Perhitungan:</strong>

                        <p>
                            Persentase =
                            <span id="total-ya">0</span> /
                            <span id="total-jawaban">0</span>
                            &times; 100% =
                            <strong id="persentase">0%</strong>
                        </p>

                        <small>Jumlah YA &divide; Jumlah (YA + TIDAK) &times; 100%</small>
                    </div>

                    <div class="mt-3">
                        <label class="form-label small fw-semibold mb-1">Catatan (opsional)</label>
                        <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="Catatan monitoring...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="monitoring-actions no-print">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Cetak
                    </button>

                    <button type="reset" class="btn btn-outline-warning rounded-pill px-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>

                    <button type="submit" class="btn btn-brand rounded-pill px-3">
                        <i class="bi bi-save me-1"></i> Simpan Monitoring
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('.monitoring-container form');

        function hitungPersentase() {
            const totalYa = form.querySelectorAll('input[value="Ya"]:checked').length;
            const totalTidak = form.querySelectorAll('input[value="Tidak"]:checked').length;
            const totalJawaban = totalYa + totalTidak;

            const persentase = totalJawaban > 0 ? (totalYa / totalJawaban) * 100 : 0;

            document.getElementById('total-ya').textContent = totalYa;
            document.getElementById('total-jawaban').textContent = totalJawaban;
            document.getElementById('persentase').textContent = persentase.toFixed(2) + '%';
        }

        form.querySelectorAll('input[type="radio"]').forEach(function (radio) {
            radio.addEventListener('change', hitungPersentase);
        });

        form.addEventListener('reset', function () {
            setTimeout(hitungPersentase, 0);
        });

        hitungPersentase();
    });
</script>
@endpush
