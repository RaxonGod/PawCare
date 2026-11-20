@extends('layouts.app')

@section('title', 'Daftar Tagihan Kunjungan (T5)')

@section('content')
<div class="space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">TRANSAKSI T5</span>
                <span class="text-xs text-slate-400">&bull; Pembayaran Tagihan & Penerimaan Kasir</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Tagihan Kunjungan & Invoice</h1>
            <p class="text-sm text-slate-500">Daftar invoice terbit dari tindakan pemeriksaan medis dan pembelian obat.</p>
        </div>

        @if(Auth::user()->isStaff())
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('reports.index') }}" class="px-4 py-2.5 rounded-xl font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition flex items-center space-x-2 text-sm">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Laporan Rekap Query Builder &rarr;</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('invoices.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode invoice atau nama klien..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <select name="status" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">
                <option value="">Semua Status Pembayaran</option>
                <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Belum Lunas (Unpaid)</option>
                <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
            </select>

            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">

            @if($search || $status || $date)
                <a href="{{ route('invoices.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">No. Invoice</th>
                        <th class="py-3.5 px-4">Tanggal Terbit</th>
                        <th class="py-3.5 px-4">Pemilik Hewan</th>
                        <th class="py-3.5 px-4 text-right">Biaya Layanan</th>
                        <th class="py-3.5 px-4 text-right">Biaya Obat</th>
                        <th class="py-3.5 px-4 text-right">Total Tagihan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">
                                <a href="{{ route('invoices.show', $inv) }}" class="text-brand-600 hover:underline">
                                    {{ $inv->invoice_code }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600">
                                {{ $inv->created_at->isoFormat('D MMM Y') }}
                                <span class="text-[11px] text-slate-400 block">Jatuh Tempo: {{ Carbon\Carbon::parse($inv->due_date)->isoFormat('D MMM Y') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $inv->user->name }}</span>
                                <span class="text-xs text-slate-400">{{ $inv->user->phone ?? $inv->user->email }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-xs">
                                Rp {{ number_format($inv->service_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-xs">
                                Rp {{ number_format($inv->medicine_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $inv->status_badge }}">
                                    {{ $inv->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('invoices.show', $inv) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-50 text-brand-700 hover:bg-brand-100 border border-brand-200">
                                    {{ $inv->payment_status === 'unpaid' && Auth::user()->isStaff() ? 'Bayar / Rincian' : 'Rincian' }} &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Belum ada dokumen tagihan terbit.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $invoices->links() }}
        </div>
    </div>

</div>
@endsection
