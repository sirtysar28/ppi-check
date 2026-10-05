<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ApdAction;
use Illuminate\Http\Request;

class ApdActionController extends Controller
{
    public function index()
    {
        $actions = ApdAction::orderBy('order')->orderBy('name')->paginate(30);

        return view('masters.apd-actions.index', compact('actions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:apd_actions,name'],
        ], [
            'name.unique' => 'Tindakan sudah ada.',
        ]);

        ApdAction::create($validated + ['order' => ApdAction::max('order') + 1, 'is_active' => true]);

        return back()->with('success', 'Tindakan APD berhasil ditambahkan.');
    }

    public function update(Request $request, ApdAction $apdAction)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:apd_actions,name,' . $apdAction->id],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $apdAction->update($validated);

        return back()->with('success', 'Tindakan APD berhasil diperbarui.');
    }

    public function destroy(ApdAction $apdAction)
    {
        $apdAction->delete();

        return back()->with('success', 'Tindakan APD berhasil dihapus.');
    }
}
