<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AuditCategory;
use App\Models\AuditQuestion;
use Illuminate\Http\Request;

/**
 * Master Instrumen Audit: kategori + daftar pertanyaan (checklist),
 * editable admin tanpa ubah kode.
 */
class InstrumentController extends Controller
{
    public function index()
    {
        $categories = AuditCategory::withCount('questions')->orderBy('name')->get();

        return view('masters.instruments.index', compact('categories'));
    }

    public function show(AuditCategory $category)
    {
        $category->load('questions');

        return view('masters.instruments.show', compact('category'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:40', 'unique:audit_categories,code', 'alpha_dash'],
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'code.unique' => 'Kode kategori sudah digunakan.',
            'code.alpha_dash' => 'Kode hanya boleh huruf, angka, tanda hubung, dan underscore.',
        ]);

        AuditCategory::create($validated);

        return back()->with('success', 'Kategori instrumen berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, AuditCategory $category)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:40', 'unique:audit_categories,code,' . $category->id, 'alpha_dash'],
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return back()->with('success', 'Kategori instrumen berhasil diperbarui.');
    }

    public function storeQuestion(Request $request, AuditCategory $category)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'weight' => ['nullable', 'numeric', 'min:0.01', 'max:99'],
            'order' => ['nullable', 'integer', 'min:1'],
        ], ['question.required' => 'Pertanyaan wajib diisi.']);

        $validated['order'] = $validated['order'] ?? ($category->questions()->max('order') + 1);

        $category->questions()->create($validated);

        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, AuditQuestion $question)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'weight' => ['nullable', 'numeric', 'min:0.01', 'max:99'],
            'order' => ['nullable', 'integer', 'min:1'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $validated['order'] = $validated['order'] ?? $question->order;

        $question->update($validated);

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroyQuestion(AuditQuestion $question)
    {
        if ($question->answers()->exists()) {
            return back()->withErrors(['question' => 'Pertanyaan sudah memiliki riwayat jawaban. Nonaktifkan saja.']);
        }

        $question->delete();

        return back()->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
