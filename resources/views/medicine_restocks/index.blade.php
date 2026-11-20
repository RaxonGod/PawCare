@extends('layouts.app')

@section('title', 'Riwayat Restock Obat Masuk (T4)')

@section('content')
<div class="space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">TRANSAKSI T4</span>
                <span class="text-xs text-slate-400">&bull; Header-Detail, DB::transaction(), Menambah Stok Obat M1</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Pengadaan & Restock Obat Masuk</h1>
            <p class="text-sm text-slate-500">Pencatatan pengadaan dari pemasok/distributor farmasi yang otomatis meningkatkan stok obat.</p>
        </div>

        <div class="mt-4 sm:mt-0">
            <a href="{{ route('medicine-restocks.create') }}" class="px-4 py-2.5 rounded-xl font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-xs transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Buat Dokumen Restock (T4)</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('medicine-restocks.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode restock, nama pemasok, atau no faktur..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">

            @if($search || $date)
                <a href="{{ route('medicine-restocks.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Restocks Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Kode Restock</th>
                        <th class="py-3.5 px-4">Tanggal Masuk</th>
                        <th class="py-3.5 px-4">Distributor / Pemasok</th>
                        <th class="py-3.5 px-4">Petugas Gudang (Pelaku T4)</th>
                        <th class="py-3.5 px-4 text-center">Variasi Obat</th>
                        <th class="py-3.5 px-4 text-right">Total Biaya Pengadaan</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($restocks as $rst)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-amber-800">
                                <a href="{{ route('medicine-restocks.show', $rst) }}" class="hover:underline">
                                    {{ $rst->restock_code }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ Carbon\Carbon::parse($rst->restock_date)->isoFormat('D MMM Y') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $rst->supplier_name }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $rst->invoice_number ? 'Faktur: ' . $rst->invoice_number : 'Faktur internal' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-800 block">{{ $rst->user->name }}</span>
                                <span class="text-xs text-slate-400">{{ $rst->created_at->isoFormat('D MMM Y, HH:mm') }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ $rst->details->count() }} Jenis Obat
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($rst->total_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('medicine-restocks.show', $rst) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200">
                                    Lihat Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-boxes-packing text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Belum ada riwayat transaksi restock obat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $restocks->links() }}
        </div>
    </div>

</div>
@endsection
