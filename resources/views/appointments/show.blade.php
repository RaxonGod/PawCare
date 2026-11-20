@extends('layouts.app')

@section('title', 'Rincian Janji Temu: ' . $appointment->appointment_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-sm font-bold text-slate-600">{{ $appointment->appointment_code }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $appointment->status_badge }}">
                    {{ $appointment->status_label }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-2">Kunjungan Pasien: {{ $appointment->pet->name }}</h1>
            <p class="text-sm text-slate-500">Diajukan pada {{ $appointment->created_at->isoFormat('D MMMM Y, HH:mm') }} WIB oleh {{ $appointment->user->name }}</p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                &larr; Daftar Kunjungan
            </a>
        </div>
    </div>

    <!-- Appointment Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Patient & Owner Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-dog text-brand-600"></i>
                <span>Data Pasien & Klien</span>
            </h3>
            <div class="text-xs space-y-1.5 text-slate-600">
                <div><span class="text-slate-400">Nama Hewan:</span> <span class="font-bold text-slate-800">{{ $appointment->pet->name }}</span></div>
                <div><span class="text-slate-400">Spesies / Ras:</span> {{ $appointment->pet->species }} ({{ $appointment->pet->breed ?? 'Domestik' }})</div>
                <div><span class="text-slate-400">Kelamin:</span> {{ $appointment->pet->gender }}</div>
                <div><span class="text-slate-400">Pemilik (Klien):</span> <span class="font-semibold text-slate-800">{{ $appointment->user->name }}</span></div>
                <div><span class="text-slate-400">WhatsApp / Telp:</span> {{ $appointment->user->phone ?? '-' }}</div>
                <div><span class="text-slate-400">Alamat:</span> {{ $appointment->user->address ?? '-' }}</div>
            </div>
        </div>

        <!-- Service & Schedule Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-stethoscope text-sky-600"></i>
                <span>Layanan & Waktu Kunjungan</span>
            </h3>
            <div class="text-xs space-y-1.5 text-slate-600">
                <div><span class="text-slate-400">Layanan M3:</span> <span class="font-bold text-slate-800">{{ $appointment->service->name }}</span></div>
                <div><span class="text-slate-400">Kategori:</span> {{ $appointment->service->category }}</div>
                <div><span class="text-slate-400">Tarif Standar:</span> <span class="font-bold text-emerald-700">Rp {{ number_format($appointment->service->price, 0, ',', '.') }}</span></div>
                <div><span class="text-slate-400">Tanggal:</span> <span class="font-bold text-slate-900">{{ Carbon\Carbon::parse($appointment->appointment_date)->isoFormat('dddd, D MMMM Y') }}</span></div>
                <div><span class="text-slate-400">Waktu:</span> <span class="font-bold text-slate-900">{{ substr($appointment->appointment_time, 0, 5) }} WIB</span></div>
            </div>
        </div>

        <!-- Status & Workflow (T2 Tracking) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-timeline text-indigo-600"></i>
                <span>Status Alur Dua Role (T2)</span>
            </h3>
            <div class="text-xs space-y-1.5 text-slate-600">
                <div><span class="text-slate-400">Status Saat Ini:</span> <span class="font-bold">{{ $appointment->status_label }}</span></div>
                <div><span class="text-slate-400">Diproses Oleh:</span> {{ $appointment->handler ? $appointment->handler->name : 'Belum Ditindaklanjuti' }}</div>
                <div><span class="text-slate-400">Waktu Respon:</span> {{ $appointment->handled_at ? $appointment->handled_at->isoFormat('D MMM Y, HH:mm') . ' WIB' : '-' }}</div>
                @if($appointment->staff_notes)
                    <div class="mt-2 p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="font-semibold block text-slate-700">Catatan Staf:</span>
                        {{ $appointment->staff_notes }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Keluhan Pasien -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-2">
        <h3 class="font-bold text-slate-900 text-sm">Keluhan / Alasan Kunjungan (T1 Input Role 2)</h3>
        <p class="text-sm text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100">{{ $appointment->complaint }}</p>
    </div>

    <!-- T2 Action Panel: Khusus Role 1 (Staf & Dokter Klinik) -->
    @if(Auth::user()->isStaff())
        <div class="bg-white p-6 rounded-2xl border-2 border-brand-200 shadow-sm space-y-4">
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">AKSI STAF KLINIK (T2)</span>
                <h3 class="font-bold text-slate-900 text-base">Alur Persetujuan, Penolakan, atau Reschedule</h3>
            </div>

            @if($appointment->status === 'pending')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <!-- Approve Form -->
                    <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50 space-y-3">
                        <h4 class="font-bold text-emerald-900 text-sm"><i class="fa-solid fa-circle-check mr-1 text-emerald-600"></i> Setujui Kunjungan</h4>
                        <p class="text-xs text-slate-600">Konfirmasi bahwa klinik siap melayani pasien pada jadwal tersebut.</p>
                        <form action="{{ route('appointments.approve', $appointment) }}" method="POST">
                            @csrf
                            <input type="text" name="staff_notes" placeholder="Catatan instruksi (opsional)..." class="w-full px-3 py-1.5 rounded-lg border border-emerald-300 text-xs mb-2">
                            <button type="submit" class="w-full py-2 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700">
                                Setujui Jadwal
                            </button>
                        </form>
                    </div>

                    <!-- Reschedule Form -->
                    <div class="p-4 rounded-xl border border-sky-200 bg-sky-50/50 space-y-3">
                        <h4 class="font-bold text-sky-900 text-sm"><i class="fa-solid fa-clock-rotate-left mr-1 text-sky-600"></i> Jadwalkan Ulang</h4>
                        <p class="text-xs text-slate-600">Ubah tanggal/jam jika slot kuota penuh.</p>
                        <form action="{{ route('appointments.reschedule', $appointment) }}" method="POST" class="space-y-2">
                            @csrf
                            <input type="date" name="new_date" value="{{ $appointment->appointment_date }}" min="{{ date('Y-m-d') }}" required class="w-full px-3 py-1 rounded-lg border border-sky-300 text-xs">
                            <input type="time" name="new_time" value="{{ $appointment->appointment_time }}" required class="w-full px-3 py-1 rounded-lg border border-sky-300 text-xs">
                            <input type="text" name="staff_notes" required placeholder="Alasan pemindahan jadwal..." class="w-full px-3 py-1 rounded-lg border border-sky-300 text-xs">
                            <button type="submit" class="w-full py-2 rounded-lg text-xs font-bold text-white bg-sky-600 hover:bg-sky-700">
                                Jadwalkan Ulang
                            </button>
                        </form>
                    </div>

                    <!-- Reject Form -->
                    <div class="p-4 rounded-xl border border-rose-200 bg-rose-50/50 space-y-3">
                        <h4 class="font-bold text-rose-900 text-sm"><i class="fa-solid fa-circle-xmark mr-1 text-rose-600"></i> Tolak Kunjungan</h4>
                        <p class="text-xs text-slate-600">Tolak dengan alasan yang tercatat jelas.</p>
                        <form action="{{ route('appointments.reject', $appointment) }}" method="POST">
                            @csrf
                            <textarea name="staff_notes" required rows="3" placeholder="Alasan penolakan pengajuan..." class="w-full px-3 py-1.5 rounded-lg border border-rose-300 text-xs mb-2"></textarea>
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak janji temu ini?');" class="w-full py-2 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-700">
                                Tolak Janji Temu
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($appointment->status === 'approved')
                <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                    <div>
                        <span class="font-bold text-emerald-900 text-sm">Janji Temu Siap Dilayani!</span>
                        <p class="text-xs text-emerald-700">Lanjutkan ke pencatatan rekam medis dan resep obat ketika pasien datang.</p>
                    </div>
                    <a href="{{ route('medical-records.create', ['appointment_id' => $appointment->id]) }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm flex items-center space-x-1">
                        <i class="fa-solid fa-file-waveform"></i>
                        <span>Buat Rekam Medis (T3)</span>
                    </a>
                </div>
            @elseif($appointment->status === 'completed')
                <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-200 text-xs text-indigo-900 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-sm block">Kunjungan Telah Selesai Diperiksa</span>
                        <span class="text-indigo-700">Rekam medis dan tagihan telah terbit secara otomatis.</span>
                    </div>
                    @if($appointment->medicalRecord)
                        <a href="{{ route('medical-records.show', $appointment->medicalRecord) }}" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700">
                            Lihat Rekam Medis &rarr;
                        </a>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- Cancel Action by Owner (Role 2) if still pending -->
    @if(Auth::user()->isPetOwner() && in_array($appointment->status, ['pending', 'rescheduled']))
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
            <span class="text-xs text-slate-500">Ingin membatalkan janji temu ini sebelum dikonfirmasi klinik?</span>
            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?');">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold text-rose-700 hover:bg-rose-100 border border-rose-300">
                    Batalkan Pengajuan
                </button>
            </form>
        </div>
    @endif

</div>
@endsection
