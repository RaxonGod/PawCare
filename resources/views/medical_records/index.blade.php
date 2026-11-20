@extends('layouts.app')

@section('title', 'Daftar Rekam Medis (T3)')

@section('content')
<div class="space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800">TRANSAKSI T3</span>
                <span class="text-xs text-slate-400">&bull; Header-Detail, DB::transaction(), Mengurangi Stok M1</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Rekam Medis Pasien Hewan</h1>
            <p class="text-sm text-slate-500">Pencatatan hasil diagnosa, suhu, tindakan dokter, serta peresepan obat langsung.</p>
        </div>

        @if(Auth::user()->isStaff())
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('medical-records.create') }}" class="px-4 py-2.5 rounded-xl font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-xs transition flex items-center space-x-2 text-sm">
                    <i class="fa-solid fa-plus"></i>
                    <span>Input Rekam Medis Baru (T3)</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('medical-records.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode rekam medis, diagnosa, nama pasien, atau pemilik..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">

            @if($search || $date)
                <a href="{{ route('medical-records.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Records Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">No. Rekam Medis</th>
                        <th class="py-3.5 px-4">Tanggal Periksa</th>
                        <th class="py-3.5 px-4">Pasien & Pemilik</th>
                        <th class="py-3.5 px-4">Diagnosa Dokter</th>
                        <th class="py-3.5 px-4 text-center">Item Obat (M1)</th>
                        <th class="py-3.5 px-4 text-right">Total Biaya</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-brand-700">
                                <a href="{{ route('medical-records.show', $rec) }}" class="hover:underline">
                                    {{ $rec->record_code }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ Carbon\Carbon::parse($rec->examination_date)->isoFormat('D MMM Y') }}
                                <span class="text-[11px] text-slate-400 block">{{ $rec->vet->name }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $rec->pet->name }} ({{ $rec->pet->species }})</span>
                                <span class="text-xs text-slate-400">Pemilik: {{ $rec->pet->owner->name }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800 block max-w-xs truncate">{{ $rec->diagnosis }}</span>
                                <span class="text-xs text-slate-400 block max-w-xs truncate">{{ $rec->treatment_actions }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $rec->details->count() }} Jenis Obat
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-800">
                                Rp {{ number_format($rec->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('medical-records.show', $rec) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200">
                                    Lihat Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-file-waveform text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Belum ada data rekam medis.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $records->links() }}
        </div>
    </div>

</div>
@endsection
