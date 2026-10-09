<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Buku Manual / Panduan Pengguna (HTML).
 * Isi manual otomatis menyesuaikan peran (role) pengguna yang login:
 * - Super Admin & Admin PPI -> manual lengkap seluruh fitur
 * - Auditor                -> manual khusus auditor
 * - Unit / Petugas         -> manual khusus unit
 */
class ManualBookController extends Controller
{
    public function __invoke(Request $request): View
    {
        $manual = match ($request->user()->role) {
            User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN_PPI => 'admin',
            User::ROLE_AUDITOR => 'auditor',
            default => 'unit',
        };

        return view('manual.book', [
            'manual' => $manual,
            'roleLabel' => $request->user()->role_label,
            'facilityName' => \App\Models\Setting::get('facility_name', config('app.name')),
        ]);
    }
}
