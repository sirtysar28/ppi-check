<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ApdType;
use Illuminate\Http\Request;

class ApdTypeController extends Controller
{
    public function index()
    {
        $apdTypes = ApdType::orderBy('name')->paginate(20);

        return view('masters.apd-types.index', compact('apdTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:apd_types,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ], ['name.unique' => 'Jenis APD sudah ada.']);

        ApdType::create($validated);

        return back()->with('success', 'Jenis APD berhasil ditambahkan.');
    }

    public function update(Request $request, ApdType $apdType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:apd_types,name,' . $apdType->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $apdType->update($validated);

        return back()->with('success', 'Jenis APD berhasil diperbarui.');
    }

    public function destroy(ApdType $apdType)
    {
        if ($apdType->audits()->exists()) {
            return back()->withErrors(['apd' => 'Jenis APD masih dipakai pada data audit.']);
        }

        $apdType->delete();

        return back()->with('success', 'Jenis APD berhasil dihapus.');
    }
}
