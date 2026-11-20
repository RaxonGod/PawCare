<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineRestock;
use App\Models\MedicineRestockDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicineRestockController extends Controller
{
    // T4: Daftar Riwayat Restock Obat
    public function index(Request $request)
    {
        $search = $request->query('search');
        $date = $request->query('date');

        $query = MedicineRestock::with(['user', 'details.medicine']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('restock_code', 'ilike', "%{$search}%")
                  ->orWhere('supplier_name', 'ilike', "%{$search}%")
                  ->orWhere('invoice_number', 'ilike', "%{$search}%");
            });
        }

        if ($date) {
            $query->whereDate('restock_date', $date);
        }

        $restocks = $query->orderByDesc('restock_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('medicine_restocks.index', compact('restocks', 'search', 'date'));
    }

    // T4: Form Restock Obat (Role 1)
    public function create()
    {
        $medicines = Medicine::with('category')->orderBy('name')->get();
        return view('medicine_restocks.create', compact('medicines'));
    }

    // T4: Simpan Restock Obat (Header-Detail & Menambah Stok M1 dalam DB::transaction)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'restock_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_id' => ['required', 'exists:medicines,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.purchase_price' => ['required', 'numeric', 'min:0'],
            'items.*.batch_number' => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date' => ['nullable', 'date', 'after:today'],
        ], [
            'supplier_name.required' => 'Nama pemasok / distributor wajib diisi.',
            'restock_date.required' => 'Tanggal pengadaan restock wajib diisi.',
            'items.required' => 'Minimal 1 item obat wajib dimasukkan dalam restock.',
            'items.*.medicine_id.required' => 'Pilihan obat wajib dipilih.',
            'items.*.quantity.min' => 'Jumlah kuantitas obat minimal 1.',
            'items.*.purchase_price.required' => 'Harga beli per unit wajib diisi.',
            'items.*.expiry_date.after' => 'Tanggal kedaluwarsa harus setelah hari ini.',
        ]);

        $restock = DB::transaction(function () use ($validated) {
            $yearMonth = Carbon::now()->format('Ym');
            $count = MedicineRestock::where('restock_code', 'like', "RST-{$yearMonth}-%")->count();
            $code = 'RST-' . $yearMonth . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            $totalCost = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = (int)$item['quantity'] * (float)$item['purchase_price'];
                $totalCost += $subtotal;
            }

            // 1. Simpan Header Restock (Pelaku T4: Auth::id())
            $medicineRestock = MedicineRestock::create([
                'restock_code' => $code,
                'user_id' => Auth::id(), // Catat pelaku T4 (Role 1)
                'supplier_name' => $validated['supplier_name'],
                'invoice_number' => $validated['invoice_number'] ?? null,
                'restock_date' => $validated['restock_date'],
                'total_cost' => $totalCost,
                'notes' => $validated['notes'] ?? null,
            ]);

            // 2. Simpan Detail Items & Tambah Stok Obat M1
            foreach ($validated['items'] as $item) {
                $qty = (int) $item['quantity'];
                $price = (float) $item['purchase_price'];
                $subtotal = $qty * $price;

                MedicineRestockDetail::create([
                    'medicine_restock_id' => $medicineRestock->id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => $qty,
                    'purchase_price' => $price,
                    'subtotal' => $subtotal,
                    'batch_number' => $item['batch_number'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);

                // Menambah stok obat M1 dan mengupdate harga beli terbaru
                $medicine = Medicine::findOrFail($item['medicine_id']);
                $medicine->increment('stock', $qty);
                $medicine->update(['purchase_price' => $price]);
            }

            return $medicineRestock;
        });

        return redirect()->route('medicine-restocks.show', $restock)
            ->with('success', "Restock obat {$restock->restock_code} berhasil disimpan! Stok obat di gudang klinik telah bertambah.");
    }

    public function show(MedicineRestock $medicineRestock)
    {
        $medicineRestock->load(['user', 'details.medicine.category']);
        return view('medicine_restocks.show', compact('medicineRestock'));
    }
}
