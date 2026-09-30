@extends('layouts.app')

@section('title', 'Master Profesi')
@section('page_title', 'Master Profesi')

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-plus-circle me-2 text-brand"></i>Tambah Profesi
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('masters.professions.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Profesi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="cth: Perawat" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-brand w-100"><i class="bi bi-save me-1"></i>Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-person-workspace me-2 text-brand"></i>Daftar Profesi ({{ $professions->total() }})
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3" style="width:52px">#</th>
                                <th>Nama Profesi</th>
                                <th class="text-center">User</th>
                                <th class="text-center">Status</th>
                                <th class="pe-3" style="width:90px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($professions as $profession)
                                <tr>
                                    <td class="ps-3 text-secondary">{{ $loop->iteration + ($professions->currentPage() - 1) * $professions->perPage() }}</td>
                                    <td class="fw-semibold">{{ $profession->name }}</td>
                                    <td class="text-center small">{{ $profession->users_count }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-rounded {{ $profession->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $profession->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalProf{{ $profession->id }}" title="Edit"><i class="bi bi-pencil"></i></button>
                                            @if($profession->users_count === 0)
                                                <form method="POST" action="{{ route('masters.professions.destroy', $profession) }}" onsubmit="return confirm('Hapus profesi ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalProf{{ $profession->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content" style="border-radius:1.1rem">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Edit Profesi</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('masters.professions.update', $profession) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <input type="text" name="name" class="form-control" value="{{ $profession->name }}" required>
                                                    <div class="form-check form-switch mt-3">
                                                        <input class="form-check-input" type="checkbox" name="is_active" id="profActive{{ $profession->id }}" {{ $profession->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="profActive{{ $profession->id }}">Aktif</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-2">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-brand">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="5" class="text-center text-secondary py-5">Belum ada profesi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
