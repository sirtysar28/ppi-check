<?php

namespace App\Http\Controllers;

use App\Models\Finding;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Auditor memverifikasi tindak lanjut: DITERIMA (closed) / DITOLAK (kembali open).
     */
    public function store(Request $request, Finding $finding)
    {
        $this->authorize('verify-followup');

        $validated = $request->validate([
            'result' => ['required', 'in:accepted,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'verification_date' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ], [
            'result.required' => 'Pilih hasil verifikasi (terima / tolak).',
            'verification_date.required' => 'Tanggal verifikasi wajib diisi.',
        ]);

        if ($finding->status !== 'progress') {
            return back()->withErrors(['finding' => 'Temuan ini belum memiliki tindak lanjut yang menunggu verifikasi.']);
        }

        $latestFollowUp = $finding->followUps()->where('status', 'submitted')->latest()->first();
        if (! $latestFollowUp) {
            return back()->withErrors(['finding' => 'Tindak lanjut tidak ditemukan.']);
        }

        Verification::create([
            'finding_id' => $finding->id,
            'verifier_id' => $request->user()->id,
            'follow_up_id' => $latestFollowUp->id,
            'verification_date' => $validated['verification_date'],
            'result' => $validated['result'],
            'notes' => $validated['notes'] ?? null,
            'photo_path' => $request->hasFile('photo') ? $request->file('photo')->store('verifications', 'public') : null,
        ]);

        if ($validated['result'] === 'accepted') {
            $latestFollowUp->update(['status' => 'accepted']);
            $finding->update(['status' => 'closed']);
            $message = 'Verifikasi DITERIMA. Temuan #' . $finding->finding_number . ' telah CLOSED.';
        } else {
            $latestFollowUp->update(['status' => 'rejected']);
            // Ditolak -> kembali ke TINDAK LANJUT (open)
            $finding->update(['status' => 'open']);
            $message = 'Verifikasi DITOLAK. Unit harus melakukan tindak lanjut ulang.';
        }

        return redirect()->route('findings.show', $finding)->with('success', $message);
    }
}
