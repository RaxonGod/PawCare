@extends('layouts.app')

@section('title', 'Detail Restock Obat: ' . $medicineRestock->restock_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-sm font-bold text-amber-800">{{ $medicineRestock->restock_code }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                    Restock Berhasil Ditambahkan
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-2">Distributor: {{ $medicineRestock->supplier_name }}</h1>
            <p class="text-sm text-slate-500">
                Penerimaan pada {{ Carbon\Carbon::parse($medicineRestock->restock_date)->isoFormat('dddd, D MMMM Y') }} &bull; Petugas: {{ $medicineRestock->user->name }}
            </p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('medicine-restocks.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                &larr; Riwayat Restock
            </a>
            <a href="{{ route('medicines.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200">
                Lihat Stok Obat (M1) &rarr;
            </a>
        </div>
    </div>

    <!-- Restock Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Total Biaya Pengadaan</span>
            <span class="text-2xl font-black text-amber-700 mt-1 block">
                Rp {{ number_format($medicineRestock->total_cost, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-400">Total belanja obat</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">No. Faktur / Surat Jalan</span>
            <span class="text-xl font-mono font-bold text-slate-800 mt-1 block">
                {{ $medicineRestock->invoice_number ?? '-' }}
            </span>
            <span class="text-xs text-slate-400">Dokumen referensi supplier</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Jumlah Item Varian</span>
            <span class="text-2xl font-black text-slate-800 mt-1 block">
                {{ $medicineRestock->details->count() }} Jenis Obat
            </span>
            <span class="text-xs text-slate-400">Tercatat masuk ke gudang</span>
        </div>
    </div>

    @if($medicineRestock->notes)
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
            <span class="font-bold block text-slate-800 mb-0.5">Catatan Pengadaan:</span>
            {{ $medicineRestock->notes }}
        </div>
    @endif

    <!-- Detail Table Items (T4 Detail) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-900 text-base flex items-center space-x-2">
            <i class="fa-solid fa-boxes-stacked text-amber-600"></i>
            <span>Daftar Item Obat yang Ditambahkan ke Stok Gudang (Detail T4)</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Kode & Nama Obat (M1)</th>
                        <th class="py-2.5 px-3">Kategori (M2)</th>
                        <th class="py-2.5 px-3 text-center">Jumlah Tambah</th>
                        <th class="py-2.5 px-3 text-right">Harga Beli Baru</th>
                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                        <th class="py-2.5 px-3">Batch & Expiry</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($medicineRestock->details as $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-2.5 px-3">
                                <span class="font-mono text-xs font-bold text-slate-500 block">{{ $item->medicine->code }}</span>
                                <a href="{{ route('medicines.show', $item->medicine) }}" class="font-semibold text-slate-900 hover:text-brand-600">
                                    {{ $item->medicine->name }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-700">
                                    {{ $item->medicine->category->name }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-emerald-700">
                                +{{ $item->quantity }} {{ $item->medicine->unit }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono">
                                Rp {{ number_format($item->purchase_price, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-500">
                                <span class="font-mono block text-slate-700">{{ $item->batch_number ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $item->expiry_date ? 'Exp: ' . Carbon\Carbon::parse($item->expiry_date)->isoFormat('D MMM Y') : '-' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 font-bold text-slate-800 border-t border-slate-200">
                    <tr class="text-base text-amber-900 bg-amber-50/60">
                        <td colspan="4" class="py-3 px-3 text-right uppercase tracking-wider">Total Nilai Pengadaan:</td>
                        <td class="py-3 px-3 text-right font-mono font-black text-amber-900">
                            Rp {{ number_format($medicineRestock->total_cost, 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
