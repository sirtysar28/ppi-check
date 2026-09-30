<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditCategory;
use App\Models\Finding;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Filter: periode, unit, kategori, auditor, shift
        $filters = $this->filters($request);

        $audits = Audit::query()->final()
            ->with(['unit', 'category', 'auditor'])
            ->when($filters['start'], fn ($q, $v) => $q->whereDate('audit_date', '>=', $v))
            ->when($filters['end'], fn ($q, $v) => $q->whereDate('audit_date', '<=', $v))
            ->when($filters['unit_id'], fn ($q, $v) => $q->where('unit_id', $v))
            ->when($filters['category_id'], fn ($q, $v) => $q->where('category_id', $v))
            ->when($filters['auditor_id'], fn ($q, $v) => $q->where('auditor_id', $v))
            ->when($filters['shift'], fn ($q, $v) => $q->where('shift', $v));

        // Unit role hanya melihat unit sendiri
        if ($user->role === User::ROLE_UNIT) {
            $audits->where('unit_id', $user->unit_id);
        }

        $statsAudits = (clone $audits)->get();

        // Kartu ringkasan
        $totalAudits = $statsAudits->count();
        $avgOverall = $totalAudits > 0 ? round($statsAudits->avg('compliance_percentage'), 1) : 0;
        $perCategory = AuditCategory::where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(function ($cat) use ($statsAudits) {
                $rows = $statsAudits->where('category_id', $cat->id);

                return [
                    'category' => $cat,
                    'count' => $rows->count(),
                    'avg' => $rows->count() > 0 ? round($rows->avg('compliance_percentage'), 1) : 0,
                ];
            });

        // Tren kepatuhan bulanan (6 bulan terakhir dalam rentang filter)
        $trendStart = $filters['start'] ? \Illuminate\Support\Carbon::parse($filters['start']) : now()->subMonths(5)->startOfMonth();
        $trendEnd = $filters['end'] ? \Illuminate\Support\Carbon::parse($filters['end']) : now();

        $months = collect(range(0, 11))->map(fn ($i) => $trendStart->copy()->addMonths($i))
            ->filter(fn ($m) => $m->lessThan($trendEnd->copy()->endOfMonth()))
            ->take(6);

        $trend = $months->map(function ($m) use ($statsAudits) {
            $rows = $statsAudits->filter(fn ($a) => $a->audit_date->format('Y-m') === $m->format('Y-m'));

            return [
                'label' => $m->translatedFormat('M y'),
                'avg' => $rows->count() > 0 ? round($rows->avg('compliance_percentage'), 1) : null,
                'count' => $rows->count(),
            ];
        })->values();

        // Audit per unit (rata-rata kepatuhan)
        $perUnit = Unit::where('is_active', true)
            ->when($user->role === User::ROLE_UNIT, fn ($q) => $q->where('id', $user->unit_id))
            ->withCount(['audits' => fn ($q) => $q->where('status', 'final')])
            ->get()
            ->map(function ($unit) use ($statsAudits) {
                $rows = $statsAudits->where('unit_id', $unit->id);

                return [
                    'unit' => $unit,
                    'avg' => $rows->count() > 0 ? round($rows->avg('compliance_percentage'), 1) : 0,
                    'count' => $rows->count(),
                ];
            })
            ->sortByDesc('avg')
            ->values();

        // Temuan
        $findingsQuery = Finding::query()
            ->when($filters['unit_id'], fn ($q, $v) => $q->where('unit_id', $v))
            ->when($filters['category_id'], fn ($q, $v) => $q->where('category_id', $v));
        if ($user->role === User::ROLE_UNIT) {
            $findingsQuery->where('unit_id', $user->unit_id);
        }

        $findingStats = [
            'open' => (clone $findingsQuery)->where('status', 'open')->count(),
            'progress' => (clone $findingsQuery)->where('status', 'progress')->count(),
            'closed' => (clone $findingsQuery)->where('status', 'closed')->count(),
        ];

        $recentAudits = (clone $audits)->latest('audit_date')->take(6)->get();
        $recentFindings = (clone $findingsQuery)->with(['unit', 'category'])
            ->where('status', '!=', 'closed')->latest()->take(6)->get();

        return view('dashboard.index', compact(
            'totalAudits', 'avgOverall', 'perCategory', 'trend', 'perUnit',
            'findingStats', 'recentAudits', 'recentFindings', 'filters'
        ));
    }

    public function unitDetail(Request $request, Unit $unit): View
    {
        $user = $request->user();
        abort_unless($user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI, User::ROLE_AUDITOR]) || $unit->id === $user->unit_id, 403);

        $start = $request->date('start');
        $end = $request->date('end');

        $audits = $unit->audits()->final()
            ->when($start, fn ($q) => $q->whereDate('audit_date', '>=', $start))
            ->when($end, fn ($q) => $q->whereDate('audit_date', '<=', $end))
            ->with(['category', 'auditor'])
            ->get();

        $overall = $audits->count() > 0 ? round($audits->avg('compliance_percentage'), 1) : 0;

        $perCategory = AuditCategory::where('is_active', true)->get()->map(function ($cat) use ($audits) {
            $rows = $audits->where('category_id', $cat->id);

            return [
                'category' => $cat,
                'avg' => $rows->count() > 0 ? round($rows->avg('compliance_percentage'), 1) : 0,
                'count' => $rows->count(),
            ];
        });

        $findings = $unit->findings();
        $statFindings = [
            'total' => (clone $findings)->count(),
            'open' => (clone $findings)->where('status', 'open')->count(),
            'progress' => (clone $findings)->where('status', 'progress')->count(),
            'closed' => (clone $findings)->where('status', 'closed')->count(),
        ];

        return view('dashboard.unit-detail', compact('unit', 'audits', 'overall', 'perCategory', 'statFindings', 'start', 'end'));
    }

    private function filters(Request $request): array
    {
        return [
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'unit_id' => $request->input('unit_id'),
            'category_id' => $request->input('category_id'),
            'auditor_id' => $request->input('auditor_id'),
            'shift' => $request->input('shift'),
            'status' => $request->input('status'),
        ];
    }
}
