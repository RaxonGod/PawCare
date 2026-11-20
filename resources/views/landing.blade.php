@extends('layouts.app')

@section('title', 'Katalog Layanan & Profil Klinik Hewan')

@section('content')
<div class="space-y-10">

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-brand-700 to-teal-800 rounded-3xl text-white p-8 sm:p-12 shadow-md overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/30 text-brand-100 border border-brand-400/40 mb-4">
                <i class="fa-solid fa-shield-cat mr-1.5"></i> Perawatan Hewan Terpercaya & Higienis
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                Kesehatan & Kebahagiaan Hewan Kesayangan Anda.
            </h1>
            <p class="mt-4 text-brand-100 text-base sm:text-lg">
                PawCare Clinic menghadirkan layanan medis veteriner komprehensif mulai dari pemeriksaan umum, vaksinasi bersertifikat, tindakan operasi steril, dental care, hingga rawat inap modern.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                @guest
                    <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl font-bold bg-white text-brand-800 hover:bg-brand-50 shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>Daftar & Ajukan Kunjungan</span>
                    </a>
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl font-bold bg-brand-600/60 hover:bg-brand-600 border border-brand-400/50 text-white transition flex items-center space-x-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Masuk Akun</span>
                    </a>
                @else
                    <a href="{{ route('appointments.create') }}" class="px-6 py-3 rounded-xl font-bold bg-white text-brand-800 hover:bg-brand-50 shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>Ajukan Kunjungan Sekarang</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl font-bold bg-brand-600/60 hover:bg-brand-600 border border-brand-400/50 text-white transition flex items-center space-x-2">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @endguest
            </div>
        </div>

        <div class="absolute right-0 bottom-0 opacity-15 pointer-events-none translate-x-12 translate-y-12">
            <i class="fa-solid fa-paw text-[280px]"></i>
        </div>
    </div>

    <!-- Quick Statistics Bar -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-xl">
                <i class="fa-solid fa-stethoscope"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-800">{{ $stats['services_count'] }}</span>
                <span class="text-xs text-slate-500 block">Layanan Medis Aktif</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-xl">
                <i class="fa-solid fa-dog"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-800">{{ $stats['pets_served'] }}+</span>
                <span class="text-xs text-slate-500 block">Pasien Hewan Terdaftar</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl">
                <i class="fa-solid fa-pills"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-800">{{ $stats['medicines_available'] }}+</span>
                <span class="text-xs text-slate-500 block">Varian Obat & Suplemen</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-800">{{ $stats['vets_count'] }}</span>
                <span class="text-xs text-slate-500 block">Dokter & Staf Siaga</span>
            </div>
        </div>
    </div>

    <!-- M3: Public Catalog of Services (Ketentuan: Guest dapat melihat katalog layanan secara read-only) -->
    <div id="katalog" class="space-y-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Katalog Terbuka (M3)</span>
                <h2 class="text-2xl font-bold text-slate-900 mt-1">Daftar Layanan & Tarif Klinik</h2>
                <p class="text-sm text-slate-500">Transparansi tarif dan estimasi waktu tindakan bagi para pemilik hewan.</p>
            </div>

            <!-- Search & Filter Form (Public) -->
            <form action="{{ route('home') }}" method="GET" class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama layanan..."
                        class="pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 w-48 sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                </div>

                <select name="category" onchange="this.form.submit()"
                    class="py-2 px-3 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                @if($search || $category)
                    <a href="{{ route('home') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($services as $srv)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-md transition p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                                {{ $srv->category }}
                            </span>
                            <span class="text-xs font-mono text-slate-400 font-semibold">{{ $srv->code }}</span>
                        </div>

                        <h3 class="font-bold text-lg text-slate-900 leading-snug">{{ $srv->name }}</h3>
                        <p class="text-sm text-slate-500 mt-2 line-clamp-3">{{ $srv->description ?? 'Layanan medis profesional oleh tim dokter hewan berpengalaman.' }}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs text-slate-400 block">Tarif Layanan</span>
                            <span class="text-xl font-black text-brand-700">Rp {{ number_format($srv->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-400 block"><i class="fa-regular fa-clock mr-1"></i> Durasi</span>
                            <span class="text-xs font-semibold text-slate-700">{{ $srv->estimated_duration }} Menit</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-400">
                    <i class="fa-solid fa-folder-open text-4xl mb-3"></i>
                    <p class="text-sm font-medium">Tidak ada layanan klinik yang cocok dengan pencarian.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $services->links() }}
        </div>
    </div>

    <!-- Operating Hours & Clinic Location -->
    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h4 class="font-bold text-slate-900">Jam Operasional</h4>
                <p class="text-sm text-slate-600">
                    Senin - Sabtu: 08:30 - 20:00 WIB<br>
                    Minggu / Hari Libur: 09:00 - 17:00 WIB<br>
                    <span class="text-emerald-600 font-semibold text-xs mt-1 block">Unit Rawat Inap & UGD 24 Jam Siaga</span>
                </p>
            </div>

            <div class="space-y-3">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <h4 class="font-bold text-slate-900">Lokasi Klinik</h4>
                <p class="text-sm text-slate-600">
                    PawCare Central Hospital<br>
                    Jl. Flamboyan Raya No. 12, Kebayoran Baru<br>
                    Jakarta Selatan 12140
                </p>
            </div>

            <div class="space-y-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <h4 class="font-bold text-slate-900">Kontak & Darurat</h4>
                <p class="text-sm text-slate-600">
                    Telepon: (021) 7890-1234<br>
                    WhatsApp Care: 0812-3456-7890<br>
                    Email: info@pawcare.com
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
