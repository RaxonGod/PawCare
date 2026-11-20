@extends('layouts.app')

@section('title', 'Dashboard Pemilik Hewan')

@section('content')
<div class="space-y-8">

    <!-- Header Greeting -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-sky-100 text-sky-800 uppercase tracking-wider">
                    Role 2: Pemilik Hewan
                </span>
                <span class="text-xs text-slate-400">&bull; {{ Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">Halo, {{ Auth::user()->name }}!</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola hewan kesayangan Anda, jadwalkan janji temu klinik, serta pantau riwayat rekam medis dan tagihan.</p>
        </div>

        <!-- Quick CTA -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('appointments.create') }}" class="px-4 py-2.5 rounded-xl text-white font-semibold bg-brand-600 hover:bg-brand-700 shadow-xs transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>Ajukan Kunjungan (T1)</span>
            </a>
            <a href="{{ route('pets.create') }}" class="px-4 py-2.5 rounded-xl text-slate-700 font-semibold bg-slate-100 hover:bg-slate-200 transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Hewan</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Hewan Terdaftar</span>
                <i class="fa-solid fa-paw text-brand-600"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['total_my_pets'] }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Tercatat dalam akun</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Jadwal Mendatang</span>
                <i class="fa-solid fa-calendar-check text-emerald-600"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['upcoming_appointments'] }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Telah disetujui klinik</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Menunggu Review</span>
                <i class="fa-solid fa-clock-rotate-left text-amber-500"></i>
            </div>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['pending_appointments'] }}</div>
            <span class="text-xs text-slate-500 mt-1 block">Proses konfirmasi staf</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border {{ $stats['unpaid_invoices'] > 0 ? 'border-rose-300 bg-rose-50/30' : 'border-slate-200' }} shadow-xs">
            <div class="flex items-center justify-between {{ $stats['unpaid_invoices'] > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                <span class="text-xs font-semibold uppercase tracking-wider">Tagihan Belum Lunas</span>
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div class="text-2xl font-black {{ $stats['unpaid_invoices'] > 0 ? 'text-rose-700' : 'text-slate-900' }} mt-2">{{ $stats['unpaid_invoices'] }}</div>
            <span class="text-xs {{ $stats['unpaid_invoices'] > 0 ? 'text-rose-600' : 'text-slate-500' }} mt-1 block">Menunggu pembayaran</span>
        </div>
    </div>

    <!-- My Registered Pets Cards -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
                    <i class="fa-solid fa-dog text-amber-600"></i>
                    <span>Hewan Kesayangan Saya</span>
                </h2>
                <p class="text-xs text-slate-500">Daftar hewan peliharaan Anda yang siap dikonsultasikan.</p>
            </div>
            <a href="{{ route('pets.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 underline">
                Kelola Semua
            </a>
        </div>

        @if($myPets->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($myPets as $pet)
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs hover:border-brand-300 transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $pet->species === 'Kucing' ? 'bg-amber-100 text-amber-800' : ($pet->species === 'Anjing' ? 'bg-sky-100 text-sky-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $pet->species }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    <i class="fa-solid {{ $pet->gender === 'Jantan' ? 'fa-mars text-blue-500' : 'fa-venus text-pink-500' }}"></i> {{ $pet->gender }}
                                </span>
                            </div>
                            <h3 class="font-bold text-base text-slate-900">{{ $pet->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ $pet->breed ?? 'Ras Domestik' }} &bull; {{ $pet->weight_kg ? $pet->weight_kg . ' kg' : '-' }}</p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <a href="{{ route('pets.show', $pet) }}" class="font-semibold text-slate-600 hover:text-brand-700">
                                Profil Lengkap
                            </a>
                            <a href="{{ route('appointments.create', ['pet_id' => $pet->id]) }}" class="font-bold text-brand-600 hover:text-brand-700">
                                Janji Temu &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center bg-white rounded-2xl border border-slate-200 text-slate-400">
                <i class="fa-solid fa-cat text-3xl mb-2 text-slate-300"></i>
                <p class="text-sm font-medium text-slate-600">Belum ada hewan peliharaan yang didaftarkan.</p>
                <a href="{{ route('pets.create') }}" class="mt-3 inline-block px-4 py-2 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700">
                    Daftarkan Hewan Sekarang
                </a>
            </div>
        @endif
    </div>

    <!-- Active Appointments & Recent Records -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Appointments (T1/T2) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-base flex items-center space-x-2">
                    <i class="fa-solid fa-calendar-days text-brand-600"></i>
                    <span>Riwayat Janji Temu Kunjungan (T1 & T2)</span>
                </h3>
                <a href="{{ route('appointments.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">
                    Lihat Semua
                </a>
            </div>

            @if($recentAppointments->count() > 0)
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($recentAppointments as $appt)
                        <div class="py-3 flex items-center justify-between">
                            <div class="space-y-0.5">
                                <span class="font-mono font-bold text-slate-800">{{ $appt->appointment_code }}</span>
                                <p class="text-slate-600 font-medium">{{ $appt->pet->name }} &bull; {{ $appt->service->name }}</p>
                                <span class="text-slate-400 block">{{ Carbon\Carbon::parse($appt->appointment_date)->isoFormat('D MMM Y') }} pukul {{ substr($appt->appointment_time, 0, 5) }} WIB</span>
                            </div>
                            <div class="text-right">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $appt->status_badge }}">
                                    {{ $appt->status_label }}
                                </span>
                                <a href="{{ route('appointments.show', $appt) }}" class="block mt-1 font-semibold text-brand-600 hover:underline">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat janji temu kunjungan.</p>
            @endif
        </div>

        <!-- Recent Medical Records (T3) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-base flex items-center space-x-2">
                    <i class="fa-solid fa-file-waveform text-indigo-600"></i>
                    <span>Catatan Rekam Medis Pasien (T3)</span>
                </h3>
                <a href="{{ route('medical-records.index') }}" class="text-xs font-semibold text-brand-600 hover:underline">
                    Lihat Semua
                </a>
            </div>

            @if($recentMedicalRecords->count() > 0)
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($recentMedicalRecords as $rec)
                        <div class="py-3 flex items-center justify-between">
                            <div class="space-y-0.5">
                                <span class="font-mono font-bold text-slate-800">{{ $rec->record_code }}</span>
                                <p class="text-slate-700 font-semibold">{{ $rec->pet->name }} &bull; Diagnosa: {{ $rec->diagnosis }}</p>
                                <span class="text-slate-400 block">Periksa: {{ Carbon\Carbon::parse($rec->examination_date)->isoFormat('D MMM Y') }} &bull; Dokter: {{ $rec->vet->name }}</span>
                            </div>
                            <div>
                                <a href="{{ route('medical-records.show', $rec) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 font-semibold hover:bg-indigo-100 border border-indigo-200">
                                    Lihat Resep
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 text-center py-4">Belum ada catatan rekam medis dari dokter hewan.</p>
            @endif
        </div>
    </div>

</div>
@endsection
