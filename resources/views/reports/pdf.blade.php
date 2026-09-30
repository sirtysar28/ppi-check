<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Audit PPI</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .header { text-align: center; border-bottom: 3px solid #0d7a66; padding-bottom: 10px; margin-bottom: 12px; }
        .header h2 { margin: 0; color: #0d7a66; }
        .header p { margin: 2px 0; font-size: 10px; color: #555; }
        h3.sec { color: #0a5c4d; margin: 14px 0 6px; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #bbb; padding: 5px 7px; text-align: left; vertical-align: top; }
        th { background: #e6f4f1; font-size: 10px; }
        .center { text-align: center; }
        .meta td { border: none; padding: 2px 6px 2px 0; font-size: 10px; }
        .bar-bg { background: #e6efec; height: 8px; width: 100%; }
        .bar-fg { background: #0d7a66; height: 8px; }
        .footer-note { margin-top: 18px; font-size: 9px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ strtoupper($facility['name']) }}</h2>
        <p>{{ $facility['address'] }}</p>
        <h3>LAPORAN REKAPITULASI AUDIT PPI</h3>
        @if(!empty($filters['start']) || !empty($filters['end']))
            <p>Periode: {{ $filters['start'] ?? '...' }} s.d. {{ $filters['end'] ?? '...' }}</p>
        @endif
    </div>

    <table class="meta">
        <tr>
            <td><b>Total Audit</b></td><td>: {{ $audits->count() }}</td>
            <td><b>Rata-rata Kepatuhan</b></td>
            <td>: {{ $audits->count() ? round($audits->avg('compliance_percentage'), 1) : 0 }}%</td>
        </tr>
        <tr>
            <td><b>Unit Teraudit</b></td><td>: {{ $perUnit->count() }}</td>
            <td><b>Total Temuan</b></td>
            <td>: {{ $audits->sum(fn ($a) => $a->findings->count()) }}</td>
        </tr>
        <tr>
            <td><b>Dicetak</b></td><td>: {{ now()->translatedFormat('d F Y H:i') }}</td>
            <td></td><td></td>
        </tr>
    </table>

    <h3 class="sec">A. Rekap Kepatuhan per Unit</h3>
    <table>
        <thead>
            <tr>
                <th style="width:40px" class="center">No</th>
                <th>Unit</th>
                <th class="center" style="width:70px">Jumlah</th>
                <th class="center" style="width:90px">Rata-rata</th>
                <th style="width:180px">Grafik</th>
            </tr>
        </thead>
        <tbody>
            @forelse($perUnit as $i => $row)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $row['unit']->name }}</td>
                    <td class="center">{{ $row['count'] }}</td>
                    <td class="center"><b>{{ $row['avg'] }}%</b></td>
                    <td><div class="bar-bg"><div class="bar-fg" style="width: {{ $row['avg'] }}%"></div></div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="sec">B. Rekap per Instrumen Audit</h3>
    <table>
        <thead>
            <tr>
                <th style="width:40px" class="center">No</th>
                <th>Instrumen</th>
                <th class="center" style="width:70px">Jumlah</th>
                <th class="center" style="width:90px">Rata-rata</th>
                <th style="width:180px">Grafik</th>
            </tr>
        </thead>
        <tbody>
            @forelse($perCategory as $i => $row)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $row['category']->name }}</td>
                    <td class="center">{{ $row['count'] }}</td>
                    <td class="center"><b>{{ $row['avg'] }}%</b></td>
                    <td><div class="bar-bg"><div class="bar-fg" style="width: {{ $row['avg'] }}%"></div></div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="sec">C. Detail Audit</h3>
    <table>
        <thead>
            <tr>
                <th style="width:40px" class="center">No</th>
                <th>No. Audit</th>
                <th>Tanggal</th>
                <th>Instrumen</th>
                <th>Unit</th>
                <th>Auditor</th>
                <th class="center" style="width:55px">Skor</th>
                <th class="center" style="width:80px">Predikat</th>
                <th class="center" style="width:55px">Temuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audits as $i => $audit)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $audit->audit_number }}</td>
                    <td>{{ $audit->audit_date->translatedFormat('d/m/Y') }}</td>
                    <td>{{ str_replace('Audit ', '', $audit->category->name) }}</td>
                    <td>{{ $audit->unit->name }}</td>
                    <td>{{ $audit->auditor->name }}</td>
                    <td class="center"><b>{{ $audit->compliance_percentage }}%</b></td>
                    <td class="center">{{ $audit->grade }}</td>
                    <td class="center">{{ $audit->findings->count() }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="center">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">Dicetak dari aplikasi {{ config('app.name') }} pada {{ now()->translatedFormat('d F Y H:i') }}.</div>
</body>
</html>
