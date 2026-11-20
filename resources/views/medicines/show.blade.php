@extends('layouts.app')

@section('title', 'Detail Obat: ' . $medicine->name)

@section('content')
<div class="space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">M1: OBAT</span>
                <span class="font-mono text-xs font-bold text-slate-500">{{ $medicine->code }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">{{ $medicine->name }}</h1>
            <p class="text-sm text-slate-500">Kategori: {{ $medicine->category->name }} &bull; Satuan: {{ $medicine->unit }}</p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('medicines.edit', $medicine) }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Data
            </a>
            <a href="{{ route('medicines.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Sisa Stok Fisik</span>
            <span class="text-2xl font-black {{ $medicine->isLowStock() ? 'text-rose-600' : 'text-slate-900' }} mt-1 block">
                {{ $medicine->stock }} {{ $medicine->unit }}
            </span>
            <span class="text-xs text-slate-400">Batas Min: {{ $medicine->min_stock }} {{ $medicine->unit }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Harga Beli Gudang</span>
            <span class="text-xl font-bold text-slate-700 mt-1 block">
                Rp {{ number_format($medicine->purchase_price, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-400">Modal per {{ $medicine->unit }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Harga Jual Resep</span>
            <span class="text-xl font-bold text-emerald-700 mt-1 block">
                Rp {{ number_format($medicine->selling_price, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-400">Margin: Rp {{ number_format($medicine->selling_price - $medicine->purchase_price, 0, ',', '.') }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Kategori Obat (M2)</span>
            <span class="text-base font-bold text-slate-800 mt-1 block">
                {{ $medicine->category->name }}
            </span>
            <a href="{{ route('medicine-categories.show', $medicine->category) }}" class="text-xs text-brand-600 hover:underline">
                Lihat Kategori &rarr;
            </a>
        </div>
    </div>

    <!-- Description -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-2">
        <h3 class="font-bold text-slate-800 text-sm">Deskripsi & Indikasi Medis</h3>
        <p class="text-sm text-slate-600">{{ $medicine->description ?? 'Tidak ada deskripsi tambahan untuk obat ini.' }}</p>
    </div>

    <!-- Relational History Grid: T3 Medical Record Usage & T4 Restock History -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Usage History (T3) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-file-waveform text-indigo-600"></i>
                    <span>Riwayat Pemakaian Resep Rekam Medis (T3)</span>
                </h3>
            </div>

            @if($recentUsage->count() > 0)
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($recentUsage as $use)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-mono font-bold text-slate-700">{{ $use->medicalRecord->record_code }}</span> &bull;
                                <span class="font-semibold text-slate-800">{{ $use->medicalRecord->pet->name }}</span>
                                <span class="text-slate-400 block">{{ Carbon\Carbon::parse($use->medicalRecord->examination_date)->isoFormat('D MMM Y') }} &bull; Aturan: {{ $use->dosage_instructions }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-rose-600">-{{ $use->quantity }} {{ $medicine->unit }}</span>
                                <span class="text-[11px] text-slate-500 block">Rp {{ number_format($use->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat peresepan pada rekam medis.</p>
            @endif
        </div>

        <!-- Restock History (T4) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-boxes-packing text-amber-600"></i>
                    <span>Riwayat Pengadaan / Restock Obat (T4)</span>
                </h3>
            </div>

            @if($recentRestocks->count() > 0)
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($recentRestocks as $rst)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-mono font-bold text-slate-700">{{ $rst->restock->restock_code }}</span> &bull;
                                <span class="font-semibold text-slate-800">{{ $rst->restock->supplier_name }}</span>
                                <span class="text-slate-400 block">{{ Carbon\Carbon::parse($rst->restock->restock_date)->isoFormat('D MMM Y') }} &bull; Batch: {{ $rst->batch_number ?? '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-emerald-600">+{{ $rst->quantity }} {{ $medicine->unit }}</span>
                                <span class="text-[11px] text-slate-500 block">@ Rp {{ number_format($rst->purchase_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat transaksi restock.</p>
            @endif
        </div>
    </div>

</div>
@endsection
