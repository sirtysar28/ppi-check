@php
    $me = auth()->user();

    $notifQuery = \App\Models\Finding::query()
        ->with('unit')
        ->whereIn('status', ['open', 'progress'])
        ->when($me->role === \App\Models\User::ROLE_UNIT, fn ($q) => $q->where('unit_id', $me->unit_id));

    $overdueCount   = (clone $notifQuery)->whereDate('due_date', '<', today())->count();
    $openCount      = (clone $notifQuery)->where('status', 'open')->count();
    $pendingVerify  = $me->can('verify-followup') ? (clone $notifQuery)->where('status', 'progress')->count() : 0;
    $notifTotal     = $overdueCount + $openCount + $pendingVerify;

    $notifRecent = (clone $notifQuery)
        ->when(! $me->can('verify-followup'), fn ($q) => $q->where('status', 'open'))
        ->latest('id')
        ->limit(5)
        ->get();
@endphp

<div class="dropdown nav-notif">
    <button class="btn nav-notif-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside"
            aria-label="Notifikasi" title="Notifikasi">
        <i class="bi bi-bell"></i>
        @if($notifTotal > 0)
            <span class="nav-notif-badge">{{ $notifTotal > 99 ? '99+' : $notifTotal }}</span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow nav-notif-menu">
        <div class="nav-notif-header">
            <span class="fw-semibold">Notifikasi</span>
            @if($notifTotal > 0)
                <span class="badge bg-danger badge-rounded">{{ $notifTotal }}</span>
            @endif
        </div>

        <div class="nav-notif-summary">
            @if($openCount > 0)
                <a href="{{ route('findings.index', ['status' => 'open']) }}" class="nav-notif-stat">
                    <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                    <div>
                        <div class="fw-bold">{{ $openCount }}</div>
                        <small>Temuan menunggu tindak lanjut</small>
                    </div>
                </a>
            @endif
            @if($overdueCount > 0)
                <a href="{{ route('findings.index', ['status' => 'open']) }}" class="nav-notif-stat">
                    <i class="bi bi-alarm-fill text-danger"></i>
                    <div>
                        <div class="fw-bold">{{ $overdueCount }}</div>
                        <small>Lewat batas waktu tindak lanjut</small>
                    </div>
                </a>
            @endif
            @if($pendingVerify > 0)
                <a href="{{ route('followups.index') }}" class="nav-notif-stat">
                    <i class="bi bi-hourglass-split text-info"></i>
                    <div>
                        <div class="fw-bold">{{ $pendingVerify }}</div>
                        <small>Tindak lanjut menunggu verifikasi</small>
                    </div>
                </a>
            @endif
        </div>

        @if($notifRecent->isNotEmpty())
            <div class="nav-notif-list">
                @foreach($notifRecent as $finding)
                    <a href="{{ route('findings.show', $finding) }}" class="nav-notif-item">
                        <span class="badge badge-rounded {{ $finding->status === 'progress' ? 'bg-warning' : 'bg-danger' }} me-2">{{ $finding->finding_number }}</span>
                        <span class="small">
                            {{ \Illuminate\Support\Str::limit($finding->description, 46) }}
                        </span>
                        <span class="d-block text-secondary" style="font-size:.68rem">
                            {{ $finding->unit?->name }}
                            @unless($finding->due_date?->isPast() && $finding->status !== 'closed')
                                &middot; jatuh tempo {{ $finding->due_date?->translatedFormat('d M Y') }}
                            @else
                                &middot; <span class="text-danger fw-semibold">lewat jatuh tempo</span>
                            @endunless
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="nav-notif-empty">
                <i class="bi bi-check2-circle"></i>
                <div>Tidak ada notifikasi baru</div>
            </div>
        @endif

        <a href="{{ route('findings.index') }}" class="nav-notif-footer">
            Lihat semua temuan &rarr;
        </a>
    </div>
</div>
