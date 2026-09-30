@extends('layouts.app')

@section('title', 'Instrumen Audit')
@section('page_title', 'Master Instrumen Audit')

@section('content')
<div class="alert alert-light border small" style="border-radius:.9rem">
    <i class="bi bi-lightbulb me-1 text-warning"></i>
    Instrumen audit (checklist) bersifat <b>master data</b>. Jika standar PPI berubah, cukup ubah pertanyaan di sini tanpa perlu mengubah kode aplikasi.
</div>

<div class="row g-3">
    @foreach($categories as $category)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="stat-icon bg-brand-light text-brand"><i class="bi {{ $category->icon }} fs-4"></i></div>
                        <div>
                            <div class="fw-bold">{{ $category->name }}</div>
                            <div class="xsmall text-secondary">Kode: {{ $category->code }}</div>
                        </div>
                        <span class="badge badge-rounded bg-brand-light text-brand ms-auto">{{ $category->questions_count }} item</span>
                    </div>
                    <p class="small text-secondary flex-grow-1">{{ $category->description }}</p>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('masters.instruments.show', $category) }}" class="btn btn-brand btn-sm rounded-pill flex-grow-1">
                            <i class="bi bi-pencil-square me-1"></i>Kelola Checklist
                        </a>
                        <span class="badge badge-rounded {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Tambah kategori baru --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card h-100 border-dashed" style="border-style:dashed">
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center py-5">
                <i class="bi bi-plus-circle fs-1 text-brand mb-2"></i>
                <div class="fw-bold mb-3">Tambah Kategori Instrumen</div>
                <button class="btn btn-outline-brand btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddCategory" style="border-color:var(--ppi-teal);color:var(--ppi-teal)">
                    Kategori Baru
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal tambah kategori --}}
<div class="modal fade" id="modalAddCategory" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:1.1rem">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Kategori Instrumen</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('masters.instruments.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="cth: sterilisasi" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="cth: Audit Sterilisasi" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Ikon Bootstrap</label>
                        <input type="text" name="icon" class="form-control" placeholder="cth: bi-clipboard2-pulse" value="bi-clipboard2-pulse">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea name="description" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
