@extends('layouts.app')

@section('title', 'Temuan ' . $finding->finding_number)
@section('page_title', 'Detail Temuan')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-9">

        {{-- Detail Temuan --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $finding->finding_number }}</h4>
                        <div class="small text-secondary">
                            <i class="bi {{ $finding->category->icon }} me-1"></i>{{ $finding->category->name }} ·
                            {{ $finding->unit->name }} ·
                            {{ $finding->created_at->translatedFormat('d M Y') }}
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge badge-rounded bg-{{ $finding->severityColor() }} fs-6"> Tingkat: {{ ucfirst($finding->severity) }} </span>
                        <span class="badge badge-rounded bg-{{ $finding->statusColor() }} fs-6"> Status: {{ strtoupper($finding->status) }} </span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="xsmall text-secondary fw-semibold mb-1">TEMUAN</div>
                            <p class="mb-2">{{ $finding->description }}</p>
                            @if($finding->location)
                                <div class="xsmall text-secondary fw-semibold mb-1">LOKASI</div>
                                <p class="mb-2 small"><i class="bi bi-geo-alt me-1"></i>{{ $finding->location }}</p>
                            @endif
                            @if($finding->recommendation)
                                <div class="xsmall text-secondary fw-semibold mb-1">REKOMENDASI</div>
                                <p class="mb-0 small text-brand">{{ $finding->recommendation }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="border rounded-3 p-3 h-100 text-center">
                            <div class="xsmall text-secondary fw-semibold mb-2">FOTO BUKTI TEMUAN</div>
                            @if($finding->photo_path)
                                <a href="{{ asset('storage/' . $finding->photo_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $finding->photo_path) }}" class="img-fluid rounded-3" alt="Foto bukti">
                                </a>
                            @else
                                <div class="py-4 text-secondary"><i class="bi bi-image fs-1 d-block mb-2"></i>Tidak ada foto</div>
                            @endif
                            <hr class="my-2">
                            <div class="xsmall text-secondary">Batas Tindak Lanjut</div>
                            <div class="fw-bold {{ $finding->due_date && $finding->due_date->isPast() && $finding->status !== 'closed' ? 'text-danger' : '' }}">
                                {{ $finding->due_date?->translatedFormat('d M Y') ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 small">
                    <a href="{{ route('audits.show', $finding->audit) }}" class="text-decoration-none">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Lihat audit terkait: {{ $finding->audit->audit_number }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Form / Timeline Tindak Lanjut & Verifikasi --}}
        <div class="row g-3">
            <div class="col-12 col-lg-7">
                <div class="card h-100">
                    <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                        <i class="bi bi-arrow-repeat me-2 text-brand"></i>Riwayat Tindak Lanjut &amp; Verifikasi
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            {{-- Status OPEN --}}
                            <div class="tl-item">
                                <div class="tl-dot" style="background:#dc3545"></div>
                                <div class="fw-semibold small">Temuan dibuat (OPEN)</div>
                                <div class="xsmall text-secondary">{{ $finding->created_at->translatedFormat('d M Y H:i') }} oleh {{ $finding->audit->auditor->name }}</div>
                            </div>

                            @foreach($finding->followUps as $followUp)
                                <div class="tl-item">
                                    <div class="tl-dot" style="background:#0d7a66"></div>
                                    <div class="border rounded-3 p-2 bg-light">
                                        <div class="d-flex justify-content-between flex-wrap gap-1">
                                            <span class="fw-semibold small">Tindak Lanjut oleh {{ $followUp->user->name }}</span>
                                            <span class="badge badge-rounded bg-{{ $followUp->status === 'accepted' ? 'success' : ($followUp->status === 'rejected' ? 'danger' : 'warning') }}">{{ strtoupper($followUp->status) }}</span>
                                        </div>
                                        <div class="small mt-1">{{ $followUp->action }}</div>
                                        <div class="xsmall text-secondary mt-1">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $followUp->follow_up_date->translatedFormat('d M Y') }}
                                        </div>
                                        @if($followUp->photo_path)
                                            <a href="{{ asset('storage/' . $followUp->photo_path) }}" target="_blank" class="d-inline-block mt-1">
                                                <img src="{{ asset('storage/' . $followUp->photo_path) }}" class="rounded-3" style="max-height:110px" alt="Bukti tindak lanjut">
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @foreach($finding->verifications->where('follow_up_id', $followUp->id) as $verification)
                                    <div class="tl-item">
                                        <div class="tl-dot" style="background: {{ $verification->result === 'accepted' ? '#198754' : '#dc3545' }}"></div>
                                        <div class="border rounded-3 p-2 {{ $verification->result === 'accepted' ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                            <div class="d-flex justify-content-between flex-wrap gap-1">
                                                <span class="fw-semibold small">
                                                    Verifikasi {{ $verification->result === 'accepted' ? 'DITERIMA ✅' : 'DITOLAK ❌' }}
                                                </span>
                                                <span class="xsmall text-secondary">{{ $verification->verification_date->translatedFormat('d M Y') }}</span>
                                            </div>
                                            <div class="small mt-1">{{ $verification->notes ?? '-' }}</div>
                                            <div class="xsmall text-secondary mt-1">Oleh: {{ $verification->verifier->name }}</div>
                                            @if($verification->photo_path)
                                                <a href="{{ asset('storage/' . $verification->photo_path) }}" target="_blank" class="d-inline-block mt-1">
                                                    <img src="{{ asset('storage/' . $verification->photo_path) }}" class="rounded-3" style="max-height:110px" alt="Foto verifikasi">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach

                            @if($finding->status === 'closed')
                                <div class="tl-item">
                                    <div class="tl-dot" style="background:#198754"></div>
                                    <div class="fw-semibold small text-success">Temuan CLOSED ✅</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                {{-- Form tindak lanjut (unit) --}}
                @if(in_array($finding->status, ['open']) && (auth()->user()->hasRole(['super_admin', 'admin_ppi', 'auditor']) || $finding->unit_id === auth()->user()->unit_id))
                    <div class="card mb-3 border-2" style="border-color: var(--ppi-teal)">
                        <div class="card-header bg-brand text-white fw-semibold" style="border-radius:calc(1rem - 2px) calc(1rem - 2px) 0 0">
                            <i class="bi bi-reply me-2"></i>Input Tindak Lanjut
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('followups.store', $finding) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Tindakan / Perbaikan <span class="text-danger">*</span></label>
                                    <textarea name="action" rows="3" class="form-control" placeholder="Jelaskan tindakan perbaikan yang telah dilakukan..." required>{{ old('action') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Tanggal Perbaikan <span class="text-danger">*</span></label>
                                    <input type="date" name="follow_up_date" class="form-control" value="{{ old('follow_up_date', now()->toDateString()) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Upload Bukti (foto, maks 5MB)</label>
                                    <input type="file" name="photo" class="form-control" accept="image/*" capture="environment">
                                </div>
                                <button class="btn btn-brand w-100"><i class="bi bi-send me-1"></i>Kirim Tindak Lanjut</button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Form verifikasi (auditor) --}}
                @if($finding->status === 'progress' && auth()->user()->can('verify-followup'))
                    <div class="card mb-3 border-2 border-warning">
                        <div class="card-header bg-warning fw-semibold" style="border-radius:calc(1rem - 2px) calc(1rem - 2px) 0 0">
                            <i class="bi bi-patch-check me-2"></i>Verifikasi Tindak Lanjut
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('verifications.store', $finding) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Hasil Verifikasi <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="result" id="result-accepted" value="accepted" checked>
                                            <label class="form-check-label small fw-semibold text-success" for="result-accepted">DITERIMA (Closed)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="result" id="result-rejected" value="rejected">
                                            <label class="form-check-label small fw-semibold text-danger" for="result-rejected">DITOLAK (Ulangi)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Tanggal Verifikasi <span class="text-danger">*</span></label>
                                    <input type="date" name="verification_date" class="form-control" value="{{ old('verification_date', now()->toDateString()) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Catatan Verifikasi</label>
                                    <textarea name="notes" rows="2" class="form-control" placeholder="Opsional">{{ old('notes') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Foto Verifikasi (maks 5MB)</label>
                                    <input type="file" name="photo" class="form-control" accept="image/*" capture="environment">
                                </div>
                                <button class="btn btn-warning w-100 fw-semibold"><i class="bi bi-check2-square me-1"></i>Simpan Verifikasi</button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- Info status --}}
                <div class="card">
                    <div class="card-body small">
                        <div class="fw-semibold mb-2">Alur Penyelesaian Temuan</div>
                        <ol class="ps-3 mb-0 text-secondary" style="line-height:1.9">
                            <li><b>OPEN</b> — Temuan dibuat, unit melakukan perbaikan</li>
                            <li><b>PROGRESS</b> — Tindak lanjut dikirim, menunggu verifikasi auditor</li>
                            <li><b>DITOLAK</b> — Kembali ke OPEN, unit mengulang tindak lanjut</li>
                            <li><b>CLOSED</b> — Verifikasi diterima, temuan selesai</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
