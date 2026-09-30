@extends('layouts.app')

@section('title', 'Master Unit')
@section('page_title', 'Master Unit / Ruangan')

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-plus-circle me-2 text-brand"></i>Tambah Unit
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('masters.units.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="cth: IGD" required maxlength="20" style="text-transform:uppercase">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Unit <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="cth: Instalasi Gawat Darurat" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kepala Unit</label>
                        <input type="text" name="head_name" class="form-control" placeholder="Opsional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Opsional"></textarea>
                    </div>
                    <button class="btn btn-brand w-100"><i class="bi bi-save me-1"></i>Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius:1rem 1rem 0 0">
                <span class="fw-semibold"><i class="bi bi-hospital me-2 text-brand"></i>Daftar Unit ({{ $units->total() }})</span>
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari unit..." value="{{ request('q') }}">
                    <button class="btn btn-sm btn-brand"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3">Kode</th>
                                <th>Nama Unit</th>
                                <th class="d-none d-md-table-cell">Kepala Unit</th>
                                <th class="text-center">User</th>
                                <th class="text-center">Status</th>
                                <th class="pe-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $unit)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $unit->code }}</td>
                                    <td>
                                        <a href="{{ route('dashboard.unit', $unit) }}" class="text-decoration-none fw-semibold">{{ $unit->name }}</a>
                                    </td>
                                    <td class="d-none d-md-table-cell small">{{ $unit->head_name ?? '-' }}</td>
                                    <td class="text-center small">{{ $unit->users_count }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-rounded {{ $unit->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalUnit{{ $unit->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal edit --}}
                                <div class="modal fade" id="modalUnit{{ $unit->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="border-radius:1.1rem">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit: {{ $unit->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('masters.units.update', $unit) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-5">
                                                            <label class="form-label small fw-semibold">Kode</label>
                                                            <input type="text" name="code" class="form-control" value="{{ $unit->code }}" required style="text-transform:uppercase">
                                                        </div>
                                                        <div class="col-7">
                                                            <label class="form-label small fw-semibold">Nama</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $unit->name }}" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-semibold">Kepala Unit</label>
                                                            <input type="text" name="head_name" class="form-control" value="{{ $unit->head_name }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-semibold">Deskripsi</label>
                                                            <textarea name="description" rows="2" class="form-control">{{ $unit->description }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="is_active" id="active{{ $unit->id }}" {{ $unit->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label small" for="active{{ $unit->id }}">Unit aktif</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-danger me-auto" data-bs-dismiss="modal"
                                                            onclick="if(confirm('Hapus unit {{ $unit->name }}?')) document.getElementById('deleteUnit{{ $unit->id }}').submit()">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-brand">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                            @unless($unit->users_count > 0 || $unit->audits()->exists())
                                                <form id="deleteUnit{{ $unit->id }}" method="POST" action="{{ route('masters.units.destroy', $unit) }}">@csrf @method('DELETE')</form>
                                            @endunless
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="6" class="text-center text-secondary py-5">Belum ada unit.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($units->hasPages())
                <div class="card-footer bg-white py-2" style="border-radius:0 0 1rem 1rem">
                    <div class="d-flex justify-content-center">{{ $units->links('pagination::bootstrap-5') }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
