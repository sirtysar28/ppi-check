@extends('layouts.app')

@section('title', 'Master Jenis Limbah')
@section('page_title', 'Master Jenis Limbah')

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-plus-circle me-2 text-brand"></i>Tambah Jenis Limbah
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('masters.waste-types.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama Limbah <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="cth: Limbah Radioaktif" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode Warna</label>
                        <input type="text" name="color_code" class="form-control" placeholder="cth: Ungu">
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
                <i class="bi bi-trash3 me-2 text-brand"></i>Daftar Jenis Limbah ({{ $wasteTypes->total() }})
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3" style="width:52px">#</th>
                                <th>Nama Limbah</th>
                                <th class="d-none d-md-table-cell">Kode Warna</th>
                                <th class="text-center">Status</th>
                                <th class="pe-3" style="width:90px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wasteTypes as $waste)
                                <tr>
                                    <td class="ps-3 text-secondary">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $waste->name }}</td>
                                    <td class="d-none d-md-table-cell"><span class="badge badge-rounded bg-dark">{{ $waste->color_code ?? '-' }}</span></td>
                                    <td class="text-center">
                                        <span class="badge badge-rounded {{ $waste->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $waste->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalWaste{{ $waste->id }}"><i class="bi bi-pencil"></i></button>
                                            @if(!$waste->audits()->exists())
                                                <form method="POST" action="{{ route('masters.waste-types.destroy', $waste) }}" onsubmit="return confirm('Hapus jenis limbah ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalWaste{{ $waste->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="border-radius:1.1rem">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Edit: {{ $waste->name }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('masters.waste-types.update', $waste) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Nama Limbah</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $waste->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Kode Warna</label>
                                                        <input type="text" name="color_code" class="form-control" value="{{ $waste->color_code }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Deskripsi</label>
                                                        <textarea name="description" rows="2" class="form-control">{{ $waste->description }}</textarea>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" id="wasteActive{{ $waste->id }}" {{ $waste->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="wasteActive{{ $waste->id }}">Aktif</label>
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
                                <tr><td colspan="5" class="text-center text-secondary py-5">Belum ada jenis limbah.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
