<?php

namespace App\Http\Controllers;

use App\Models\Finding;
use App\Models\FollowUp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    /**
     * Halaman Tindak Lanjut (utamanya untuk role Unit/Petugas).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $findings = Finding::query()->with(['unit', 'category', 'followUps'])
            ->whereIn('status', ['open', 'progress'])
            ->when($user->role === User::ROLE_UNIT, fn ($q) => $q->where('unit_id', $user->unit_id))
            ->when($request->input('unit_id'), fn ($q, $v) => $q->where('unit_id', $v))
            ->when($request->input('status'), fn ($q, $v) => $q->where('status', $v))
            ->orderByRaw("CASE severity WHEN 'kritis' THEN 1 WHEN 'mayor' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->paginate(15)
            ->withQueryString();

        return view('followups.index', compact('findings'));
    }

    public function store(Request $request, Finding $finding)
    {
        $user = $request->user();

        // Role unit hanya boleh menindaklanjuti temuan unitnya sendiri
        abort_unless(
            $user->hasRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI, User::ROLE_AUDITOR]) || $finding->unit_id === $user->unit_id,
            403
        );

        $validated = $request->validate([
            'action' => ['required', 'string', 'max:2000'],
            'follow_up_date' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ], [
            'action.required' => 'Uraian tindakan wajib diisi.',
            'follow_up_date.required' => 'Tanggal perbaikan wajib diisi.',
            'photo.image' => 'Bukti harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        if (in_array($finding->status, ['progress']) && $finding->followUps()->where('status', 'submitted')->exists()) {
            return back()->withErrors(['finding' => 'Tindak lanjut sedang menunggu verifikasi auditor.']);
        }

        FollowUp::create([
            'finding_id' => $finding->id,
            'user_id' => $user->id,
            'action' => $validated['action'],
            'follow_up_date' => $validated['follow_up_date'],
            'photo_path' => $request->hasFile('photo') ? $request->file('photo')->store('followups', 'public') : null,
            'status' => 'submitted',
        ]);

        // Status temuan: open -> progress (menunggu verifikasi)
        $finding->update(['status' => 'progress']);

        return redirect()->route('findings.show', $finding)
            ->with('success', 'Tindak lanjut berhasil dikirim dan menunggu verifikasi auditor.');
    }
}
