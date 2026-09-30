<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    public function index()
    {
        $professions = Profession::withCount('users')->orderBy('name')->paginate(20);

        return view('masters.professions.index', compact('professions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:professions,name']], [
            'name.unique' => 'Profesi sudah ada.',
        ]);

        Profession::create($validated);

        return back()->with('success', 'Profesi berhasil ditambahkan.');
    }

    public function update(Request $request, Profession $profession)
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:professions,name,' . $profession->id]]);
        $validated['is_active'] = $request->boolean('is_active');

        $profession->update($validated);

        return back()->with('success', 'Profesi berhasil diperbarui.');
    }

    public function destroy(Profession $profession)
    {
        if ($profession->users()->exists()) {
            return back()->withErrors(['profession' => 'Profesi masih dipakai user.']);
        }

        $profession->delete();

        return back()->with('success', 'Profesi berhasil dihapus.');
    }
}
