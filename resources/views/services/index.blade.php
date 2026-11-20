@extends('layouts.app')

@section('title', 'Master Data Layanan Klinik (M3)')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-sky-100 text-sky-800">DATA MASTER M3</span>
                <span class="text-xs text-slate-400">&bull; Dapat Dilihat Publik oleh Guest</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Layanan Medis & Tindakan Klinik</h1>
            <p class="text-sm text-slate-500">Kelola tarif konsultasi, vaksinasi, bedah/operasi, scaling gigi, dan rawat inap.</p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('services.create') }}" class="px-4 py-2.5 rounded-xl font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-xs transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Layanan Baru</span>
            </a>
            <a href="{{ route('home') }}" target="_blank" class="px-4 py-2.5 rounded-xl font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition flex items-center space-x-2 text-sm">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Preview Katalog Publik</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('services.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode layanan..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <select name="category" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">
                <option value="">Semua Kategori Layanan</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">
                <option value="">Semua Status</option>
                <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ $status === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            @if($search || $category || $status !== null)
                <a href="{{ route('services.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Nama Layanan</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4 text-right">Tarif Jasa (Rp)</th>
                        <th class="py-3.5 px-4 text-center">Estimasi Durasi</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($services as $srv)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-700">{{ $srv->code }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                <a href="{{ route('services.show', $srv) }}" class="text-brand-700 hover:underline">
                                    {{ $srv->name }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $srv->category }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($srv->price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center text-xs">
                                <i class="fa-regular fa-clock mr-1 text-slate-400"></i> {{ $srv->estimated_duration }} Menit
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($srv->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('services.show', $srv) }}" class="p-1.5 text-slate-500 hover:text-brand-600 hover:bg-slate-100 rounded-lg" title="Lihat">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('services.edit', $srv) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg" title="Ubah">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <!-- Pencegahan Hapus Master jika sudah dipakai transaksi (T1 Appointment) -->
                                    <form action="{{ route('services.destroy', $srv) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini? Sistem akan mencegah penghapusan jika layanan telah digunakan dalam riwayat janji temu.');">
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
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-stethoscope text-3xl mb-2 text-slate-300"></i>
                                <p class="text-sm font-medium">Belum ada data layanan klinik.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $services->links() }}
        </div>
    </div>

</div>
@endsection
