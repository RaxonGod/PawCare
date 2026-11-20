@extends('layouts.app')

@section('title', 'Detail Kategori Obat: ' . $medicineCategory->name)

@section('content')
<div class="space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800">M2: KATEGORI OBAT</span>
                <span class="font-mono text-xs text-slate-500">slug: {{ $medicineCategory->slug }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">{{ $medicineCategory->name }}</h1>
            <p class="text-sm text-slate-500">{{ $medicineCategory->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('medicine-categories.edit', $medicineCategory) }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Kategori
            </a>
            <a href="{{ route('medicine-categories.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Related Medicines (M1) List in this Category -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Daftar Obat dalam Kategori Ini (Relasi HasMany)</h3>
                <p class="text-xs text-slate-500">Menampilkan seluruh obat M1 yang terhubung ke kategori {{ $medicineCategory->name }}.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                {{ $medicineCategory->medicines->count() }} Data Obat
            </span>
        </div>

        @if($medicineCategory->medicines->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Kode</th>
                            <th class="py-2.5 px-3">Nama Obat</th>
                            <th class="py-2.5 px-3">Satuan</th>
                            <th class="py-2.5 px-3 text-center">Stok</th>
                            <th class="py-2.5 px-3 text-right">Harga Jual</th>
                            <th class="py-2.5 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($medicineCategory->medicines as $med)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-700">{{ $med->code }}</td>
                                <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $med->name }}</td>
                                <td class="py-2.5 px-3">{{ $med->unit }}</td>
                                <td class="py-2.5 px-3 text-center font-bold {{ $med->isLowStock() ? 'text-rose-600' : 'text-slate-800' }}">
                                    {{ $med->stock }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono">Rp {{ number_format($med->selling_price, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <a href="{{ route('medicines.show', $med) }}" class="text-xs font-semibold text-brand-600 hover:underline">
                                        Lihat Obat &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-slate-400 text-xs">
                Belum ada obat yang dimasukkan ke dalam kategori ini.
            </div>
        @endif
    </div>

</div>
@endsection
