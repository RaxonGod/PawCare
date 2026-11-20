@extends('layouts.app')

@section('title', 'Master Data Obat & Perlengkapan Medis (M1)')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">DATA MASTER M1</span>
                <span class="text-xs text-slate-400">&bull; Relasi BelongsTo ke Kategori Obat (M2)</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Obat & Perlengkapan Medis</h1>
            <p class="text-sm text-slate-500">Katalog obat, sediaan injeksi, salep, vaksin, dan inventaris farmasi klinik.</p>
        </div>

        <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
            <a href="{{ route('medicines.create') }}" class="px-4 py-2.5 rounded-xl font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-xs transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Obat Baru</span>
            </a>
            <a href="{{ route('medicine-restocks.create') }}" class="px-4 py-2.5 rounded-xl font-semibold text-amber-800 bg-amber-100 hover:bg-amber-200 border border-amber-300 transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-cart-flatbed"></i>
                <span>Restock Masuk (T4)</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('medicines.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode obat..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <select name="category_id" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">
                <option value="">Semua Kategori (M2)</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="stock_filter" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">
                <option value="">Semua Kondisi Stok</option>
                <option value="low" {{ $stockFilter === 'low' ? 'selected' : '' }}>Stok Menipis (<= Min)</option>
                <option value="out" {{ $stockFilter === 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
            </select>

            @if($search || $categoryId || $stockFilter)
                <a href="{{ route('medicines.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Medicines Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Nama Obat</th>
                        <th class="py-3.5 px-4">Kategori (M2)</th>
                        <th class="py-3.5 px-4 text-center">Stok Gudang</th>
                        <th class="py-3.5 px-4 text-right">Harga Beli</th>
                        <th class="py-3.5 px-4 text-right">Harga Jual</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($medicines as $med)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                                <a href="{{ route('medicines.show', $med) }}" class="text-brand-600 hover:underline">
                                    {{ $med->code }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                {{ $med->name }}
                                <span class="text-xs text-slate-400 block font-normal">{{ $med->unit }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $med->category->name }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($med->stock <= 0)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        Habis (0)
                                    </span>
                                @elseif($med->isLowStock())
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300" title="Batas Min: {{ $med->min_stock }}">
                                        {{ $med->stock }} {{ $med->unit }} (Menipis)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        {{ $med->stock }} {{ $med->unit }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-xs">
                                Rp {{ number_format($med->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-800">
                                Rp {{ number_format($med->selling_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('medicines.show', $med) }}" class="p-1.5 text-slate-500 hover:text-brand-600 hover:bg-slate-100 rounded-lg" title="Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('medicines.edit', $med) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg" title="Ubah">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <!-- Form Hapus dengan Pencegahan Server-side -->
                                    <form action="{{ route('medicines.destroy', $med) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data obat ini? Catatan: Jika obat telah terhubung ke transaksi rekam medis atau restock, penghapusan akan dicegah oleh sistem.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-pills text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Belum ada data obat atau pencarian tidak ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-100">
            {{ $medicines->links() }}
        </div>
    </div>

</div>
@endsection
