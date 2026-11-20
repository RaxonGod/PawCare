@extends('layouts.app')

@section('title', 'Profil Pasien: ' . $pet->name)

@section('content')
<div class="space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">{{ $pet->species }}</span>
                <span class="text-xs text-slate-500">Pemilik: {{ $pet->owner->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">{{ $pet->name }}</h1>
            <p class="text-sm text-slate-500">{{ $pet->breed ?? 'Ras Domestik' }} &bull; {{ $pet->gender }} &bull; {{ $pet->color ?? 'Warna Alami' }}</p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('appointments.create', ['pet_id' => $pet->id]) }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-xs">
                <i class="fa-solid fa-calendar-plus mr-1"></i> Buat Janji Temu (T1)
            </a>
            <a href="{{ route('pets.edit', $pet) }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
            </a>
            <a href="{{ route('pets.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Health Metrics Card -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Berat Badan</span>
            <span class="text-2xl font-black text-slate-800 mt-1 block">
                {{ $pet->weight_kg ? $pet->weight_kg . ' kg' : '-' }}
            </span>
            <span class="text-xs text-slate-400">Pengukuran terakhir</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Usia Perkiraan</span>
            <span class="text-xl font-bold text-slate-800 mt-1 block">
                @if($pet->birth_date)
                    {{ $pet->birth_date->diff(now())->format('%y thn %m bln') }}
                @else
                    -
                @endif
            </span>
            <span class="text-xs text-slate-400">{{ $pet->birth_date ? 'Lahir: ' . $pet->birth_date->format('d/m/Y') : 'Tanggal lahir belum dicatat' }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Kontak Pemilik</span>
            <span class="text-base font-bold text-slate-800 mt-1 block">{{ $pet->owner->name }}</span>
            <span class="text-xs text-slate-500 block">{{ $pet->owner->phone ?? $pet->owner->email }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Total Riwayat Periksa</span>
            <span class="text-2xl font-black text-brand-700 mt-1 block">
                {{ $pet->medicalRecords->count() }} Kali
            </span>
            <span class="text-xs text-slate-400">Tercatat di rekam medis</span>
        </div>
    </div>

    @if($pet->notes)
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
            <span class="font-bold block mb-0.5"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Catatan Khusus & Alergi:</span>
            {{ $pet->notes }}
        </div>
    @endif

    <!-- Medical Records (T3) for this pet -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-base flex items-center space-x-2">
                <i class="fa-solid fa-file-waveform text-brand-600"></i>
                <span>Riwayat Rekam Medis (T3)</span>
            </h3>
            @if(Auth::user()->isStaff())
                <a href="{{ route('medical-records.create', ['pet_id' => $pet->id]) }}" class="text-xs font-bold text-brand-600 hover:underline">
                    + Input Rekam Medis Baru
                </a>
            @endif
        </div>

        @if($pet->medicalRecords->count() > 0)
            <div class="divide-y divide-slate-100 text-sm">
                @foreach($pet->medicalRecords as $rec)
                    <div class="py-3.5 space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-mono font-bold text-brand-700">{{ $rec->record_code }}</span> &bull;
                                <span class="text-xs text-slate-500">{{ Carbon\Carbon::parse($rec->examination_date)->isoFormat('dddd, D MMMM Y') }}</span>
                                <span class="text-xs text-slate-400 ml-2">Diperiksa oleh: {{ $rec->vet->name }}</span>
                            </div>
                            <a href="{{ route('medical-records.show', $rec) }}" class="text-xs font-semibold text-brand-600 hover:underline">
                                Rincian Lengkap &rarr;
                            </a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs bg-slate-50 p-3 rounded-xl">
                            <div>
                                <span class="font-semibold text-slate-700 block">Diagnosa:</span>
                                <span class="text-slate-900">{{ $rec->diagnosis }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-slate-700 block">Tindakan Medis:</span>
                                <span class="text-slate-900">{{ $rec->treatment_actions }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat rekam medis untuk hewan ini.</p>
        @endif
    </div>

</div>
@endsection
