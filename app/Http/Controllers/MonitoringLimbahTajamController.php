<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SharpWasteMonitoring;
use App\Models\SharpWasteMonitoringItem;
use App\Models\Unit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringLimbahTajamController extends Controller
{
    public function create(): View
    {
        $units = Unit::where('is_active', true)->orderBy('name')->get();

        return view('monitoring.limbah-tajam.create', [
            'units' => $units,
            'items' => SharpWasteMonitoring::STATEMENTS,
        ]);
    }

    public function store(Request $request)
    {
        $statements = SharpWasteMonitoring::STATEMENTS;

        $validated = $request->validate([
            'monitoring_date' => ['required', 'date'],
            'unit_id' => ['required', 'exists:units,id'],
            'officer_name' => ['required', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'size:' . count($statements)],
            'items.*.pernyataan' => ['required', 'string'],
            'items.*.jawaban' => ['required', 'in:Ya,Tidak'],
            'items.*.keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'items.*.jawaban.required' => 'Setiap pernyataan wajib dijawab Ya atau Tidak.',
            'items.*.jawaban.in' => 'Jawaban hanya boleh Ya atau Tidak.',
        ]);

        // Pastikan pernyataan tidak dimanipulasi
        foreach ($validated['items'] as $item) {
            if (! in_array($item['pernyataan'], $statements, true)) {
                return back()->withErrors(['items' => 'Pernyataan tidak valid.'])->withInput();
            }
        }

        $totalYa = collect($validated['items'])->where('jawaban', 'Ya')->count();
        $totalTidak = count($validated['items']) - $totalYa;
        $percentage = count($validated['items']) > 0
            ? round(($totalYa / count($validated['items'])) * 100, 2)
            : 0;

        $year = now()->year;
        $seq = (int) SharpWasteMonitoring::where('monitoring_number', 'like', "LBT-{$year}-%")->count() + 1;
        do {
            $number = sprintf('LBT-%d-%04d', $year, $seq);
            $seq++;
        } while (SharpWasteMonitoring::where('monitoring_number', $number)->exists());

        $monitoring = SharpWasteMonitoring::create([
            'monitoring_number' => $number,
            'unit_id' => $validated['unit_id'],
            'officer_name' => $validated['officer_name'],
            'monitoring_date' => $validated['monitoring_date'],
            'total_items' => count($validated['items']),
            'conform_items' => $totalYa,
            'nonconform_items' => $totalTidak,
            'compliance_percentage' => $percentage,
            'grade' => Setting::grade($percentage),
            'user_id' => $request->user()->id,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach (array_values($validated['items']) as $i => $item) {
            SharpWasteMonitoringItem::create([
                'monitoring_id' => $monitoring->id,
                'statement' => $item['pernyataan'],
                'answer' => $item['jawaban'] === 'Ya' ? 'ya' : 'tidak',
                'notes' => $item['keterangan'] ?? null,
                'order' => $i + 1,
            ]);
        }

        return redirect()
            ->route('monitoring-limbah-tajam.show', $monitoring)
            ->with('success', "Monitoring berhasil disimpan ({$number}). Persentase: {$percentage}%.");
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $monitorings = SharpWasteMonitoring::query()->with(['unit', 'user'])
            ->when($request->input('q'), function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('monitoring_number', 'like', "%{$v}%")
                        ->orWhere('officer_name', 'like', "%{$v}%")
                        ->orWhereHas('unit', fn ($u) => $u->where('name', 'like', "%{$v}%"));
                });
            })
            ->when($request->input('unit_id'), fn ($q, $v) => $q->where('unit_id', $v))
            ->when($request->filled('start'), fn ($q, $v) => $q->whereDate('monitoring_date', '>=', $v))
            ->when($request->filled('end'), fn ($q, $v) => $q->whereDate('monitoring_date', '<=', $v))
            ->when($user->role === User::ROLE_UNIT, fn ($q) => $q->where('unit_id', $user->unit_id))
            ->latest('monitoring_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('monitoring.limbah-tajam.index', [
            'monitorings' => $monitorings,
            'units' => Unit::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function show(SharpWasteMonitoring $monitoring): View
    {
        $monitoring->load(['unit', 'user', 'items']);

        return view('monitoring.limbah-tajam.show', compact('monitoring'));
    }

    public function pdf(SharpWasteMonitoring $monitoring)
    {
        $monitoring->load(['unit', 'user', 'items']);

        $pdf = Pdf::loadView('monitoring.limbah-tajam.pdf', [
            'monitoring' => $monitoring,
            'facility' => [
                'name' => Setting::get('facility_name'),
                'address' => Setting::get('facility_address'),
            ],
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($monitoring->monitoring_number . '.pdf');
    }

    public function destroy(SharpWasteMonitoring $monitoring)
    {
        $this->authorize('manage-masters');

        $number = $monitoring->monitoring_number;
        $monitoring->delete();

        return redirect()->route('monitoring-limbah-tajam.index')
            ->with('success', "Data monitoring {$number} telah dihapus.");
    }
}
