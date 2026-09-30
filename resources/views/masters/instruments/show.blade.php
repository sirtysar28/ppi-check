@extends('layouts.app')

@section('title', 'Instrumen: ' . $category->name)
@section('page_title', 'Instrumen ' . $category->name)

@section('content')
<div class="row g-3">
    <div class="col-12 col-lg-4">
        {{-- Info kategori --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon bg-brand-light text-brand"><i class="bi {{ $category->icon }} fs-4"></i></div>
                    <div>
                        <div class="fw-bold fs-5">{{ $category->name }}</div>
                        <div class="xsmall text-secondary">Kode: {{ $category->code }}</div>
                    </div>
                </div>
                <p class="small text-secondary mb-3">{{ $category->description }}</p>

                <button class="btn btn-sm btn-outline-secondary rounded-pill w-100 mb-3" data-bs-toggle="modal" data-bs-target="#modalEditCategory">
                    <i class="bi bi-pencil me-1"></i>Edit Kategori
                </button>

                <hr>
                <div class="fw-semibold small mb-2"><i class="bi bi-plus-circle me-1 text-brand"></i>Tambah Pertanyaan</div>
                <form method="POST" action="{{ route('masters.instruments.questions.store', $category) }}">
                    @csrf
                    <div class="mb-2">
                        <textarea name="question" rows="2" class="form-control form-control-sm @error('question') is-invalid @enderror" placeholder="Tulis parameter pemeriksaan..." required></textarea>
                        @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <input type="number" name="order" class="form-control form-control-sm" placeholder="Urutan" min="1" step="1">
                        </div>
                        <div class="col-6">
                            <input type="number" name="weight" class="form-control form-control-sm" placeholder="Bobot" min="0.01" step="0.01">
                        </div>
                    </div>
                    <button class="btn btn-brand btn-sm w-100"><i class="bi bi-save me-1"></i>Simpan Pertanyaan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold" style="border-radius:1rem 1rem 0 0">
                <i class="bi bi-list-check me-2 text-brand"></i>Checklist ({{ $category->questions->count() }} pertanyaan)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small">
                                <th class="ps-3 text-center" style="width:56px">Urut</th>
                                <th>Pertanyaan / Parameter</th>
                                <th class="text-center d-none d-md-table-cell">Bobot</th>
                                <th class="text-center">Status</th>
                                <th class="pe-3" style="width:70px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($category->questions as $question)
                                <tr class="{{ $question->is_active ? '' : 'table-secondary text-decoration-line-through' }}">
                                    <td class="ps-3 text-center fw-bold">{{ $question->order }}</td>
                                    <td class="small">{{ $question->question }}</td>
                                    <td class="text-center d-none d-md-table-cell small">{{ $question->weight }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-rounded {{ $question->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $question->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalQ{{ $question->id }}"><i class="bi bi-pencil"></i></button>
                                    </td>
                                </tr>

                                {{-- Modal edit pertanyaan --}}
                                <div class="modal fade" id="modalQ{{ $question->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="border-radius:1.1rem">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold">Edit Pertanyaan #{{ $question->order }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('masters.instruments.questions.update', $question) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Pertanyaan</label>
                                                        <textarea name="question" rows="3" class="form-control" required>{{ $question->question }}</textarea>
                                                    </div>
                                                    <div class="row g-2 mb-3">
                                                        <div class="col-6">
                                                            <label class="form-label small fw-semibold">Urutan</label>
                                                            <input type="number" name="order" class="form-control" value="{{ $question->order }}" min="1">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label small fw-semibold">Bobot</label>
                                                            <input type="number" name="weight" class="form-control" value="{{ $question->weight }}" min="0.01" step="0.01">
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" id="qActive{{ $question->id }}" {{ $question->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="qActive{{ $question->id }}">Aktif (tampil di checklist audit)</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer py-2">
                                                    @if(!$question->answers()->exists())
                                                        <button type="button" class="btn btn-outline-danger me-auto" data-bs-dismiss="modal"
                                                                onclick="if(confirm('Hapus pertanyaan ini?')) document.getElementById('deleteQ{{ $question->id }}').submit()">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-brand">Simpan</button>
                                                </div>
                                            </form>
                                            @if(!$question->answers()->exists())
                                                <form id="deleteQ{{ $question->id }}" method="POST" action="{{ route('masters.instruments.questions.destroy', $question) }}">@csrf @method('DELETE')</form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr><td colspan="5" class="text-center text-secondary py-5">Belum ada pertanyaan. Tambahkan di form sebelah kiri.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal edit kategori --}}
<div class="modal fade" id="modalEditCategory" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:1.1rem">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Edit Kategori</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('masters.instruments.update', $category) }}">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Kode</label>
                        <input type="text" name="code" class="form-control" value="{{ $category->code }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Ikon Bootstrap</label>
                        <input type="text" name="icon" class="form-control" value="{{ $category->icon }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <textarea name="description" rows="2" class="form-control">{{ $category->description }}</textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="catActive" {{ $category->is_active ? 'checked' : '' }}>
                        <label class="form-check-label small" for="catActive">Aktif</label>
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
