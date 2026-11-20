@extends('layouts.app')

@section('title', 'Buat Dokumen Restock Obat (T4 Header-Detail)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">TRANSAKSI T4 (HEADER-DETAIL)</span>
                <span class="text-xs text-slate-400">&bull; Menambah Stok Fisik Obat M1 dalam DB::transaction()</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Form Pengadaan & Restock Obat Masuk</h1>
            <p class="text-sm text-slate-500">Catat penerimaan obat dari distributor untuk menambah ketersediaan stok klinik.</p>
        </div>
        <a href="{{ route('medicine-restocks.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('medicine-restocks.store') }}" method="POST" id="restockForm" class="space-y-6">
            @csrf

            <!-- 1. HEADER DATA: Distributor, Faktur, Tanggal -->
            <div class="border-b border-slate-200 pb-5 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                    <i class="fa-solid fa-file-invoice text-amber-600"></i>
                    <span>Bagian 1: Header Dokumen Pengadaan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label for="supplier_name" class="block text-sm font-semibold text-slate-700 mb-1">
                            Nama Distributor / Pemasok <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                            placeholder="Contoh: PT Surya Medika Farma Veteriner">
                    </div>

                    <div>
                        <label for="restock_date" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tanggal Restock <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="restock_date" id="restock_date" value="{{ old('restock_date', date('Y-m-d')) }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="invoice_number" class="block text-sm font-semibold text-slate-700 mb-1">
                            Nomor Faktur / Surat Jalan Supplier
                        </label>
                        <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-mono text-sm focus:ring-2 focus:ring-brand-500"
                            placeholder="Contoh: INV-SUPP-8921">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1">
                            Catatan Pengadaan
                        </label>
                        <input type="text" name="notes" id="notes" value="{{ old('notes') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                            placeholder="Pengadaan rutin bulanan / antisipasi stok habis">
                    </div>
                </div>
            </div>

            <!-- 2. DETAIL DATA: Item Obat yang direstock -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-boxes-stacked text-amber-600"></i>
                            <span>Bagian 2: Detail Item Obat yang Diterima (Detail T4)</span>
                        </h3>
                        <p class="text-xs text-slate-500">Stok obat akan otomatis bertambah sesuai jumlah yang dimasukkan.</p>
                    </div>
                    <button type="button" onclick="addRestockRow()" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 flex items-center space-x-1 shadow-xs">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Item Obat</span>
                    </button>
                </div>

                <!-- Container Baris Item Restock -->
                <div id="restockRowsContainer" class="space-y-3">
                    <div class="restock-row p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Obat (M1) <span class="text-rose-500">*</span></label>
                            <select name="items[0][medicine_id]" required class="item-select w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white focus:ring-2 focus:ring-brand-500">
                                <option value="">-- Pilih Obat --</option>
                                @foreach($medicines as $med)
                                    <option value="{{ $med->id }}" data-purchase="{{ $med->purchase_price }}">
                                        {{ $med->name }} (Stok saat ini: {{ $med->stock }} {{ $med->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Masuk <span class="text-rose-500">*</span></label>
                            <input type="number" name="items[0][quantity]" value="10" min="1" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-center">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Harga Beli/Pcs <span class="text-rose-500">*</span></label>
                            <input type="number" name="items[0][purchase_price]" value="50000" min="0" step="100" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">No. Batch</label>
                            <input type="text" name="items[0][batch_number]" placeholder="BATCH-XXXX" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
                        </div>

                        <div class="sm:col-span-2 flex items-center gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Kedaluwarsa</label>
                                <input type="date" name="items[0][expiry_date]" min="{{ date('Y-m-d') }}" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-xs">
                            </div>
                            <div class="pt-4">
                                <button type="button" onclick="removeRestockRow(this)" class="p-2 text-slate-400 hover:text-rose-600 transition" title="Hapus">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-2 border-t border-slate-100">
                <a href="{{ route('medicine-restocks.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-sm flex items-center space-x-2">
                    <i class="fa-solid fa-boxes-packing"></i>
                    <span>Simpan Restock & Tambahkan Stok Fisik Obat (T4)</span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    let restockIndex = 1;
    const medicinesList = @json($medicines);

    function addRestockRow() {
        const container = document.getElementById('restockRowsContainer');
        const div = document.createElement('div');
        div.className = 'restock-row p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center';

        let opts = '<option value="">-- Pilih Obat --</option>';
        medicinesList.forEach(m => {
            opts += `<option value="${m.id}" data-purchase="${m.purchase_price}">${m.name} (Stok: ${m.stock} ${m.unit})</option>`;
        });

        div.innerHTML = `
            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Obat (M1) <span class="text-rose-500">*</span></label>
                <select name="items[${restockIndex}][medicine_id]" required class="item-select w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white focus:ring-2 focus:ring-brand-500">
                    ${opts}
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Masuk <span class="text-rose-500">*</span></label>
                <input type="number" name="items[${restockIndex}][quantity]" value="10" min="1" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-center">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Harga Beli/Pcs <span class="text-rose-500">*</span></label>
                <input type="number" name="items[${restockIndex}][purchase_price]" value="50000" min="0" step="100" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">No. Batch</label>
                <input type="text" name="items[${restockIndex}][batch_number]" placeholder="BATCH-XXXX" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
            </div>
            <div class="sm:col-span-2 flex items-center gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kedaluwarsa</label>
                    <input type="date" name="items[${restockIndex}][expiry_date]" min="{{ date('Y-m-d') }}" class="w-full px-2 py-2 rounded-lg border border-slate-300 text-xs">
                </div>
                <div class="pt-4">
                    <button type="button" onclick="removeRestockRow(this)" class="p-2 text-slate-400 hover:text-rose-600 transition" title="Hapus">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(div);
        restockIndex++;
    }

    function removeRestockRow(btn) {
        const rows = document.querySelectorAll('.restock-row');
        if (rows.length > 1) {
            btn.closest('.restock-row').remove();
        } else {
            alert('Minimal harus ada 1 item obat dalam dokumen restock.');
        }
    }
</script>
@endpush
@endsection
