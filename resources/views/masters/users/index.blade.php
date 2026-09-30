@extends('layouts.app')

@section('title', 'Kelola User')
@section('page_title', 'Kelola User')

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-person-plus me-2 text-brand"></i>Tambah User
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('masters.users.store') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="newPass" class="form-control @error('password') is-invalid @enderror" minlength="8" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('newPass', this)"><i class="bi bi-eye"></i></button>
                        </div>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="admin_ppi">Admin PPI</option>
                            <option value="auditor">Auditor</option>
                            <option value="unit">Unit / Petugas</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Unit (untuk role Unit)</label>
                        <select name="unit_id" class="form-select">
                            <option value="">-- Tidak ada --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Profesi</label>
                        <select name="profession_id" class="form-select">
                            <option value="">-- Tidak ada --</option>
                            @foreach($professions as $prof)
                                <option value="{{ $prof->id }}">{{ $prof->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-brand w-100"><i class="bi bi-save me-1"></i>Simpan User</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2" style="border-radius:1rem 1rem 0 0">
                <span class="fw-semibold"><i class="bi bi-people me-2 text-brand"></i>Daftar User ({{ $users->total() }})</span>
                <form method="GET" class="d-flex gap-2">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Semua Role</option>
                        @foreach(\App\Models\User::ROLES as $key => $label)
                            <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari nama/email" value="{{ request('q') }}">
                    <button class="btn btn-sm btn-brand"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3">Nama</th>
                                <th class="d-none d-md-table-cell">Email</th>
                                <th>Role</th>
                                <th class="d-none d-lg-table-cell">Unit</th>
                                <th class="text-center">Status</th>
                                <th class="pe-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $userRow)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar" style="width:32px;height:32px;font-size:.8rem">{{ strtoupper(substr($userRow->name, 0, 1)) }}</div>
                                            <span class="fw-semibold">{{ $userRow->name }}</span>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell small text-secondary">{{ $userRow->email }}</td>
                                    <td><span class="badge badge-rounded bg-brand-light text-brand">{{ $userRow->role_label }}</span></td>
                                    <td class="d-none d-lg-table-cell small">{{ $userRow->unit?->name ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-rounded {{ $userRow->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $userRow->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalUser{{ $userRow->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                    </td>
                                </tr>

                                {{-- Modal edit user --}}
                                <div class="modal fade" id="modalUser{{ $userRow->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content" style="border-radius:1.1rem">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Edit User: {{ $userRow->name }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('masters.users.update', $userRow) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-12 col-md-6">
                                                            <label class="form-label small fw-semibold">Nama</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $userRow->name }}" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label class="form-label small fw-semibold">Email</label>
                                                            <input type="email" name="email" class="form-control" value="{{ $userRow->email }}" required>
                                                        </div>
                                                        <div class="col-6 col-md-4">
                                                            <label class="form-label small fw-semibold">Role</label>
                                                            <select name="role" class="form-select">
                                                                @foreach(\App\Models\User::ROLES as $key => $label)
                                                                    <option value="{{ $key }}" {{ $userRow->role === $key ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-4">
                                                            <label class="form-label small fw-semibold">Unit</label>
                                                            <select name="unit_id" class="form-select">
                                                                <option value="">-- Tidak ada --</option>
                                                                @foreach($units as $unit)
                                                                    <option value="{{ $unit->id }}" {{ $userRow->unit_id === $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-4">
                                                            <label class="form-label small fw-semibold">Profesi</label>
                                                            <select name="profession_id" class="form-select">
                                                                <option value="">-- Tidak ada --</option>
                                                                @foreach($professions as $prof)
                                                                    <option value="{{ $prof->id }}" {{ $userRow->profession_id === $prof->id ? 'selected' : '' }}>{{ $prof->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-6 col-md-4">
                                                            <label class="form-label small fw-semibold">No. HP</label>
                                                            <input type="text" name="phone" class="form-control" value="{{ $userRow->phone }}">
                                                        </div>
                                                        <div class="col-6 col-md-4">
                                                            <label class="form-label small fw-semibold">Password Baru</label>
                                                            <div class="input-group">
                                                                <input type="password" name="password" id="editPass{{ $userRow->id }}" class="form-control" placeholder="Kosongkan jika tetap" minlength="8">
                                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('editPass{{ $userRow->id }}', this)"><i class="bi bi-eye"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-4 d-flex align-items-end">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="userActive{{ $userRow->id }}" {{ $userRow->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label small" for="userActive{{ $userRow->id }}">Aktif</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-2">
                                                    @if($userRow->id !== auth()->id() && !$userRow->audits()->exists())
                                                        <button type="button" class="btn btn-outline-danger me-auto" data-bs-dismiss="modal"
                                                                onclick="if(confirm('Hapus user {{ $userRow->name }}?')) document.getElementById('deleteUser{{ $userRow->id }}').submit()">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-brand">Simpan</button>
                                                </div>
                                            </form>
                                            @if($userRow->id !== auth()->id() && !$userRow->audits()->exists())
                                                <form id="deleteUser{{ $userRow->id }}" method="POST" action="{{ route('masters.users.destroy', $userRow) }}">@csrf @method('DELETE')</form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="6" class="text-center text-secondary py-5">Belum ada user.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
                <div class="card-footer bg-white py-2" style="border-radius:0 0 1rem 1rem">
                    <div class="d-flex justify-content-center">{{ $users->links('pagination::bootstrap-5') }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function togglePass(id, btn) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.innerHTML = input.type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    }
</script>
@endsection
