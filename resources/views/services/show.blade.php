@extends('layouts.app')

@section('title', 'Detail Layanan: ' . $service->name)

@section('content')
<div class="space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-sky-100 text-sky-800">M3: LAYANAN KLINIK</span>
                <span class="font-mono text-xs font-bold text-slate-500">{{ $service->code }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">{{ $service->name }}</h1>
            <p class="text-sm text-slate-500">Kategori: {{ $service->category }} &bull; Estimasi: {{ $service->estimated_duration }} Menit</p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('services.edit', $service) }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Layanan
            </a>
            <a href="{{ route('services.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Tarif Layanan</span>
            <span class="text-2xl font-black text-brand-700 mt-1 block">
                Rp {{ number_format($service->price, 0, ',', '.') }}
            </span>
            <span class="text-xs text-slate-400">Termasuk konsultasi standar</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Estimasi Waktu</span>
            <span class="text-2xl font-black text-slate-800 mt-1 block">
                {{ $service->estimated_duration }} Menit
            </span>
            <span class="text-xs text-slate-400">Durasi tindakan medis</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Status Publikasi</span>
            <span class="text-base font-bold {{ $service->is_active ? 'text-emerald-700' : 'text-slate-500' }} mt-1 block">
                {{ $service->is_active ? 'Aktif (Muncul di Katalog Publik)' : 'Nonaktif' }}
            </span>
            <span class="text-xs text-slate-400">Visibilitas Guest & Klien</span>
        </div>
    </div>

    <!-- Description -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-2">
        <h3 class="font-bold text-slate-800 text-sm">Deskripsi Layanan</h3>
        <p class="text-sm text-slate-600">{{ $service->description ?? 'Tidak ada rincian tambahan untuk layanan ini.' }}</p>
    </div>

    <!-- Relational Appointments History -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Riwayat Janji Temu Menggunakan Layanan Ini (T1/T2)</h3>
        @if($recentAppointments->count() > 0)
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($recentAppointments as $appt)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-mono font-bold text-slate-700">{{ $appt->appointment_code }}</span> &bull;
                            <span class="font-semibold text-slate-800">{{ $appt->pet->name }}</span> (Pemilik: {{ $appt->user->name }})
                            <span class="text-slate-400 block">{{ Carbon\Carbon::parse($appt->appointment_date)->isoFormat('D MMM Y') }} pukul {{ substr($appt->appointment_time, 0, 5) }} WIB</span>
                        </div>
                        <div>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $appt->status_badge }}">
                                {{ $appt->status_label }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 text-center py-4">Belum ada kunjungan yang memilih layanan ini.</p>
        @endif
    </div>

</div>
@endsection
