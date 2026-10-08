<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $audit->audit_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { text-align: center; border-bottom: 3px solid #0d7a66; padding-bottom: 10px; margin-bottom: 14px; }
        .header h2 { margin: 0; color: #0d7a66; }
        .header p { margin: 2px 0; font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #bbb; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #e6f4f1; }
        .center { text-align: center; }
        .badge-ok { color: #0a5c4d; font-weight: bold; }
        .badge-no { color: #c0392b; font-weight: bold; }
        .score-box { border: 2px solid #0d7a66; border-radius: 8px; padding: 10px 16px; display: inline-block; text-align: center; }
        .score-box .value { font-size: 26px; font-weight: bold; color: #0d7a66; }
        .meta td { border: none; padding: 2px 6px 2px 0; }
        h4 { color: #0a5c4d; margin: 14px 0 6px; font-size: 13px; text-transform: uppercase; letter-spacing: .5px; }
        .footer { margin-top: 24px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ strtoupper($facility['name']) }}</h2>
        <p>{{ $facility['address'] }}</p>
        <h3>LAPORAN AUDIT PPI — {{ strtoupper($audit->category->name) }}</h3>
        <p>No. {{ $audit->audit_number }}</p>
    </div>

    <table class="meta">
        <tr>
            <td><b>Unit</b></td><td>: {{ $audit->unit->name }}</td>
            <td><b>Tanggal Audit</b></td><td>: {{ $audit->audit_date->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><b>Auditor</b></td><td>: {{ $audit->auditor->name }}</td>
            <td><b>Shift</b></td><td>: {{ $audit->shift_label }}</td>
        </tr>
        @if($audit->officer_name)
        <tr>
            <td><b>Petugas Diaudit</b></td><td>: {{ $audit->officer_name }}</td>
            @if($audit->profession)<td><b>Profesi</b></td><td>: {{ $audit->profession->name }}</td>@endif
        </tr>
        @endif
        @if($audit->action_type)
        <tr><td><b>Jenis Tindakan</b></td><td>: {{ $audit->action_type }}</td>
            @if($audit->apdType)<td><b>Jenis APD</b></td><td>: {{ $audit->apdType->name }}</td>@endif
        </tr>
        @endif
    </table>

@php($isHandHygiene = $audit->category->code === 'cuci-tangan' && $audit->observations->isNotEmpty())
@php($HH = \App\Models\HandHygieneObservation::class)
    <div class="score-box">
        <div class="value">{{ $audit->compliance_percentage }}%</div>
        <div>Nilai Kepatuhan — {{ $audit->grade }}</div>
    </div>

    <table style="margin-top:14px">
        <tr>
            <th class="center">Total Observasi</th>
            <th class="center">{{ $isHandHygiene ? 'Patuh' : 'Sesuai' }}</th>
            <th class="center">{{ $isHandHygiene ? 'Tidak Patuh' : 'Tidak Sesuai' }}</th>
            <th class="center">N/A</th>
            <th class="center">Temuan</th>
        </tr>
        <tr class="center">
            <td>{{ $audit->total_items }}</td>
            <td>{{ $audit->conform_items }}</td>
            <td>{{ $audit->nonconform_items }}</td>
            <td>{{ $audit->na_items }}</td>
            <td>{{ $audit->findings->count() }}</td>
        </tr>
    </table>

    <h4>{{ $isHandHygiene ? 'Detail Observasi Cuci Tangan' : 'Checklist Penilaian' }}</h4>
    @if($isHandHygiene)
    <table>
        <tr>
            <th class="center" style="width:34px">No</th>
            <th>Momen (5 Momen WHO)</th>
            <th style="width:26%">Tindakan</th>
            <th class="center" style="width:80px">Status</th>
        </tr>
        @foreach($audit->observations as $obs)
        <tr>
            <td class="center">{{ $obs->sequence }}</td>
            <td>{{ $obs->moment_label }}</td>
            <td>{{ $obs->action_label }}</td>
            <td class="center">
                @if($obs->is_compliant)<span class="badge-ok">✓ Patuh</span>
                @else<span class="badge-no">✗ Tidak</span>@endif
            </td>
        </tr>
        @endforeach
    </table>
    <table style="margin-top:8px">
        <tr>
            @foreach($HH::ACTIONS as $aKey => $aLabel)
            <th class="center">{{ $aLabel }}</th>
            @endforeach
            <th class="center">Kepatuhan</th>
        </tr>
        <tr class="center">
            @foreach($HH::ACTIONS as $aKey => $aLabel)
            <td>{{ $audit->observations->where('action', $aKey)->count() }}</td>
            @endforeach
            <td><b>{{ $audit->compliance_percentage }}%</b></td>
        </tr>
    </table>
    @else
    <table>
        <tr>
            <th style="width:34px" class="center">No</th>
            <th>Parameter</th>
            <th style="width:90px" class="center">Status</th>
        </tr>
        @foreach($audit->answers->sortBy('question.order') as $answer)
        <tr>
            <td class="center">{{ $answer->question->order ?? '-' }}</td>
            <td>{{ $answer->question->question }}</td>
            <td class="center">
                @if($answer->answer === 'ya')<span class="badge-ok">✓ Sesuai</span>
                @elseif($answer->answer === 'tidak')<span class="badge-no">✗ Tidak Sesuai</span>
                @else N/A @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    <h4>Temuan</h4>
    @forelse($audit->findings as $finding)
    <table>
        <tr>
            <th style="width:130px">{{ $finding->finding_number }}</th>
            <td>{{ $finding->description }}</td>
        </tr>
        <tr><th>Lokasi</th><td>{{ $finding->location ?: '-' }}</td></tr>
        <tr><th>Tingkat</th><td>{{ ucfirst($finding->severity) }}</td></tr>
        <tr><th>Rekomendasi</th><td>{{ $finding->recommendation ?: '-' }}</td></tr>
        <tr><th>Status</th><td>{{ strtoupper($finding->status) }} — Batas tindak lanjut: {{ \Illuminate\Support\Carbon::parse($finding->due_date)->translatedFormat('d M Y') }}</td></tr>
    </table>
    @empty
    <p><i>Tidak ada temuan.</i></p>
    @endforelse

    @if($audit->notes)
    <h4>Catatan Auditor</h4>
    <p>{{ $audit->notes }}</p>
    @endif

    <div class="footer">
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
        <p>Auditor,<br><br><br><br><b><u>{{ $audit->auditor->name }}</u></b></p>
    </div>
</body>
</html>
