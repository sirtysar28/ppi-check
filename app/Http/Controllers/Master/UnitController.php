<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $units = Unit::when($request->input('q'), fn ($q, $v) => $q->where(fn ($w) => $w->where('name', 'like', "%{$v}%")->orWhere('code', 'like', "%{$v}%")))
            ->withCount('users')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('masters.units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:units,code'],
            'name' => ['required', 'string', 'max:150'],
            'head_name' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], ['code.unique' => 'Kode unit sudah digunakan.']);

        Unit::create($validated);

        return back()->with('success', 'Unit berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:units,code,' . $unit->id],
            'name' => ['required', 'string', 'max:150'],
            'head_name' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $unit->update($validated);

        return back()->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->audits()->exists() || $unit->users()->exists()) {
            return back()->withErrors(['unit' => 'Unit tidak dapat dihapus karena masih memiliki data audit / user. Nonaktifkan saja.']);
        }

        $unit->delete();

        return back()->with('success', 'Unit berhasil dihapus.');
    }
}
