@extends('layouts.app')

@section('title', 'Audit Baru')
@section('page_title', 'Buat Audit ' . $category->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

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

        <form method="POST" action="{{ route('audits.store') }}" enctype="multipart/form-data"
              x-data="auditForm()" id="auditForm">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">

            {{-- ====== Data Awal ====== --}}
            <div class="card mb-3">
                <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                    <i class="bi bi-1-circle me-2 text-brand"></i>Data Awal Audit — {{ $category->name }}
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Tanggal Audit <span class="text-danger">*</span></label>
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
                            <label class="form-label small fw-semibold">Auditor <span class="text-danger">*</span></label>
                            @if(auth()->user()->role === \App\Models\User::ROLE_AUDITOR)
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                                <input type="hidden" name="auditor_id" value="{{ auth()->user()->id }}">
                            @else
                                <select name="auditor_id" class="form-select" required>
                                    <option value="">-- Pilih Auditor --</option>
                                    @foreach($auditors as $aud)
                                        <option value="{{ $aud->id }}" {{ (string)old('auditor_id') === (string)$aud->id ? 'selected' : '' }}>{{ $aud->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Unit / Ruangan <span class="text-danger">*</span></label>
                            <select name="unit_id" class="form-select" required x-model="unitId">
                                <option value="">-- Pilih Unit --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ (string)old('unit_id') === (string)$unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold">Nama Petugas yang Diaudit</label>
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

                        @if($category->code === 'apd')
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Tindakan yang Diobservasi <span class="text-danger">*</span></label>
                                <select name="action_type" class="form-select" required>
                                    <option value="">-- Pilih Tindakan --</option>
                                    @foreach($apdActions as $action)
                                        <option value="{{ $action->name }}" {{ old('action_type') === $action->name ? 'selected' : '' }}>{{ $action->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Pilih tindakan yang sedang diobservasi, lalu nilai penggunaan setiap jenis APD pada checklist di bawah.</div>
                            </div>
                        @endif

                        @if($category->code === 'sampah')
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Jenis Limbah yang Diaudit</label>
                                <select name="waste_type_id" class="form-select">
                                    <option value="">-- Pilih Jenis Limbah --</option>
                                    @foreach($wasteTypes as $waste)
                                        <option value="{{ $waste->id }}" {{ (string)old('waste_type_id') === (string)$waste->id ? 'selected' : '' }}>{{ $waste->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ====== Checklist ====== --}}
            <div class="card mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-radius:1rem 1rem 0 0">
                    <span class="fw-semibold"><i class="bi bi-2-circle me-2 text-brand"></i>Checklist Penilaian</span>
                    <span class="badge badge-rounded bg-brand-light text-brand">
                        <span x-text="answered">0</span>/{{ $category->activeQuestions->count() }} terjawab
                    </span>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border small py-2" style="border-radius:.8rem">
                        <b>Status:</b>
                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Ya = Sesuai</span> ·
                        <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Tidak = Tidak Sesuai (membuka form temuan)</span> ·
                        <span class="text-secondary"><i class="bi bi-dash-circle-fill"></i> N/A = Tidak Dinilai</span>
                    </div>

                    @if($category->code === 'apd')
                        <div class="alert alert-light border small py-2 mb-3" style="border-radius:.8rem">
                            <b>Catatan:</b> Setiap tindakan dinilai terhadap penggunaan jenis APD berikut:
                            Sarung Tangan, Masker, Goggle, Apron, Tutup Kepala, dan Sepatu Boot.
                            Pilih <span class="text-success fw-semibold">Ya</span> bila APD digunakan sesuai, <span class="text-danger fw-semibold">Tidak</span> bila tidak digunakan.
                        </div>
                    @endif

                    @foreach($category->activeQuestions as $q)
                        <div class="checklist-item" :class="answers[{{ $q->id }}] === 'tidak' && 'has-tidak'">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="pe-2">
                                    <span class="badge bg-brand-light text-brand badge-rounded me-2">{{ $q->order }}</span>
                                    <span class="fw-medium">{{ $q->question }}</span>
                                    @error('answers.'.$q->id)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" id="q{{ $q->id }}-ya" name="answers[{{ $q->id }}]" value="ya" @change="answers[{{ $q->id }}] = 'ya'; recount()">
                                    <label class="btn btn-outline-success answer-btn" :class="answers[{{ $q->id }}] === 'ya' && 'btn-selected-ya'" for="q{{ $q->id }}-ya"><i class="bi bi-check-lg"></i> Ya</label>

                                    <input type="radio" class="btn-check" id="q{{ $q->id }}-tidak" name="answers[{{ $q->id }}]" value="tidak" @change="answers[{{ $q->id }}] = 'tidak'; recount()">
                                    <label class="btn btn-outline-danger answer-btn" :class="answers[{{ $q->id }}] === 'tidak' && 'btn-selected-tidak'" for="q{{ $q->id }}-tidak"><i class="bi bi-x-lg"></i> Tidak</label>

                                    <input type="radio" class="btn-check" id="q{{ $q->id }}-na" name="answers[{{ $q->id }}]" value="na" @change="answers[{{ $q->id }}] = 'na'; recount()">
                                    <label class="btn btn-outline-secondary answer-btn" :class="answers[{{ $q->id }}] === 'na' && 'btn-selected-na'" for="q{{ $q->id }}-na">N/A</label>
                                </div>
                            </div>

                            {{-- ====== Form Temuan otomatis saat "Tidak" ====== --}}
                            <div x-show="answers[{{ $q->id }}] === 'tidak'" x-cloak x-transition
                                 class="mt-3 p-3 bg-white rounded-4 border border-danger border-2">
                                <div class="fw-semibold text-danger small mb-2">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>TEMUAN AUDIT — Item {{ $q->order }}
                                </div>
                                <input type="hidden" name="findings[{{ $q->id }}][question_id]" value="{{ $q->id }}">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label xsmall fw-semibold mb-1">Uraian Temuan <span class="text-danger">*</span></label>
                                        <textarea name="findings[{{ $q->id }}][description]" rows="2" class="form-control form-control-sm"
                                                  placeholder="Jelaskan kondisi yang tidak sesuai...">{{ old('findings.'.$q->id.'.description', strtolower($q->question)) }}</textarea>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label xsmall fw-semibold mb-1">Lokasi</label>
                                        <input type="text" name="findings[{{ $q->id }}][location]" class="form-control form-control-sm" placeholder="cth: Ruang perawatan 3">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label xsmall fw-semibold mb-1">Tingkat Temuan</label>
                                        <div class="d-flex gap-3 pt-1">
                                            @foreach(\App\Models\Finding::SEVERITIES as $key => $label)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="findings[{{ $q->id }}][severity]" id="sev{{ $q->id }}{{ $key }}" value="{{ $key }}" {{ $key === 'minor' ? 'checked' : '' }}>
                                                    <label class="form-check-label xsmall" for="sev{{ $q->id }}{{ $key }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label xsmall fw-semibold mb-1">Foto Bukti (maks 5MB)</label>
                                        <input type="file" name="findings[{{ $q->id }}][photo]" class="form-control form-control-sm" accept="image/*" capture="environment">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label xsmall fw-semibold mb-1">Rekomendasi</label>
                                        <textarea name="findings[{{ $q->id }}][recommendation]" rows="2" class="form-control form-control-sm" placeholder="Rekomendasi perbaikan untuk unit..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-3">
                        <label class="form-label small fw-semibold">Catatan Audit</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Opsional">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ====== Live skor ====== --}}
            <div class="card mb-3" x-cloak>
                <div class="card-body d-flex flex-wrap align-items-center gap-4">
                    <div>
                        <div class="text-secondary small">Skor kepatuhan sementara (dihitung otomatis)</div>
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="display-6 fw-bold" :class="scoreColor()" x-text="score() + '%'">0%</span>
                            <span class="badge badge-rounded bg-secondary" x-text="grade()"></span>
                        </div>
                    </div>
                    <div class="flex-grow-1" style="min-width:200px">
                        <div class="progress progress-kepatuhan">
                            <div class="progress-bar" :class="scoreBg()" :style="'width:' + Math.min(100, score()) + '%'"></div>
                        </div>
                        <div class="small text-secondary mt-1">
                            <i class="bi bi-check-circle text-success"></i> Sesuai: <b x-text="countYa">0</b> &nbsp;
                            <i class="bi bi-x-circle text-danger"></i> Tidak: <b x-text="countTidak">0</b> &nbsp;
                            <i class="bi bi-dash-circle text-secondary"></i> N/A: <b x-text="countNa">0</b>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mb-4 no-print">
                <a href="{{ route('audits.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-brand rounded-pill px-4" :disabled="answered < totalQuestions">
                    <i class="bi bi-send me-1"></i> Simpan &amp; Hitung Skor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function auditForm() {
        return {
            answers: @json(old('answers', [])),
            totalQuestions: {{ $category->activeQuestions->count() }},
            countYa: 0, countTidak: 0, countNa: 0,
            unitId: '',

            init() {
                // Sinkronkan dari radio yang ter-check (mis. setelah validasi error / old input)
                document.querySelectorAll('#auditForm input[type=radio]:checked').forEach(r => {
                    const id = r.name.match(/answers\[(\d+)\]/);
                    if (id) this.answers[id[1]] = r.value;
                });
                this.recount();
            },

            get answered() {
                return Object.keys(this.answers).filter(k => ['ya', 'tidak', 'na'].includes(this.answers[k])).length;
            },

            recount() {
                const vals = Object.values(this.answers);
                this.countYa = vals.filter(v => v === 'ya').length;
                this.countTidak = vals.filter(v => v === 'tidak').length;
                this.countNa = vals.filter(v => v === 'na').length;
            },

            score() {
                const assessed = this.countYa + this.countTidak;
                if (assessed === 0) return 0;
                return Math.round((this.countYa / assessed) * 1000) / 10;
            },

            grade() {
                const s = this.score();
                const t = @json(\App\Support\Ppi::thresholds());
                if (!this.countYa && !this.countTidak) return '-';
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
@endpush
