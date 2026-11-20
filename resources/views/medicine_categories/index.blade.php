@extends('layouts.app')

@section('title', 'Master Data Kategori Obat (M2)')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800">DATA MASTER M2</span>
                <span class="text-xs text-slate-400">&bull; Relasi HasMany ke Obat (M1)</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Kategori Obat & Farmasi</h1>
            <p class="text-sm text-slate-500">Pengelompokan jenis obat (Antibiotik, Analgesik, Antiparasit, Vitamin, dll).</p>
        </div>

        <div class="mt-4 sm:mt-0">
            <a href="{{ route('medicine-categories.create') }}" class="px-4 py-2.5 rounded-xl font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-xs transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Kategori Baru</span>
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('medicine-categories.index') }}" method="GET" class="flex items-center gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau deskripsi kategori obat..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            @if($search)
                <a href="{{ route('medicine-categories.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">Nama Kategori</th>
                        <th class="py-3.5 px-4">Slug</th>
                        <th class="py-3.5 px-4">Deskripsi</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Obat Terdaftar (M1)</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $index => $cat)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-slate-400 font-mono text-xs">{{ $categories->firstItem() + $index }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <a href="{{ route('medicine-categories.show', $cat) }}" class="text-brand-700 hover:underline">
                                    {{ $cat->name }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-500">{{ $cat->slug }}</td>
                            <td class="py-3.5 px-4 text-slate-600 text-xs max-w-xs truncate">{{ $cat->description ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $cat->medicines_count > 0 ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $cat->medicines_count }} Obat
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('medicine-categories.show', $cat) }}" class="p-1.5 text-slate-500 hover:text-brand-600 hover:bg-slate-100 rounded-lg" title="Lihat">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('medicine-categories.edit', $cat) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg" title="Ubah">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <!-- Pencegahan Hapus Master jika sudah dipakai transaksi/relasi -->
                                    <form action="{{ route('medicine-categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Sistem akan mencegah penghapusan jika kategori masih memuat data obat (M1).');">
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
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-tags text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Belum ada data kategori obat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $categories->links() }}
        </div>
    </div>

</div>
@endsection
