@extends('layouts.app')

@section('title', 'Laporan Rekapitulasi (Query Builder)')

@section('content')
<div class="space-y-6">

    <!-- Header & Description -->
    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-violet-100 text-violet-800">TRANSAKSI T5: LAPORAN QUERY BUILDER</span>
                <span class="text-xs text-slate-400">&bull; Join, GroupBy, Sum, Count Agregat</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Laporan Rekapitulasi & Analitik Klinik</h1>
            <p class="text-sm text-slate-500">Rekapitulasi keuangan pendapatan kasir, konsumsi obat pasien, dan statistik utilisasi layanan.</p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200">
                <i class="fa-solid fa-print mr-1"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Date Range Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-center gap-4 text-sm">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <div class="flex items-center space-x-2">
                <label for="start_date" class="font-semibold text-slate-700 text-xs uppercase">Dari Tanggal:</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" required
                    class="px-3 py-1.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
            </div>

            <div class="flex items-center space-x-2">
                <label for="end_date" class="font-semibold text-slate-700 text-xs uppercase">Sampai Tanggal:</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" required
                    class="px-3 py-1.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl font-bold text-white bg-brand-600 hover:bg-brand-700 text-xs shadow-xs">
                <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter Periode
            </button>
        </form>
    </div>

    <!-- Financial Aggregates Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Total Transaksi Lunas</span>
            <span class="text-2xl font-black text-slate-800 mt-1 block">
                {{ $financialSummary->paid_invoice_count ?? 0 }} Invoice
            </span>
            <span class="text-xs text-slate-400">Periode terpilih</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Pendapatan Jasa Layanan</span>
            <span class="text-xl font-bold text-indigo-700 mt-1 block">
                Rp {{ number_format($financialSummary->sum_service ?? 0, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-400">Tindakan dokter & medis</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Pendapatan Farmasi Obat</span>
            <span class="text-xl font-bold text-emerald-700 mt-1 block">
                Rp {{ number_format($financialSummary->sum_medicine ?? 0, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-400">{{ $totalMedicineDispensedQty }} unit obat terpakai</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-brand-300 bg-brand-50/40 shadow-xs">
            <span class="text-xs text-brand-700 font-semibold uppercase block">Total Penerimaan Kas</span>
            <span class="text-2xl font-black text-brand-800 mt-1 block">
                Rp {{ number_format($financialSummary->sum_total ?? 0, 0, ',', '.') }}
            </span>
            <span class="text-xs text-brand-600">Grand Total Rekapitulasi</span>
        </div>
    </div>

    <!-- Navigation Tabs for Query Builder Reports -->
    <div class="flex border-b border-slate-200 space-x-4">
        <a href="{{ route('reports.index', ['tab' => 'revenue', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
            class="py-3 px-4 border-b-2 font-bold text-sm transition {{ $activeTab === 'revenue' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            <i class="fa-solid fa-money-bill-trend-up mr-1.5"></i> 1. Rekapitulasi Pendapatan Harian
        </a>

        <a href="{{ route('reports.index', ['tab' => 'medicines', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
            class="py-3 px-4 border-b-2 font-bold text-sm transition {{ $activeTab === 'medicines' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            <i class="fa-solid fa-pills mr-1.5"></i> 2. Rekapitulasi Pemakaian Obat Pasien
        </a>

        <a href="{{ route('reports.index', ['tab' => 'services', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
            class="py-3 px-4 border-b-2 font-bold text-sm transition {{ $activeTab === 'services' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
            <i class="fa-solid fa-stethoscope mr-1.5"></i> 3. Rekapitulasi Kunjungan Layanan
        </a>
    </div>

    <!-- TAB 1: REVENUE REPORT (Query Builder with join, groupBy, sum, count) -->
    @if($activeTab === 'revenue')
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs space-y-4 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Rekapitulasi Pendapatan Harian (Query Builder)</h3>
                    <p class="text-xs text-slate-500">Query memanfaatkan <code>DB::table('invoices')->join(...)->groupBy(DATE(paid_at))</code> dengan agregat <code>SUM</code> & <code>COUNT</code>.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Tanggal Pembayaran</th>
                            <th class="py-3.5 px-4 text-center">Jumlah Transaksi</th>
                            <th class="py-3.5 px-4 text-right">Pendapatan Jasa</th>
                            <th class="py-3.5 px-4 text-right">Pendapatan Obat</th>
                            <th class="py-3.5 px-4 text-right">Total Pendapatan Harian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($revenueReports as $rev)
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    {{ Carbon\Carbon::parse($rev->payment_day)->isoFormat('dddd, D MMMM Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-mono">
                                        {{ $rev->total_transactions }} Transaksi
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono">
                                    Rp {{ number_format($rev->total_service_income, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono">
                                    Rp {{ number_format($rev->total_medicine_income, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-black text-emerald-700">
                                    Rp {{ number_format($rev->grand_total_income, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Tidak ada data pendapatan pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold text-slate-900 border-t border-slate-200">
                        <tr>
                            <td class="py-3 px-4">Total Rekap Periode:</td>
                            <td class="py-3 px-4 text-center font-mono">{{ $revenueReports->sum('total_transactions') }} Transaksi</td>
                            <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($revenueReports->sum('total_service_income'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($revenueReports->sum('total_medicine_income'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-black text-emerald-800 text-base">
                                Rp {{ number_format($revenueReports->sum('grand_total_income'), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 2: MEDICINE USAGE REPORT (Query Builder with join, groupBy, sum, count) -->
    @if($activeTab === 'medicines')
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs space-y-4 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Rekapitulasi Pemakaian Obat Pasien (Query Builder)</h3>
                    <p class="text-xs text-slate-500">Query memanfaatkan <code>DB::table('medical_record_details')->join('medicines')->groupBy(...)</code> dengan agregat <code>SUM(quantity)</code>.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Kode & Nama Obat (M1)</th>
                            <th class="py-3.5 px-4">Kategori (M2)</th>
                            <th class="py-3.5 px-4 text-center">Frekuensi Resep</th>
                            <th class="py-3.5 px-4 text-center">Total Kuantitas Terpakai</th>
                            <th class="py-3.5 px-4 text-center">Sisa Stok Fisik</th>
                            <th class="py-3.5 px-4 text-right">Nilai Rupiah Obat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($medicineUsageReports as $medReport)
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $medReport->name }}
                                    <span class="text-xs font-mono text-slate-400 block font-normal">{{ $medReport->code }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-700">
                                        {{ $medReport->category_name }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                    {{ $medReport->prescription_count }} Kali
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        {{ $medReport->total_dispensed_qty }} {{ $medReport->unit }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                    {{ $medReport->current_stock }} {{ $medReport->unit }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($medReport->total_dispensed_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Tidak ada pemakaian obat tercatat pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold text-slate-900 border-t border-slate-200">
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right">Total Seluruh Pemakaian:</td>
                            <td class="py-3 px-4 text-center font-mono">{{ $totalMedicineDispensedQty }} Unit</td>
                            <td></td>
                            <td class="py-3 px-4 text-right font-mono font-black text-emerald-800 text-base">
                                Rp {{ number_format($totalMedicineDispensedAmount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 3: SERVICE UTILIZATION REPORT (Query Builder with join, groupBy, count, conditional cases) -->
    @if($activeTab === 'services')
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs space-y-4 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Rekapitulasi Kunjungan Layanan Klinik (Query Builder)</h3>
                    <p class="text-xs text-slate-500">Query memanfaatkan <code>DB::table('appointments')->join('services')->groupBy(...)</code> dengan agregat <code>COUNT</code> status alur.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">Nama Layanan (M3)</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4 text-right">Tarif</th>
                            <th class="py-3.5 px-4 text-center">Total Pengajuan (T1)</th>
                            <th class="py-3.5 px-4 text-center">Selesai (T3)</th>
                            <th class="py-3.5 px-4 text-center">Disetujui (T2)</th>
                            <th class="py-3.5 px-4 text-center">Ditolak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($serviceUtilizationReports as $srvReport)
                            <tr class="hover:bg-slate-50/80">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $srvReport->name }}
                                    <span class="text-xs font-mono text-slate-400 block font-normal">{{ $srvReport->code }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-700">
                                        {{ $srvReport->category }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono">
                                    Rp {{ number_format($srvReport->price, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-mono">
                                        {{ $srvReport->total_bookings }} Kunjungan
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-indigo-700">
                                    {{ $srvReport->completed_count }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-emerald-700">
                                    {{ $srvReport->approved_count }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-rose-600">
                                    {{ $srvReport->rejected_count }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Tidak ada data kunjungan pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold text-slate-900 border-t border-slate-200">
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right">Total Janji Temu Masuk:</td>
                            <td class="py-3 px-4 text-center font-mono">{{ $serviceUtilizationReports->sum('total_bookings') }} Kunjungan</td>
                            <td class="py-3 px-4 text-center font-mono">{{ $serviceUtilizationReports->sum('completed_count') }}</td>
                            <td class="py-3 px-4 text-center font-mono">{{ $serviceUtilizationReports->sum('approved_count') }}</td>
                            <td class="py-3 px-4 text-center font-mono">{{ $serviceUtilizationReports->sum('rejected_count') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
