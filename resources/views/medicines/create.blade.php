@extends('layouts.app')

@section('title', 'Tambah Obat Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Obat Baru (M1)</h1>
            <p class="text-sm text-slate-500">Masukkan rincian sediaan obat dan kaitkan ke kategori obat (M2).</p>
        </div>
        <a href="{{ route('medicines.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('medicines.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="block text-sm font-semibold text-slate-700 mb-1">Kode Obat <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $suggestedCode) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-mono text-sm focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-700 mb-1">Kategori Obat (M2) <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Obat & Sediaan <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="Contoh: Amoxicillin Clavulanate Drop 15ml">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="unit" class="block text-sm font-semibold text-slate-700 mb-1">Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', 'Botol') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Botol, Tablet, Vial, Tube">
                </div>

                <div>
                    <label for="stock" class="block text-sm font-semibold text-slate-700 mb-1">Stok Awal <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 10) }}" min="0" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="min_stock" class="block text-sm font-semibold text-slate-700 mb-1">Batas Min. Stok <span class="text-rose-500">*</span></label>
                    <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', 5) }}" min="0" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="purchase_price" class="block text-sm font-semibold text-slate-700 mb-1">Harga Beli (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" min="0" step="100" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Contoh: 45000">
                </div>

                <div>
                    <label for="selling_price" class="block text-sm font-semibold text-slate-700 mb-1">Harga Jual (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" min="0" step="100" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Contoh: 65000">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Indikasi Obat</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="Indikasi pemakaian, efek samping, atau catatan penyimpanan...">{{ old('description') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-2">
                <a href="{{ route('medicines.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                    Simpan Data Obat
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
