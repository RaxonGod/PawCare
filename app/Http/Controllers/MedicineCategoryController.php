<?php

namespace App\Http\Controllers;

use App\Models\MedicineCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MedicineCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = MedicineCategory::withCount('medicines');

        if ($search) {
            $query->where('name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
        }

        $categories = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('medicine_categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('medicine_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:medicine_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama kategori obat wajib diisi.',
            'name.unique' => 'Nama kategori obat sudah ada.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        MedicineCategory::create($validated);

        return redirect()->route('medicine-categories.index')->with('success', 'Kategori obat baru berhasil ditambahkan.');
    }

    public function show(MedicineCategory $medicineCategory)
    {
        $medicineCategory->load(['medicines' => function ($q) {
            $q->orderBy('name');
        }]);

        return view('medicine_categories.show', compact('medicineCategory'));
    }

    public function edit(MedicineCategory $medicineCategory)
    {
        return view('medicine_categories.edit', compact('medicineCategory'));
    }

    public function update(Request $request, MedicineCategory $medicineCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:medicine_categories,name,' . $medicineCategory->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama kategori obat wajib diisi.',
            'name.unique' => 'Nama kategori obat sudah terdaftar.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $medicineCategory->update($validated);

        return redirect()->route('medicine-categories.index')->with('success', 'Kategori obat berhasil diperbarui.');
    }

    public function destroy(MedicineCategory $medicineCategory)
    {
        // Pencegahan hapus jika sudah dipakai data obat (M1)
        if ($medicineCategory->medicines()->exists()) {
            $count = $medicineCategory->medicines()->count();
            return redirect()->route('medicine-categories.index')->with('error', "Kategori '{$medicineCategory->name}' tidak dapat dihapus karena masih memuat {$count} data obat terkait.");
        }

        $medicineCategory->delete();

        return redirect()->route('medicine-categories.index')->with('success', 'Kategori obat berhasil dihapus.');
    }
}
