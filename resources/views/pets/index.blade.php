@extends('layouts.app')

@section('title', 'Data Hewan Peliharaan')

@section('content')
<div class="space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">DATA PASIEN HEWAN</span>
                <span class="text-xs text-slate-400">&bull; Mendukung Alur Janji Temu & Rekam Medis</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">
                {{ Auth::user()->isStaff() ? 'Seluruh Pasien Hewan Klinik' : 'Hewan Peliharaan Saya' }}
            </h1>
            <p class="text-sm text-slate-500">Kelola profil kucing, anjing, kelinci, ras, riwayat kunjungan, dan kondisi fisik.</p>
        </div>

        <div class="mt-4 sm:mt-0">
            <a href="{{ route('pets.create') }}" class="px-4 py-2.5 rounded-xl font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-xs transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Daftarkan Hewan Baru</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('pets.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama hewan, ras, atau pemilik..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <select name="species" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">
                <option value="">Semua Spesies</option>
                <option value="Kucing" {{ $species === 'Kucing' ? 'selected' : '' }}>Kucing</option>
                <option value="Anjing" {{ $species === 'Anjing' ? 'selected' : '' }}>Anjing</option>
                <option value="Kelinci" {{ $species === 'Kelinci' ? 'selected' : '' }}>Kelinci</option>
                <option value="Burung" {{ $species === 'Burung' ? 'selected' : '' }}>Burung</option>
                <option value="Hamster" {{ $species === 'Hamster' ? 'selected' : '' }}>Hamster</option>
                <option value="Lainnya" {{ $species === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>

            @if($search || $species)
                <a href="{{ route('pets.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Pets Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Nama Hewan</th>
                        <th class="py-3.5 px-4">Spesies & Ras</th>
                        <th class="py-3.5 px-4 text-center">Kelamin</th>
                        <th class="py-3.5 px-4">Pemilik (User Role 2)</th>
                        <th class="py-3.5 px-4 text-center">Berat (kg)</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pets as $pet)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <a href="{{ route('pets.show', $pet) }}" class="text-brand-700 hover:underline">
                                    {{ $pet->name }}
                                </a>
                                <span class="text-xs text-slate-400 block font-normal">{{ $pet->color ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $pet->species === 'Kucing' ? 'bg-amber-100 text-amber-800' : ($pet->species === 'Anjing' ? 'bg-sky-100 text-sky-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $pet->species }}
                                </span>
                                <span class="text-xs text-slate-500 ml-1">{{ $pet->breed ?? 'Campuran' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-xs">
                                <i class="fa-solid {{ $pet->gender === 'Jantan' ? 'fa-mars text-blue-500' : 'fa-venus text-pink-500' }}"></i>
                                {{ $pet->gender }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800 block">{{ $pet->owner->name }}</span>
                                <span class="text-xs text-slate-400">{{ $pet->owner->phone ?? $pet->owner->email }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono">
                                {{ $pet->weight_kg ? $pet->weight_kg . ' kg' : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('pets.show', $pet) }}" class="p-1.5 text-slate-500 hover:text-brand-600 hover:bg-slate-100 rounded-lg" title="Lihat">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pets.edit', $pet) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg" title="Ubah">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('pets.destroy', $pet) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data hewan ini? Sistem akan mencegah jika hewan memiliki riwayat kunjungan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-dog text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Belum ada data hewan terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $pets->links() }}
        </div>
    </div>

</div>
@endsection
