<?php

namespace App\Http\Controllers;

use App\Models\ApdAction;
use App\Models\ApdType;
use App\Models\Audit;
use App\Models\AuditAnswer;
use App\Models\AuditCategory;
use App\Models\Finding;
use App\Models\HandHygieneObservation;
use App\Models\Profession;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use App\Models\WasteType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuditController extends Controller
{
    /** Kategori audit yang boleh dilaksanakan sendiri oleh role Unit (audit mandiri). */
    public const UNIT_ALLOWED_CATEGORIES = ['cuci-tangan', 'apd'];

    public function index(Request $request): View
    {
        $user = $request->user();

        $audits = Audit::query()->with(['unit', 'category', 'auditor'])
            ->when($request->input('q'), function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('audit_number', 'like', "%{$v}%")
                        ->orWhere('officer_name', 'like', "%{$v}%")
                        ->orWhereHas('unit', fn ($u) => $u->where('name', 'like', "%{$v}%"));
                });
            })
            ->when($request->input('category_id'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->input('unit_id'), fn ($q, $v) => $q->where('unit_id', $v))
            ->when($request->input('start'), fn ($q, $v) => $q->whereDate('audit_date', '>=', $v))
            ->when($request->input('end'), fn ($q, $v) => $q->whereDate('audit_date', '<=', $v))
            ->when($request->input('shift'), fn ($q, $v) => $q->where('shift', $v))
            ->when($request->filled('grade'), fn ($q) => $q->where('grade', $request->input('grade')))
            ->when($user->role === User::ROLE_UNIT, fn ($q) => $q->where('unit_id', $user->unit_id))
            ->when($user->role === User::ROLE_AUDITOR && $request->input('mine') === '1', fn ($q) => $q->where('auditor_id', $user->id))
            ->latest('audit_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $categories = AuditCategory::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();

        return view('audits.index', compact('audits', 'categories', 'units'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $categoryCode = $request->input('category', 'cuci-tangan');
        $category = AuditCategory::where('code', $categoryCode)->where('is_active', true)->firstOrFail();

        // Role Unit hanya boleh audit mandiri Cuci Tangan & APD untuk unitnya sendiri
        if ($user->role === User::ROLE_UNIT && ! in_array($category->code, self::UNIT_ALLOWED_CATEGORIES, true)) {
            abort(403, 'Unit hanya dapat melaksanakan Audit Cuci Tangan dan Audit APD.');
        }

        $categories = AuditCategory::where('is_active', true)
            ->when($user->role === User::ROLE_UNIT, fn ($q) => $q->whereIn('code', self::UNIT_ALLOWED_CATEGORIES))
            ->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        if ($user->role === User::ROLE_UNIT) {
            $units = Unit::whereKey($user->unit_id)->get();
        }
        $auditors = User::where('role', 'auditor')->where('is_active', true)->orderBy('name')->get();
        $professions = Profession::where('is_active', true)->get();

        // Lembar Audit Cuci Tangan punya form khusus (24 observasi: momen + tindakan)
        if ($category->code === 'cuci-tangan') {
            return view('audits.forms.cuci-tangan', compact('category', 'categories', 'units', 'auditors', 'professions'));
        }

        $apdTypes = ApdType::where('is_active', true)->get();
        $wasteTypes = WasteType::where('is_active', true)->get();
        $apdActions = ApdAction::where('is_active', true)->orderBy('order')->orderBy('name')->get();

        return view('audits.create', compact(
            'category', 'categories', 'units', 'auditors', 'professions', 'apdTypes', 'wasteTypes', 'apdActions'
        ));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Form khusus: Lembar Audit Cuci Tangan (24 observasi momen + tindakan)
        $category = AuditCategory::find($request->input('category_id'));
        if ($category?->code === 'cuci-tangan') {
            return $this->storeHandHygiene($request, $category, $user);
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:audit_categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'auditor_id' => ['required', 'exists:users,id'],
            'audit_date' => ['required', 'date'],
            'shift' => ['required', 'in:pagi,siang,malam'],
            'officer_name' => ['nullable', 'string', 'max:150'],
            'profession_id' => ['nullable', 'exists:professions,id'],
            'action_type' => ['nullable', 'string', 'max:150'],
            'apd_type_id' => ['nullable', 'exists:apd_types,id'],
            'waste_type_id' => ['nullable', 'exists:waste_types,id'],
            'notes' => ['nullable', 'string'],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['required', 'in:ya,tidak,na'],
            'findings' => ['nullable', 'array'],
            'findings.*.question_id' => ['nullable', 'exists:audit_questions,id'],
            'findings.*.description' => ['required_with:findings', 'string', 'max:1000'],
            'findings.*.location' => ['nullable', 'string', 'max:200'],
            'findings.*.severity' => ['nullable', 'in:minor,mayor,kritis'],
            'findings.*.recommendation' => ['nullable', 'string', 'max:1000'],
            'findings.*.photo' => ['nullable', 'image', 'max:5120'],
        ], [
            'answers.required' => 'Checklist wajib diisi.',
            'answers.*.required' => 'Semua item checklist wajib dipilih (Ya / Tidak / N/A).',
        ]);

        // Hanya admin/auditor yang boleh memilih auditor lain; auditor & unit (audit mandiri) default dirinya
        $auditorId = $validated['auditor_id'];
        if (in_array($user->role, [User::ROLE_AUDITOR, User::ROLE_UNIT], true)) {
            $auditorId = $user->id;
        }

        // Role Unit wajib audit untuk unitnya sendiri
        if ($user->role === User::ROLE_UNIT) {
            $validated['unit_id'] = $user->unit_id;
        }

        $category = AuditCategory::findOrFail($validated['category_id']);

        // Role Unit hanya boleh audit mandiri kategori tertentu
        if ($user->role === User::ROLE_UNIT && ! in_array($category->code, self::UNIT_ALLOWED_CATEGORIES, true)) {
            abort(403, 'Unit hanya dapat melaksanakan Audit Cuci Tangan dan Audit APD.');
        }

        $questions = $category->activeQuestions;

        // Pastikan semua pertanyaan terjawab
        foreach ($questions as $q) {
            if (! isset($validated['answers'][$q->id])) {
                return back()->withErrors(['answers' => 'Item #' . $q->order . ' belum dijawab.'])->withInput();
            }
        }

        // Tindakan wajib dipilih pada audit APD
        if ($category->code === 'apd' && empty($validated['action_type'])) {
            return back()->withErrors(['action_type' => 'Tindakan yang diobservasi wajib dipilih.'])->withInput();
        }

        DB::transaction(function () use ($validated, $questions, $category, $auditorId, $request, &$audit) {
            $conform = 0;
            $nonConform = 0;
            $na = 0;

            foreach ($questions as $q) {
                $ans = $validated['answers'][$q->id];
                if ($ans === 'ya') {
                    $conform++;
                } elseif ($ans === 'tidak') {
                    $nonConform++;
                } else {
                    $na++;
                }
            }

            $assessed = $conform + $nonConform;
            $compliance = $assessed > 0 ? round(($conform / $assessed) * 100, 2) : 0;

            $year = now()->year;
            $seq = (int) Audit::where('audit_number', 'like', "PPI-{$year}-%")->count() + 1;
            do {
                $number = sprintf('PPI-%d-%04d', $year, $seq);
                $seq++;
            } while (Audit::where('audit_number', $number)->exists());

            $audit = Audit::create([
                'audit_number' => $number,
                'category_id' => $validated['category_id'],
                'unit_id' => $validated['unit_id'],
                'auditor_id' => $auditorId,
                'audit_date' => $validated['audit_date'],
                'shift' => $validated['shift'],
                'officer_name' => $validated['officer_name'] ?? null,
                'profession_id' => $validated['profession_id'] ?? null,
                'action_type' => $validated['action_type'] ?? null,
                'apd_type_id' => $validated['apd_type_id'] ?? null,
                'waste_type_id' => $validated['waste_type_id'] ?? null,
                'total_items' => $questions->count(),
                'conform_items' => $conform,
                'nonconform_items' => $nonConform,
                'na_items' => $na,
                'compliance_percentage' => $compliance,
                'grade' => Setting::grade($compliance),
                'status' => 'final',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($questions as $q) {
                AuditAnswer::create([
                    'audit_id' => $audit->id,
                    'question_id' => $q->id,
                    'answer' => $validated['answers'][$q->id],
                    'score' => $validated['answers'][$q->id] === 'ya' ? 1 : 0,
                ]);
            }

            // Temuan otomatis dari jawaban "Tidak Sesuai"
            $findingSeq = (int) Finding::where('finding_number', 'like', "TMN-{$year}-%")->count();
            $deadlineDays = (int) Setting::get('follow_up_deadline_days', '7');

            foreach ($questions as $q) {
                if ($validated['answers'][$q->id] !== 'tidak') {
                    continue;
                }

                $input = $validated['findings'][$q->id] ?? null;
                $findingSeq++;
                $finding = Finding::create([
                    'finding_number' => sprintf('TMN-%d-%04d', $year, $findingSeq),
                    'audit_id' => $audit->id,
                    'category_id' => $category->id,
                    'question_id' => $q->id,
                    'unit_id' => $validated['unit_id'],
                    'description' => $input['description'] ?? ('Tidak sesuai: ' . strtolower($q->question)),
                    'location' => $input['location'] ?? null,
                    'severity' => $input['severity'] ?? 'minor',
                    'photo_path' => $request->hasFile("findings.{$q->id}.photo")
                        ? $request->file("findings.{$q->id}.photo")->store('findings', 'public')
                        : null,
                    'recommendation' => $input['recommendation'] ?? null,
                    'due_date' => now()->addDays($deadlineDays)->toDateString(),
                    'status' => 'open',
                ]);
            }
        });

        return redirect()->route('audits.show', $audit)
            ->with('success', 'Audit berhasil disimpan. Skor kepatuhan dihitung otomatis: ' . $audit->compliance_percentage . '%.');
    }

    /**
     * Simpan Lembar Audit Cuci Tangan sesuai format Word:
     * header (observer, ruang, bulan/tanggal) + maks 24 observasi,
     * tiap observasi = momen (5 Momen WHO) + tindakan (HR/HW/Tidak/Set lepas sarung tangan).
     * Kepatuhan = tindakan patuh / seluruh observasi terisi x 100%.
     */
    private function storeHandHygiene(Request $request, AuditCategory $category, User $user)
    {
        $validated = $request->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'auditor_id' => ['required', 'exists:users,id'],
            'audit_date' => ['required', 'date'],
            'shift' => ['required', 'in:pagi,siang,malam'],
            'officer_name' => ['nullable', 'string', 'max:150'],
            'profession_id' => ['nullable', 'exists:professions,id'],
            'notes' => ['nullable', 'string'],
            'observations' => ['nullable', 'array', 'max:24'],
            'observations.*.moment' => ['nullable', 'string', 'in:' . implode(',', array_keys(HandHygieneObservation::MOMENTS))],
            'observations.*.action' => ['nullable', 'string', 'in:' . implode(',', array_keys(HandHygieneObservation::ACTIONS))],
        ], [
            'observations.*.moment.in' => 'Momen observasi tidak valid.',
            'observations.*.action.in' => 'Tindakan observasi tidak valid.',
        ]);

        // Kumpulkan baris yang terisi; momen & tindakan wajib berpasangan
        $rows = [];
        foreach ((array) ($validated['observations'] ?? []) as $seq => $row) {
            $moment = $row['moment'] ?? null;
            $action = $row['action'] ?? null;

            if ($moment === null && $action === null) {
                continue; // baris kosong (peluang tidak terpakai)
            }

            if ($moment === null || $action === null) {
                return back()
                    ->withErrors(['observations' => "Observasi #{$seq}: Momen dan Tindakan wajib diisi keduanya."])
                    ->withInput();
            }

            $rows[(int) $seq] = ['moment' => $moment, 'action' => $action];
        }

        if (empty($rows)) {
            return back()->withErrors(['observations' => 'Minimal 1 observasi harus diisi.'])->withInput();
        }

        // Hanya admin/auditor yang boleh memilih auditor lain; auditor & unit (audit mandiri) default dirinya
        $auditorId = in_array($user->role, [User::ROLE_AUDITOR, User::ROLE_UNIT], true) ? $user->id : $validated['auditor_id'];

        // Role Unit wajib observasi untuk unitnya sendiri
        if ($user->role === User::ROLE_UNIT) {
            $validated['unit_id'] = $user->unit_id;
        }

        $conform = 0;
        foreach ($rows as $row) {
            if (in_array($row['action'], HandHygieneObservation::COMPLIANT_ACTIONS, true)) {
                $conform++;
            }
        }
        $nonConform = count($rows) - $conform;
        $compliance = \App\Support\Ppi::compliance($conform, count($rows));

        $audit = null;
        DB::transaction(function () use ($validated, $rows, $category, $auditorId, $conform, $nonConform, $compliance, &$audit) {
            $audit = Audit::create([
                'audit_number' => $this->nextAuditNumber(),
                'category_id' => $category->id,
                'unit_id' => $validated['unit_id'],
                'auditor_id' => $auditorId,
                'audit_date' => $validated['audit_date'],
                'shift' => $validated['shift'],
                'officer_name' => $validated['officer_name'] ?? null,
                'profession_id' => $validated['profession_id'] ?? null,
                'total_items' => count($rows),
                'conform_items' => $conform,
                'nonconform_items' => $nonConform,
                'na_items' => 0,
                'compliance_percentage' => $compliance,
                'grade' => Setting::grade($compliance),
                'status' => 'final',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($rows as $seq => $row) {
                HandHygieneObservation::create([
                    'audit_id' => $audit->id,
                    'sequence' => $seq,
                    'moment' => $row['moment'],
                    'action' => $row['action'],
                ]);
            }
        });

        return redirect()->route('audits.show', $audit)
            ->with('success', 'Audit Cuci Tangan berhasil disimpan. Kepatuhan: ' . $compliance . '% (' . $conform . ' patuh dari ' . count($rows) . ' observasi).');
    }

    /** Nomor audit berurutan per tahun: PPI-YYYY-0001. */
    private function nextAuditNumber(): string
    {
        $year = now()->year;
        $seq = (int) Audit::where('audit_number', 'like', "PPI-{$year}-%")->count() + 1;
        do {
            $number = sprintf('PPI-%d-%04d', $year, $seq);
            $seq++;
        } while (Audit::where('audit_number', $number)->exists());

        return $number;
    }

    public function show(Request $request, Audit $audit): View
    {
        $this->authorize('view-audit', $audit);

        $audit->load(['unit', 'category', 'auditor', 'profession', 'apdType', 'wasteType', 'answers.question', 'observations', 'findings.followUps.user', 'findings.verifications.verifier']);

        return view('audits.show', compact('audit'));
    }

    public function pdf(Request $request, Audit $audit)
    {
        $this->authorize('view-audit', $audit);

        $audit->load(['unit', 'category', 'auditor', 'profession', 'apdType', 'wasteType', 'answers.question', 'observations', 'findings']);

        $pdf = Pdf::loadView('audits.pdf', [
            'audit' => $audit,
            'facility' => [
                'name' => Setting::get('facility_name'),
                'address' => Setting::get('facility_address'),
            ],
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($audit->audit_number . '.pdf');
    }

    public function destroy(Request $request, Audit $audit)
    {
        $this->authorize('manage-masters');

        if ($audit->findings()->where('status', '!=', 'closed')->exists()) {
            return back()->withErrors(['audit' => 'Audit tidak dapat dihapus karena masih memiliki temuan yang belum closed.']);
        }

        $audit->delete();

        return redirect()->route('audits.index')->with('success', 'Data audit ' . $audit->audit_number . ' telah dihapus.');
    }
}
