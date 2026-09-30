@extends('layouts.app')

@section('title', 'Master Jenis APD')
@section('page_title', 'Master Jenis APD')

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-plus-circle me-2 text-brand"></i>Tambah Jenis APD
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('masters.apd-types.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama APD <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="cth: Masker N95" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-shield-check me-2 text-brand"></i>Daftar Jenis APD ({{ $apdTypes->total() }})
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3" style="width:52px">#</th>
                                <th>Nama APD</th>
                                <th class="d-none d-md-table-cell">Deskripsi</th>
                                <th class="text-center">Status</th>
                                <th class="pe-3" style="width:90px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apdTypes as $apd)
                                <tr>
                                    <td class="ps-3 text-secondary">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $apd->name }}</td>
                                    <td class="d-none d-md-table-cell small text-secondary">{{ $apd->description ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-rounded {{ $apd->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $apd->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalApd{{ $apd->id }}"><i class="bi bi-pencil"></i></button>
                                            @if(!$apd->audits()->exists())
                                                <form method="POST" action="{{ route('masters.apd-types.destroy', $apd) }}" onsubmit="return confirm('Hapus jenis APD ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalApd{{ $apd->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="border-radius:1.1rem">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Edit: {{ $apd->name }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('masters.apd-types.update', $apd) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Nama APD</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $apd->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Deskripsi</label>
                                                        <textarea name="description" rows="2" class="form-control">{{ $apd->description }}</textarea>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" id="apdActive{{ $apd->id }}" {{ $apd->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="apdActive{{ $apd->id }}">Aktif</label>
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
                                <tr><td colspan="5" class="text-center text-secondary py-5">Belum ada jenis APD.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
