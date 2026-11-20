@extends('layouts.app')

@section('title', 'Dashboard Dokter & Staf Klinik')

@section('content')
<div class="space-y-8">

    <!-- Header Greeting -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                    Role 1: Staf / Dokter Klinik
                </span>
                <span class="text-xs text-slate-400">&bull; {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="text-sm text-slate-500 mt-1">Ringkasan operasional harian, antrean persetujuan kunjungan, dan monitoring stok obat klinik.</p>
        </div>

        <!-- Quick Action Shortcuts -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('medical-records.create') }}" class="px-4 py-2.5 rounded-xl text-white font-semibold bg-brand-600 hover:bg-brand-700 shadow-xs transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-file-circle-plus"></i>
                <span>Input Rekam Medis (T3)</span>
            </a>
            <a href="{{ route('medicine-restocks.create') }}" class="px-4 py-2.5 rounded-xl text-slate-700 font-semibold bg-slate-100 hover:bg-slate-200 transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-cart-flatbed"></i>
                <span>Restock Obat (T4)</span>
            </a>
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 rounded-xl text-brand-700 font-semibold bg-brand-50 hover:bg-brand-100 border border-brand-200 transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-chart-column"></i>
                <span>Laporan Rekap (T5)</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Kunjungan Hari Ini</span>
                <i class="fa-solid fa-calendar-day text-indigo-500"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['today_appointments'] }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Pasien terkonfirmasi</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-amber-200 bg-amber-50/30 shadow-xs">
            <div class="flex items-center justify-between text-amber-600">
                <span class="text-xs font-semibold uppercase tracking-wider">Antrean Persetujuan</span>
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="text-2xl font-black text-amber-700 mt-2">{{ $stats['pending_appointments'] }}</div>
            <span class="text-xs text-amber-600 mt-1 block">Perlu review staf (T2)</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Pasien</span>
                <i class="fa-solid fa-paw text-sky-500"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['total_pets'] }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Hewan terdaftar</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border {{ $stats['low_stock_medicines'] > 0 ? 'border-rose-300 bg-rose-50/30' : 'border-slate-200' }} shadow-xs">
            <div class="flex items-center justify-between {{ $stats['low_stock_medicines'] > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                <span class="text-xs font-semibold uppercase tracking-wider">Stok Obat Menipis</span>
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="text-2xl font-black {{ $stats['low_stock_medicines'] > 0 ? 'text-rose-700' : 'text-slate-900' }} mt-2">
                {{ $stats['low_stock_medicines'] }}
            </div>
            <span class="text-xs {{ $stats['low_stock_medicines'] > 0 ? 'text-rose-600' : 'text-slate-500' }} mt-1 block">Di bawah batas minimum</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Pendapatan Bulan Ini</span>
                <i class="fa-solid fa-sack-dollar text-emerald-500"></i>
            </div>
            <div class="text-xl font-black text-emerald-700 mt-2">
                Rp {{ number_format($stats['monthly_revenue'], 0, ',', '.') }}
            </div>
            <span class="text-xs text-slate-500 mt-1 block">Tagihan lunas (T5)</span>
        </div>
    </div>

    <!-- Main Section: Pending Approval Queue & Low Stock Warning -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Pending Appointments Queue (T2 Alur Status Dua Role) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
                        <i class="fa-solid fa-hourglass-half text-amber-500"></i>
                        <span>Antrean Janji Temu Menunggu Persetujuan (T2)</span>
                    </h2>
                    <p class="text-xs text-slate-500">Kunjungan yang diajukan oleh Pemilik Hewan (Role 2) menunggu tindakan staf klinik.</p>
                </div>
                <a href="{{ route('appointments.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 underline">
                    Lihat Semua
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                @if($pendingQueue->count() > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($pendingQueue as $appt)
                            <div class="p-5 hover:bg-slate-50 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-mono font-bold text-slate-700">{{ $appt->appointment_code }}</span>
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">Menunggu</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-sm">
                                        {{ $appt->pet->name }} ({{ $appt->pet->species }} - {{ $appt->pet->breed ?? 'Campuran' }})
                                    </h4>
                                    <p class="text-xs text-slate-500">
                                        Pemilik: <span class="font-medium text-slate-700">{{ $appt->user->name }}</span> ({{ $appt->user->phone ?? '-' }})
                                    </p>
                                    <p class="text-xs text-slate-600 bg-slate-50 p-2 rounded-lg mt-1">
                                        <span class="font-semibold text-slate-700">Keluhan:</span> {{ Str::limit($appt->complaint, 120) }}
                                    </p>
                                    <div class="text-xs text-slate-400 pt-1">
                                        <i class="fa-regular fa-calendar mr-1"></i> Rencana: {{ Carbon\Carbon::parse($appt->appointment_date)->isoFormat('D MMM Y') }} pukul {{ substr($appt->appointment_time, 0, 5) }} WIB &bull; Layanan: {{ $appt->service->name }}
                                    </div>
                                </div>

                                <div class="flex sm:flex-col gap-2 shrink-0">
                                    <a href="{{ route('appointments.show', $appt) }}" class="px-3 py-2 rounded-xl text-xs font-bold bg-brand-600 hover:bg-brand-700 text-white text-center transition">
                                        Proses Janji Temu
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-slate-400">
                        <i class="fa-solid fa-clipboard-check text-3xl mb-2 text-emerald-500"></i>
                        <p class="text-sm font-medium text-slate-600">Semua janji temu telah ditinjau!</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tidak ada antrean persetujuan yang tertunda saat ini.</p>
                    </div>
                @endif
            </div>

            <!-- Today's Approved Schedule -->
            <div class="pt-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                        <i class="fa-solid fa-calendar-check text-brand-600"></i>
                        <span>Jadwal Pasien Hadir Hari Ini</span>
                    </h3>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                    @if($todaySchedule->count() > 0)
                        <div class="divide-y divide-slate-100">
                            @foreach($todaySchedule as $sch)
                                <div class="p-4 flex items-center justify-between text-sm">
                                    <div>
                                        <span class="font-bold text-slate-800">{{ substr($sch->appointment_time, 0, 5) }} WIB</span> &bull;
                                        <span class="font-semibold text-brand-700">{{ $sch->pet->name }}</span> ({{ $sch->pet->species }})
                                        <span class="text-xs text-slate-500 block">Layanan: {{ $sch->service->name }} &bull; Klien: {{ $sch->user->name }}</span>
                                    </div>
                                    <a href="{{ route('medical-records.create', ['appointment_id' => $sch->id]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200">
                                        <i class="fa-solid fa-stethoscope mr-1"></i> Periksa (T3)
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-xs text-slate-400">
                            Tidak ada jadwal janji temu terkonfirmasi untuk hari ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Low Stock Alert & Master Shortcuts -->
        <div class="space-y-6">
            <!-- Low stock alert box -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-2">
                        <i class="fa-solid fa-boxes-stacked text-rose-500"></i>
                        <span>Peringatan Stok Obat (M1)</span>
                    </h3>
                    <a href="{{ route('medicines.index', ['stock_filter' => 'low']) }}" class="text-xs text-rose-600 font-semibold hover:underline">
                        Lihat Semua
                    </a>
                </div>

                @if($criticalMedicines->count() > 0)
                    <div class="space-y-2.5">
                        @foreach($criticalMedicines as $crit)
                            <div class="p-3 rounded-xl bg-rose-50/60 border border-rose-200 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-rose-900 block">{{ $crit->name }}</span>
                                    <span class="text-rose-600 font-mono">{{ $crit->code }} &bull; {{ $crit->category->name }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 rounded-md font-bold text-xs {{ $crit->stock <= 0 ? 'bg-rose-600 text-white' : 'bg-rose-200 text-rose-800' }}">
                                        {{ $crit->stock }} {{ $crit->unit }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">Min: {{ $crit->min_stock }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('medicine-restocks.create') }}" class="block w-full py-2.5 rounded-xl text-center text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-xs transition">
                        <i class="fa-solid fa-cart-plus mr-1"></i> Buat Dokumen Restock Obat (T4)
                    </a>
                @else
                    <div class="text-center py-6 text-slate-400">
                        <i class="fa-solid fa-check-circle text-emerald-500 text-2xl mb-1"></i>
                        <p class="text-xs font-semibold text-slate-700">Persediaan Obat Aman</p>
                        <p class="text-[11px] text-slate-400">Seluruh obat berada di atas batas minimum stok.</p>
                    </div>
                @endif
            </div>

            <!-- Management Master Links -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-3">
                <h3 class="font-bold text-slate-900 text-sm">Pusat Data Master Klinik</h3>
                <div class="grid grid-cols-1 gap-2 text-xs">
                    <a href="{{ route('medicines.index') }}" class="p-2.5 rounded-xl border border-slate-200 hover:bg-brand-50 hover:border-brand-200 transition flex items-center justify-between text-slate-700">
                        <span class="font-semibold"><i class="fa-solid fa-pills mr-2 text-emerald-600"></i> M1: Obat & Perlengkapan</span>
                        <i class="fa-solid fa-arrow-right text-slate-400"></i>
                    </a>
                    <a href="{{ route('medicine-categories.index') }}" class="p-2.5 rounded-xl border border-slate-200 hover:bg-brand-50 hover:border-brand-200 transition flex items-center justify-between text-slate-700">
                        <span class="font-semibold"><i class="fa-solid fa-tags mr-2 text-indigo-600"></i> M2: Kategori Obat</span>
                        <i class="fa-solid fa-arrow-right text-slate-400"></i>
                    </a>
                    <a href="{{ route('services.index') }}" class="p-2.5 rounded-xl border border-slate-200 hover:bg-brand-50 hover:border-brand-200 transition flex items-center justify-between text-slate-700">
                        <span class="font-semibold"><i class="fa-solid fa-stethoscope mr-2 text-sky-600"></i> M3: Layanan & Tarif</span>
                        <i class="fa-solid fa-arrow-right text-slate-400"></i>
                    </a>
                    <a href="{{ route('pets.index') }}" class="p-2.5 rounded-xl border border-slate-200 hover:bg-brand-50 hover:border-brand-200 transition flex items-center justify-between text-slate-700">
                        <span class="font-semibold"><i class="fa-solid fa-dog mr-2 text-amber-600"></i> Data Seluruh Pasien Hewan</span>
                        <i class="fa-solid fa-arrow-right text-slate-400"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
