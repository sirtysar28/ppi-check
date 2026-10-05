<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $monitoring->monitoring_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { text-align: center; border-bottom: 3px solid #0d7a66; padding-bottom: 10px; margin-bottom: 14px; }
        .header h2 { margin: 0; color: #0d7a66; }
        .header p { margin: 2px 0; font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #bbb; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #e6f4f1; }
        .center { text-align: center; }
        .meta td { border: none; padding: 2px 6px 2px 0; }
        .score-box { border: 2px solid #0d7a66; border-radius: 8px; padding: 10px 16px; display: inline-block; text-align: center; }
        .score-box .value { font-size: 24px; font-weight: bold; color: #0d7a66; }
        .footer { margin-top: 24px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ strtoupper($facility['name']) }}</h2>
        <p>{{ $facility['address'] }}</p>
        <h3>LEMBAR MONITORING PENANGANAN LIMBAH BENDA TAJAM</h3>
        <p>No. {{ $monitoring->monitoring_number }}</p>
    </div>

    <table class="meta">
        <tr>
            <td><b>Tanggal</b></td><td>: {{ $monitoring->monitoring_date->translatedFormat('d F Y') }}</td>
            <td><b>Ruangan</b></td><td>: {{ $monitoring->unit?->name }}</td>
        </tr>
        <tr>
            <td><b>Petugas</b></td><td>: {{ $monitoring->officer_name }}</td>
            <td><b>Diinput oleh</b></td><td>: {{ $monitoring->user?->name ?? '-' }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th style="width:5%" class="center">NO</th>
            <th style="width:60%">PERNYATAAN</th>
            <th style="width:7%" class="center">YA</th>
            <th style="width:7%" class="center">TIDAK</th>
            <th style="width:21%">KETERANGAN</th>
        </tr>
        @foreach($monitoring->items as $item)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $item->statement }}</td>
                <td class="center">{{ $item->answer === 'ya' ? 'V' : '' }}</td>
                <td class="center">{{ $item->answer === 'tidak' ? 'V' : '' }}</td>
                <td>{{ $item->notes }}</td>
            </tr>
        @endforeach
    </table>

    <div class="score-box">
        <div class="value">{{ $monitoring->compliance_percentage }}%</div>
        <div>{{ $monitoring->conform_items }} / {{ $monitoring->total_items }} &mdash; {{ $monitoring->grade }}</div>
    </div>

    @if($monitoring->notes)
        <p style="margin-top:12px"><b>Catatan:</b> {{ $monitoring->notes }}</p>
    @endif

    <div class="footer">
        Dicetak dari {{ config('app.name') }} pada {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>
