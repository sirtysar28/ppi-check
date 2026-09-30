<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use App\Models\Profession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with(['unit', 'profession'])
            ->when($request->input('q'), fn ($q, $v) => $q->where(fn ($w) => $w->where('name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")))
            ->when($request->input('role'), fn ($q, $v) => $q->where('role', $v))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $units = Unit::where('is_active', true)->orderBy('name')->get();
        $professions = Profession::where('is_active', true)->get();

        return view('masters.users.index', compact('users', 'units', 'professions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:super_admin,admin_ppi,auditor,unit'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'profession_id' => ['nullable', 'exists:professions,id'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:super_admin,admin_ppi,auditor,unit'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'profession_id' => ['nullable', 'exists:professions,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Cegah menonaktifkan diri sendiri
        $validated['is_active'] = $user->id === $request->user()->id ? true : $request->boolean('is_active');

        $user->update($validated);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Anda tidak dapat menghapus akun sendiri.']);
        }
        if ($user->audits()->exists()) {
            return back()->withErrors(['user' => 'User memiliki riwayat audit. Nonaktifkan saja akunnya.']);
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
