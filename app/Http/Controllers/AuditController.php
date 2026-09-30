<?php

namespace App\Http\Controllers;

use App\Models\ApdType;
use App\Models\Audit;
use App\Models\AuditAnswer;
use App\Models\AuditCategory;
use App\Models\Finding;
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
        $categoryCode = $request->input('category', 'cuci-tangan');
        $category = AuditCategory::where('code', $categoryCode)->where('is_active', true)->firstOrFail();

        $categories = AuditCategory::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $auditors = User::where('role', 'auditor')->where('is_active', true)->orderBy('name')->get();
        $professions = Profession::where('is_active', true)->get();
        $apdTypes = ApdType::where('is_active', true)->get();
        $wasteTypes = WasteType::where('is_active', true)->get();

        return view('audits.create', compact(
            'category', 'categories', 'units', 'auditors', 'professions', 'apdTypes', 'wasteTypes'
        ));
    }

    public function store(Request $request)
    {
        $user = $request->user();

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

        // Hanya admin/auditor yang boleh memilih auditor lain; auditor default dirinya
        $auditorId = $validated['auditor_id'];
        if ($user->role === User::ROLE_AUDITOR) {
            $auditorId = $user->id;
        }

        $category = AuditCategory::findOrFail($validated['category_id']);
        $questions = $category->activeQuestions;

        // Pastikan semua pertanyaan terjawab
        foreach ($questions as $q) {
            if (! isset($validated['answers'][$q->id])) {
                return back()->withErrors(['answers' => 'Item #' . $q->order . ' belum dijawab.'])->withInput();
            }
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

    public function show(Request $request, Audit $audit): View
    {
        $this->authorize('view-audit', $audit);

        $audit->load(['unit', 'category', 'auditor', 'profession', 'apdType', 'wasteType', 'answers.question', 'findings.followUps.user', 'findings.verifications.verifier']);

        return view('audits.show', compact('audit'));
    }

    public function pdf(Request $request, Audit $audit)
    {
        $this->authorize('view-audit', $audit);

        $audit->load(['unit', 'category', 'auditor', 'profession', 'apdType', 'wasteType', 'answers.question', 'findings']);

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
