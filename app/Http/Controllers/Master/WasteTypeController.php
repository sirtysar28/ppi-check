<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\WasteType;
use Illuminate\Http\Request;

class WasteTypeController extends Controller
{
    public function index()
    {
        $wasteTypes = WasteType::orderBy('name')->paginate(20);

        return view('masters.waste-types.index', compact('wasteTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:waste_types,name'],
            'color_code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
        ], ['name.unique' => 'Jenis limbah sudah ada.']);

        WasteType::create($validated);

        return back()->with('success', 'Jenis limbah berhasil ditambahkan.');
    }

    public function update(Request $request, WasteType $wasteType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:waste_types,name,' . $wasteType->id],
            'color_code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $wasteType->update($validated);

        return back()->with('success', 'Jenis limbah berhasil diperbarui.');
    }

    public function destroy(WasteType $wasteType)
    {
        if ($wasteType->audits()->exists()) {
            return back()->withErrors(['waste' => 'Jenis limbah masih dipakai pada data audit.']);
        }

        $wasteType->delete();

        return back()->with('success', 'Jenis limbah berhasil dihapus.');
    }
}
