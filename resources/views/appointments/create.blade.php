@extends('layouts.app')

@section('title', 'Ajukan Janji Temu Kunjungan (T1)')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-sky-100 text-sky-800">TRANSAKSI T1</span>
                <span class="text-xs text-slate-400">&bull; Diajukan oleh Pemilik Hewan (Role 2)</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Form Pengajuan Kunjungan Klinik</h1>
            <p class="text-sm text-slate-500">Pilih pasien hewan peliharaan, jenis tindakan layanan, dan tanggal kedatangan.</p>
        </div>
        <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('appointments.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Pilih Hewan Peliharaan -->
            <div>
                <label for="pet_id" class="block text-sm font-semibold text-slate-700 mb-1">
                    Pilih Hewan Peliharaan <span class="text-rose-500">*</span>
                </label>
                @if($pets->count() > 0)
                    <select name="pet_id" id="pet_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                        <option value="">-- Pilih Hewan yang Akan Diperiksa --</option>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ (old('pet_id') == $pet->id || request('pet_id') == $pet->id) ? 'selected' : '' }}>
                                {{ $pet->name }} ({{ $pet->species }} - {{ $pet->breed ?? 'Campuran' }}) {{ Auth::user()->isStaff() ? ' - Pemilik: ' . $pet->owner->name : '' }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-800">
                        <p class="font-semibold">Anda belum mendaftarkan hewan peliharaan.</p>
                        <a href="{{ route('pets.create') }}" class="mt-2 inline-block font-bold underline text-amber-900">
                            + Daftarkan hewan peliharaan terlebih dahulu &rarr;
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pilih Layanan Klinik (M3) -->
            <div>
                <label for="service_id" class="block text-sm font-semibold text-slate-700 mb-1">
                    Layanan Klinik yang Dibutuhkan (Data Master M3) <span class="text-rose-500">*</span>
                </label>
                <select name="service_id" id="service_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                    <option value="">-- Pilih Layanan Medis --</option>
                    @foreach($services as $srv)
                        <option value="{{ $srv->id }}" {{ old('service_id') == $srv->id ? 'selected' : '' }}>
                            {{ $srv->name }} ({{ $srv->category }}) - Rp {{ number_format($srv->price, 0, ',', '.') }} [~{{ $srv->estimated_duration }} mnt]
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal & Waktu Kunjungan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="appointment_date" class="block text-sm font-semibold text-slate-700 mb-1">
                        Rencana Tanggal Kunjungan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="appointment_date" id="appointment_date" value="{{ old('appointment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="appointment_time" class="block text-sm font-semibold text-slate-700 mb-1">
                        Waktu / Jam Kedatangan <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="appointment_time" id="appointment_time" value="{{ old('appointment_time', '10:00') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                    <span class="text-[11px] text-slate-400 mt-1 block">Jam operasional klinik: 08:30 - 20:00 WIB</span>
                </div>
            </div>

            <!-- Keluhan / Gejala -->
            <div>
                <label for="complaint" class="block text-sm font-semibold text-slate-700 mb-1">
                    Keluhan Hewan / Catatan Gejala <span class="text-rose-500">*</span>
                </label>
                <textarea name="complaint" id="complaint" rows="3" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="Jelaskan kondisi hewan, sudah berapa hari gejala muncul, apakah muntah, lesu, diare, atau nafsu makan berkurang...">{{ old('complaint') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-2">
                <a href="{{ route('appointments.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm flex items-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Pengajuan Kunjungan (T1)</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
