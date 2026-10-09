@extends('layouts.app')

@section('title', 'Audit Cuci Tangan')
@section('page_title', 'Lembar Audit Cuci Tangan')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-11">

        {{-- Pilih kategori (Cuci Tangan / APD / Penanganan Limbah Benda Tajam) --}}
        <div class="card mb-3 no-print">
            <div class="card-body">
                <div class="row g-2">
                    @foreach($categories as $cat)
                        <div class="col-12 col-md-4">
                            <a href="{{ route('audits.create', ['category' => $cat->code]) }}"
                               class="d-flex align-items-center gap-3 text-decoration-none p-3 rounded-4 border {{ $cat->id === $category->id ? 'border-2 bg-brand-light' : '' }}"
                               style="{{ $cat->id === $category->id ? 'border-color:var(--ppi-teal) !important' : '' }}">
                                <div class="stat-icon bg-brand-light text-brand"><i class="bi {{ $cat->icon }} fs-4"></i></div>
                                <div>
                                    <div class="fw-bold {{ $cat->id === $category->id ? 'text-brand' : 'text-dark' }}">{{ $cat->name }}</div>
                                    <div class="small text-secondary">
                                        {{ $cat->code === 'cuci-tangan' ? 'Form observasi (maks. 24 peluang)' : $cat->activeQuestions()->count() . ' item pemeriksaan' }}
                                    </div>
                                </div>
                                @if($cat->id === $category->id)
                                    <i class="bi bi-check-circle-fill text-brand ms-auto"></i>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('audits.store') }}" id="handHygieneForm" x-data="handHygieneForm()">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">

            {{-- ====== Identitas Lembar (Nama Observer / Ruang / Bulan) ====== --}}
            <div class="card mb-3">
                <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                    <i class="bi bi-1-circle me-2 text-brand"></i>Identitas Lembar Audit
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Nama Observer / Auditor <span class="text-danger">*</span></label>
                            @if(in_array(auth()->user()->role, [\App\Models\User::ROLE_AUDITOR, \App\Models\User::ROLE_UNIT]))
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                                <input type="hidden" name="auditor_id" value="{{ auth()->user()->id }}">
                            @else
                                <select name="auditor_id" class="form-select" required>
                                    <option value="">-- Pilih Observer --</option>
                                    @foreach($auditors as $aud)
                                        <option value="{{ $aud->id }}" {{ (string)old('auditor_id') === (string)$aud->id ? 'selected' : '' }}>{{ $aud->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small fw-semibold">Ruang / Unit <span class="text-danger">*</span></label>
                            @if(auth()->user()->role === \App\Models\User::ROLE_UNIT)
                                <input type="text" class="form-control" value="{{ auth()->user()->unit?->name ?? '-' }}" disabled>
                                <input type="hidden" name="unit_id" value="{{ auth()->user()->unit_id }}">
                            @else
                                <select name="unit_id" class="form-select" required>
                                    <option value="">-- Pilih Ruang --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ (string)old('unit_id') === (string)$unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small fw-semibold">Bulan / Tanggal Observasi <span class="text-danger">*</span></label>
                            <input type="date" name="audit_date" class="form-control" value="{{ old('audit_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small fw-semibold">Shift <span class="text-danger">*</span></label>
                            <select name="shift" class="form-select" required>
                                @foreach(\App\Models\Audit::SHIFTS as $key => $label)
                                    <option value="{{ $key }}" {{ old('shift') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small fw-semibold">Nama Petugas yang Diobservasi</label>
                            <input type="text" name="officer_name" class="form-control" placeholder="Opsional" value="{{ old('officer_name') }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Profesi</label>
                            <select name="profession_id" class="form-select">
                                <option value="">-- Pilih Profesi --</option>
                                @foreach($professions as $prof)
                                    <option value="{{ $prof->id }}" {{ (string)old('profession_id') === (string)$prof->id ? 'selected' : '' }}>{{ $prof->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ====== 24 Observasi ====== --}}
            <div class="card mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-radius:1rem 1rem 0 0">
                    <span class="fw-semibold"><i class="bi bi-2-circle me-2 text-brand"></i>Observasi Peluang Cuci Tangan (1–24)</span>
                    <span class="badge badge-rounded bg-brand-light text-brand">
                        <span x-text="filled">0</span>/24 observasi terisi
                    </span>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border small py-2 mb-3" style="border-radius:.8rem">
                        <b>Cara pengisian:</b> Pada tiap peluang, centang <b>Momen</b> yang terjadi (5 Momen Kebersihan Tangan WHO)
                        lalu centang <b>Tindakan</b> yang dilakukan petugas:
                        <span class="text-success fw-semibold">HR</span> (Hand Rub) ·
                        <span class="text-success fw-semibold">HW</span> (Hand Wash) ·
                        <span class="text-success fw-semibold">Set. lepas sarung tangan</span> = <b>PATUH</b>, sedangkan
                        <span class="text-danger fw-semibold">Tidak</span> (tidak melakukan) = <b>TIDAK PATUH</b>.
                        Baris yang tidak terpakai dibiarkan kosong.
                    </div>

                    @error('observations')
                        <div class="alert alert-danger small py-2">{{ $message }}</div>
                    @enderror

                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0 obs-table">
                            <thead>
                                <tr class="small text-center text-secondary">
                                    <th rowspan="2" style="width:44px">No</th>
                                    <th colspan="5" class="border-bottom pb-1">MOMEN (pilih salah satu)</th>
                                    <th colspan="4" class="border-bottom pb-1">TINDAKAN (pilih salah satu)</th>
                                </tr>
                                <tr class="xsmall text-center text-secondary">
                                    @foreach(\App\Models\HandHygieneObservation::MOMENT_SHORT as $mKey => $mLabel)
                                        <th class="fw-normal">{{ $mLabel }}</th>
                                    @endforeach
                                    <th class="fw-normal">HR<br><span class="text-secondary" style="font-weight:400">Hand Rub</span></th>
                                    <th class="fw-normal">HW<br><span class="text-secondary" style="font-weight:400">Hand Wash</span></th>
                                    <th class="fw-normal">Tidak<br><span class="text-secondary" style="font-weight:400">melakukan</span></th>
                                    <th class="fw-normal">Set. lepas<br>sarung tangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(range(1, 24) as $i)
                                    @php($idx = $i - 1)
                                    <tr :class="rows[{{ $idx }}].moment || rows[{{ $idx }}].action ? 'table-light' : ''">
                                        <td class="text-center text-secondary small fw-semibold">{{ $i }}</td>
                                        @foreach(\App\Models\HandHygieneObservation::MOMENT_SHORT as $mKey => $mLabel)
                                            <td class="text-center">
                                                <input type="radio" class="btn-check" id="obs{{ $i }}-m-{{ $mKey }}"
                                                       name="observations[{{ $i }}][moment]" value="{{ $mKey }}"
                                                       @change="rows[{{ $idx }}].moment = '{{ $mKey }}'"
                                                       {{ old('observations.' . $i . '.moment') === $mKey ? 'checked' : '' }}>
                                                <label class="btn btn-sm btn-outline-secondary obs-btn" for="obs{{ $i }}-m-{{ $mKey }}"
                                                       title="{{ \App\Models\HandHygieneObservation::MOMENTS[$mKey] }}">
                                                    <i class="bi bi-check-lg"></i>
                                                </label>
                                            </td>
                                        @endforeach
                                        @foreach(\App\Models\HandHygieneObservation::ACTION_SHORT as $aKey => $aLabel)
                                            @php($aBtn = $aKey === 'tidak' ? 'danger' : 'success')
                                            <td class="text-center">
                                                <input type="radio" class="btn-check" id="obs{{ $i }}-a-{{ $aKey }}"
                                                       name="observations[{{ $i }}][action]" value="{{ $aKey }}"
                                                       @change="rows[{{ $idx }}].action = '{{ $aKey }}'"
                                                       {{ old('observations.' . $i . '.action') === $aKey ? 'checked' : '' }}>
                                                <label class="btn btn-sm btn-outline-{{ $aBtn }} obs-btn" for="obs{{ $i }}-a-{{ $aKey }}"
                                                       title="{{ \App\Models\HandHygieneObservation::ACTIONS[$aKey] }}">
                                                    <i class="bi {{ $aKey === 'tidak' ? 'bi-x-lg' : 'bi-check-lg' }}"></i>
                                                </label>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <label class="form-label small fw-semibold">Catatan Audit</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Opsional">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ====== Live skor kepatuhan ====== --}}
            <div class="card mb-3" x-cloak>
                <div class="card-body d-flex flex-wrap align-items-center gap-4">
                    <div>
                        <div class="text-secondary small">Kepatuhan cuci tangan sementara (dihitung otomatis)</div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="display-6 fw-bold" :class="scoreColor()" x-text="score() + '%'">0%</span>
                            <span class="badge badge-rounded bg-secondary" x-text="grade()">-</span>
                        </div>
                    </div>
                    <div class="flex-grow-1" style="min-width:220px">
                        <div class="progress progress-kepatuhan">
                            <div class="progress-bar" :class="scoreBg()" :style="'width:' + Math.min(100, score()) + '%'"></div>
                        </div>
                        <div class="small text-secondary mt-1">
                            <i class="bi bi-check-circle text-success"></i> Patuh (HR/HW/Set. lepas ST): <b x-text="compliant">0</b> &nbsp;
                            <i class="bi bi-x-circle text-danger"></i> Tidak: <b x-text="nonCompliant">0</b> &nbsp;
                            <i class="bi bi-clipboard-check text-secondary"></i> Terisi: <b x-text="filled">0</b>/24
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mb-4 no-print">
                <a href="{{ route('audits.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-brand rounded-pill px-4" :disabled="filled === 0">
                    <i class="bi bi-send me-1"></i> Simpan &amp; Hitung Kepatuhan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function handHygieneForm() {
        return {
            rows: Array.from({ length: 24 }, () => ({ moment: null, action: null })),

            init() {
                // Sinkronkan dari radio ter-check (mis. setelah validasi error / old input)
                document.querySelectorAll('#handHygieneForm input[type=radio]:checked').forEach(r => {
                    const m = r.name.match(/observations\[(\d+)\]\[(moment|action)\]/);
                    if (m) this.rows[+m[1] - 1][m[2]] = r.value;
                });
            },

            get filled() {
                return this.rows.filter(r => r.moment || r.action).length;
            },

            get compliant() {
                return this.rows.filter(r => r.action && r.action !== 'tidak').length;
            },

            get nonCompliant() {
                return this.rows.filter(r => r.action === 'tidak').length;
            },

            score() {
                const assessed = this.compliant + this.nonCompliant;
                if (assessed === 0) return 0;
                return Math.round((this.compliant / assessed) * 1000) / 10;
            },

            grade() {
                const s = this.score();
                const t = @json(\App\Support\Ppi::thresholds());
                if (!this.compliant && !this.nonCompliant) return '-';
                if (s >= t.sangat_baik) return 'Sangat Baik';
                if (s >= t.baik) return 'Baik';
                if (s >= t.cukup) return 'Cukup';
                return 'Perlu Perbaikan';
            },

            scoreColor() {
                const t = @json(\App\Support\Ppi::thresholds());
                const s = this.score();
                if (s >= t.baik) return 'text-success';
                if (s >= t.cukup) return 'text-warning';
                return 'text-danger';
            },

            scoreBg() {
                const t = @json(\App\Support\Ppi::thresholds());
                const s = this.score();
                if (s >= t.baik) return 'bg-success';
                if (s >= t.cukup) return 'bg-warning';
                return 'bg-danger';
            },
        };
    }
</script>

<style>
    .obs-table .obs-btn { --bs-btn-padding-y: .1rem; --bs-btn-padding-x: .45rem; border-radius: 2rem !important; }
    .obs-table td { padding: .25rem .35rem; }
    .obs-table thead th { border-bottom: 0; }
</style>
@endpush
