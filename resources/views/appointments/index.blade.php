@extends('layouts.app')

@section('title', 'Janji Temu & Kunjungan Klinik (T1 & T2)')

@section('content')
<div class="space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">TRANSAKSI T1 & T2</span>
                <span class="text-xs text-slate-400">&bull; Alur Status Dua Role: Diajukan Role 2, Diproses Role 1</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Janji Temu Kunjungan Klinik</h1>
            <p class="text-sm text-slate-500">Pendaftaran antrean pemeriksaan kesehatan, vaksinasi, dan tindakan bedah.</p>
        </div>

        <div class="mt-4 sm:mt-0">
            <a href="{{ route('appointments.create') }}" class="px-4 py-2.5 rounded-xl font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-xs transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>Ajukan Kunjungan Baru (T1)</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('appointments.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode kunjungan, nama hewan, atau klien..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <select name="status" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">
                <option value="">Semua Status Alur</option>
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rescheduled" {{ $status === 'rescheduled' ? 'selected' : '' }}>Jadwal Ulang</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Selesai Diperiksa</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">

            @if($search || $status || $date)
                <a href="{{ route('appointments.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Appointments Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Pasien Hewan</th>
                        <th class="py-3.5 px-4">Layanan (M3)</th>
                        <th class="py-3.5 px-4">Waktu Kunjungan</th>
                        <th class="py-3.5 px-4">Pemilik (Pelaku T1)</th>
                        <th class="py-3.5 px-4 text-center">Status (T2)</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($appointments as $appt)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('appointments.show', $appt) }}" class="text-brand-600 hover:underline">
                                    {{ $appt->appointment_code }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $appt->pet->name }}</span>
                                <span class="text-xs text-slate-400">{{ $appt->pet->species }} &bull; {{ $appt->pet->breed ?? 'Domestik' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800 block">{{ $appt->service->name }}</span>
                                <span class="text-xs text-slate-400">Rp {{ number_format($appt->service->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ Carbon\Carbon::parse($appt->appointment_date)->isoFormat('D MMM Y') }}</span>
                                <span class="text-xs text-slate-500"><i class="fa-regular fa-clock mr-1"></i> {{ substr($appt->appointment_time, 0, 5) }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-800 block">{{ $appt->user->name }}</span>
                                <span class="text-xs text-slate-400">{{ $appt->user->phone ?? $appt->user->email }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $appt->status_badge }}">
                                    {{ $appt->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('appointments.show', $appt) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-brand-50 text-brand-700 hover:bg-brand-100 border border-brand-200" title="Detail">
                                        Rincian &rarr;
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-calendar-xmark text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Belum ada data kunjungan yang sesuai dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $appointments->links() }}
        </div>
    </div>

</div>
@endsection
