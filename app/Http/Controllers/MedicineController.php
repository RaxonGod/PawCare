<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $stockFilter = $request->query('stock_filter');

        $query = Medicine::with('category');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockFilter === 'low') {
            $query->whereColumn('stock', '<=', 'min_stock');
        } elseif ($stockFilter === 'out') {
            $query->where('stock', '<=', 0);
        }

        $medicines = $query->orderBy('name')->paginate(10)->withQueryString();
        $categories = MedicineCategory::orderBy('name')->get();

        return view('medicines.index', compact('medicines', 'categories', 'search', 'categoryId', 'stockFilter'));
    }

    public function create()
    {
        $categories = MedicineCategory::orderBy('name')->get();
        // Generate automatic code suggestion
        $latest = Medicine::latest('id')->first();
        $nextNumber = $latest ? ($latest->id + 1) : 1;
        $suggestedCode = 'OBT-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('medicines.create', compact('categories', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:medicine_categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:medicines,code'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'category_id.required' => 'Kategori obat wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'code.required' => 'Kode obat wajib diisi.',
            'code.unique' => 'Kode obat sudah digunakan.',
            'name.required' => 'Nama obat wajib diisi.',
            'unit.required' => 'Satuan obat (misal: Botol, Tablet) wajib diisi.',
            'stock.required' => 'Stok awal wajib diisi.',
            'min_stock.required' => 'Stok minimum peringatan wajib diisi.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
        ]);

        Medicine::create($validated);

        return redirect()->route('medicines.index')->with('success', 'Data obat baru berhasil disimpan.');
    }

    public function show(Medicine $medicine)
    {
        $medicine->load('category');
        
        $recentUsage = $medicine->medicalRecordDetails()
            ->with(['medicalRecord.pet', 'medicalRecord.vet'])
            ->latest()
            ->take(5)
            ->get();

        $recentRestocks = $medicine->restockDetails()
            ->with(['restock.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('medicines.show', compact('medicine', 'recentUsage', 'recentRestocks'));
    }

    public function edit(Medicine $medicine)
    {
        $categories = MedicineCategory::orderBy('name')->get();
        return view('medicines.edit', compact('medicine', 'categories'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:medicine_categories,id'],
            'code' => ['required', 'string', 'max:50', 'unique:medicines,code,' . $medicine->id],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'category_id.required' => 'Kategori obat wajib dipilih.',
            'code.required' => 'Kode obat wajib diisi.',
            'code.unique' => 'Kode obat sudah digunakan.',
            'name.required' => 'Nama obat wajib diisi.',
            'unit.required' => 'Satuan obat wajib diisi.',
            'purchase_price.required' => 'Harga beli wajib diisi.',
            'selling_price.required' => 'Harga jual wajib diisi.',
        ]);

        $medicine->update($validated);

        return redirect()->route('medicines.index')->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Medicine $medicine)
    {
        // Pencegahan hapus jika sudah dipakai di rekam medis (T3) atau restock (T4)
        $hasMedicalUsage = $medicine->medicalRecordDetails()->exists();
        $hasRestock = $medicine->restockDetails()->exists();

        if ($hasMedicalUsage || $hasRestock) {
            $reason = [];
            if ($hasMedicalUsage) $reason[] = 'pencatatan resep rekam medis pasien';
            if ($hasRestock) $reason[] = 'riwayat restock obat';
            $msg = implode(' dan ', $reason);

            return redirect()->route('medicines.index')->with('error', "Obat '{$medicine->name}' ({$medicine->code}) tidak dapat dihapus karena telah terhubung ke {$msg}.");
        }

        $medicine->delete();

        return redirect()->route('medicines.index')->with('success', 'Data obat berhasil dihapus.');
    }
}
