<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditCategory;
use App\Models\Finding;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->rekapData($request);

        return view('reports.index', $data + [
            'categories' => AuditCategory::where('is_active', true)->get(),
            'units' => Unit::where('is_active', true)->get(),
            'auditors' => User::where('role', User::ROLE_AUDITOR)->where('is_active', true)->get(),
        ]);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->rekapData($request);

        return Excel::download(new \App\Exports\AuditsExport($data['audits']), 'laporan-audit-ppi-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $data = $this->rekapData($request);

        $pdf = Pdf::loadView('reports.pdf', $data + [
            'facility' => [
                'name' => Setting::get('facility_name'),
                'address' => Setting::get('facility_address'),
            ],
        ]);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('laporan-audit-ppi-' . now()->format('Ymd-His') . '.pdf');
    }

    private function rekapData(Request $request): array
    {
        $user = $request->user();

        $audits = Audit::query()->final()->with(['unit', 'category', 'auditor'])
            ->when($request->input('start'), fn ($q, $v) => $q->whereDate('audit_date', '>=', $v))
            ->when($request->input('end'), fn ($q, $v) => $q->whereDate('audit_date', '<=', $v))
            ->when($request->input('category_id'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->input('unit_id'), fn ($q, $v) => $q->where('unit_id', $v))
            ->when($request->input('auditor_id'), fn ($q, $v) => $q->where('auditor_id', $v))
            ->when($user->role === User::ROLE_UNIT, fn ($q) => $q->where('unit_id', $user->unit_id))
            ->orderBy('audit_date', 'desc')
            ->get();

        // Rekap per unit
        $perUnit = $audits->groupBy('unit_id')->map(function ($rows, $unitId) {
            return [
                'unit' => $rows->first()->unit,
                'count' => $rows->count(),
                'avg' => round($rows->avg('compliance_percentage'), 1),
            ];
        })->sortByDesc('avg')->values();

        // Rekap per kategori
        $perCategory = $audits->groupBy('category_id')->map(function ($rows) {
            return [
                'category' => $rows->first()->category,
                'count' => $rows->count(),
                'avg' => round($rows->avg('compliance_percentage'), 1),
            ];
        })->values();

        return [
            'audits' => $audits,
            'perUnit' => $perUnit,
            'perCategory' => $perCategory,
            'filters' => $request->only(['start', 'end', 'category_id', 'unit_id', 'auditor_id']),
        ];
    }
}
