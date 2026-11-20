<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - PawCare Clinic</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ Auth::check() ? route('dashboard') : route('home') }}" class="flex items-center space-x-2 text-brand-700 hover:text-brand-800 transition">
                        <span class="w-10 h-10 rounded-xl bg-brand-500 text-white flex items-center justify-center text-xl shadow-sm">
                            <i class="fa-solid fa-paw"></i>
                        </span>
                        <div>
                            <span class="font-bold text-lg text-slate-900 tracking-tight">PawCare</span>
                            <span class="text-xs block text-brand-600 font-semibold tracking-wider uppercase">Clinic</span>
                        </div>
                    </a>

                    <!-- Nav Items (Desktop) -->
                    <div class="hidden md:flex md:items-center md:space-x-1 ml-6">
                        @guest
                            <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('home') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <i class="fa-solid fa-house-chimney-medical mr-1.5"></i> Katalog Layanan
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <i class="fa-solid fa-chart-pie mr-1.5"></i> Dashboard
                            </a>

                            <!-- Hewan Peliharaan -->
                            <a href="{{ route('pets.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('pets.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <i class="fa-solid fa-dog mr-1.5"></i> {{ Auth::user()->isStaff() ? 'Data Pasien Hewan' : 'Hewan Peliharaan' }}
                            </a>

                            <!-- Janji Temu / Kunjungan -->
                            <a href="{{ route('appointments.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('appointments.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <i class="fa-solid fa-calendar-check mr-1.5"></i> Janji Temu
                            </a>

                            <!-- Rekam Medis -->
                            <a href="{{ route('medical-records.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('medical-records.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <i class="fa-solid fa-file-waveform mr-1.5"></i> Rekam Medis
                            </a>

                            <!-- Tagihan & Pembayaran -->
                            <a href="{{ route('invoices.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('invoices.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <i class="fa-solid fa-receipt mr-1.5"></i> Tagihan
                            </a>

                            <!-- Menu Khusus Role 1 (Staf & Dokter Klinik) -->
                            @if(Auth::user()->isStaff())
                                <div class="relative group">
                                    <button class="px-3 py-2 text-sm font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 flex items-center space-x-1">
                                        <i class="fa-solid fa-database mr-1.5 text-brand-600"></i>
                                        <span>Data Master</span>
                                        <i class="fa-solid fa-chevron-down text-xs ml-1 text-slate-400 group-hover:rotate-180 transition-transform"></i>
                                    </button>
                                    <div class="absolute left-0 mt-1 w-56 bg-white border border-slate-200 rounded-xl shadow-lg py-2 hidden group-hover:block z-50">
                                        <a href="{{ route('medicines.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-700">
                                            <i class="fa-solid fa-pills mr-2 text-emerald-600 w-5"></i> M1: Obat & Medis
                                        </a>
                                        <a href="{{ route('medicine-categories.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-700">
                                            <i class="fa-solid fa-tags mr-2 text-indigo-600 w-5"></i> M2: Kategori Obat
                                        </a>
                                        <a href="{{ route('services.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-700">
                                            <i class="fa-solid fa-stethoscope mr-2 text-sky-600 w-5"></i> M3: Layanan Klinik
                                        </a>
                                    </div>
                                </div>

                                <a href="{{ route('medicine-restocks.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('medicine-restocks.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    <i class="fa-solid fa-boxes-packing mr-1.5 text-amber-600"></i> T4: Restock
                                </a>

                                <a href="{{ route('reports.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('reports.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                    <i class="fa-solid fa-chart-line mr-1.5 text-violet-600"></i> Laporan Rekap
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>

                <!-- Right Side: Auth status & profile -->
                <div class="flex items-center space-x-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 rounded-lg transition">
                            <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-lg shadow-sm transition">
                            <i class="fa-solid fa-user-plus mr-1.5"></i> Daftar Akun
                        </a>
                    @else
                        <!-- Role Badge -->
                        <div class="hidden sm:flex items-center">
                            @if(Auth::user()->isStaff())
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <i class="fa-solid fa-user-doctor mr-1"></i> Role 1: {{ Auth::user()->role->display_name }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-sky-100 text-sky-800 border border-sky-300">
                                    <i class="fa-solid fa-user mr-1"></i> Role 2: {{ Auth::user()->role->display_name }}
                                </span>
                            @endif
                        </div>

                        <!-- User Profile Info & Logout -->
                        <div class="flex items-center space-x-3">
                            <div class="text-right hidden sm:block">
                                <span class="text-sm font-semibold text-slate-800 block leading-tight">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-slate-500 block">{{ Auth::user()->email }}</span>
                            </div>

                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar?');" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Logout">
                                    <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check text-xl mr-3 text-emerald-600"></i>
                <div class="text-sm font-medium flex-1">{{ session('success') }}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center p-4 mb-4 text-rose-800 rounded-xl bg-rose-50 border border-rose-200 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-exclamation text-xl mr-3 text-rose-600"></i>
                <div class="text-sm font-medium flex-1">{{ session('error') }}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if(session('warning'))
            <div class="flex items-center p-4 mb-4 text-amber-800 rounded-xl bg-amber-50 border border-amber-200 shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation text-xl mr-3 text-amber-600"></i>
                <div class="text-sm font-medium flex-1">{{ session('warning') }}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 text-rose-800 rounded-xl bg-rose-50 border border-rose-200 shadow-sm">
                <div class="flex items-center mb-2">
                    <i class="fa-solid fa-circle-exclamation text-lg mr-2 text-rose-600"></i>
                    <span class="text-sm font-bold">Terjadi kesalahan pada input data:</span>
                </div>
                <ul class="list-disc list-inside text-sm space-y-1 ml-4 text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Body -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Unified Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:flex sm:justify-between sm:items-center text-sm text-slate-500">
            <div class="flex items-center justify-center sm:justify-start space-x-2">
                <span class="font-bold text-slate-800">PawCare Clinic</span>
                <span>&bull;</span>
                <span>Sistem Manajemen Klinik Hewan</span>
            </div>
            <div class="mt-2 sm:mt-0 text-xs text-slate-400">
                Tugas UAS Pengembangan Perangkat Lunak Web (Framework) &bull; Laravel {{ app()->version() }} &bull; PostgreSQL
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
