<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'facility_name' => ['required', 'string', 'max:150'],
            'facility_address' => ['nullable', 'string', 'max:300'],
            'threshold_sangat_baik' => ['required', 'numeric', 'min:0', 'max:100'],
            'threshold_baik' => ['required', 'numeric', 'min:0', 'max:100'],
            'threshold_cukup' => ['required', 'numeric', 'min:0', 'max:100'],
            'follow_up_deadline_days' => ['required', 'integer', 'min:1', 'max:90'],
        ], [
            'facility_name.required' => 'Nama fasilitas wajib diisi.',
            'threshold_sangat_baik.required' => 'Ambang batas Sangat Baik wajib diisi.',
        ]);

        if (! ($validated['threshold_sangat_baik'] >= $validated['threshold_baik'] && $validated['threshold_baik'] >= $validated['threshold_cukup'])) {
            return back()->withErrors(['threshold_baik' => 'Urutan ambang batas harus: Sangat Baik ≥ Baik ≥ Cukup.']);
        }

        foreach ($validated as $key => $value) {
            Setting::set($key, (string) $value);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
