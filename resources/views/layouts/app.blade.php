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
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen antialiased">

@auth
    <!-- ========================================== -->
    <!-- AUTHENTICATED LAYOUT (SIDEBAR + TOPBAR)   -->
    <!-- ========================================== -->
    <div class="min-h-screen flex">

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden transition-opacity"></div>

        <!-- Left Sidebar Navigation -->
        <aside id="mainSidebar" class="fixed top-0 bottom-0 left-0 w-72 bg-white border-r border-slate-200 z-50 flex flex-col justify-between transition-transform duration-300 -translate-x-full lg:translate-x-0">
            <!-- Sidebar Header / Logo -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <span class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center text-xl shadow-sm">
                        <i class="fa-solid fa-paw"></i>
                    </span>
                    <div>
                        <span class="font-bold text-lg text-slate-900 tracking-tight block leading-tight">PawCare</span>
                        <span class="text-[11px] font-semibold text-brand-600 tracking-wider uppercase block">Veterinary Clinic</span>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-slate-600 p-1.5">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- User Profile Box in Sidebar -->
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/70">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-800 font-bold flex items-center justify-center text-sm border border-brand-200 shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden flex-1">
                        <span class="font-bold text-sm text-slate-900 block truncate leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-slate-500 block truncate">{{ Auth::user()->email }}</span>
                    </div>
                </div>

                <!-- Role Badge -->
                <div class="mt-3">
                    @if(Auth::user()->isStaff())
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 w-full justify-center">
                            <i class="fa-solid fa-user-doctor mr-1.5"></i> Role 1: Dokter & Staf
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-sky-100 text-sky-800 border border-sky-300 w-full justify-center">
                            <i class="fa-solid fa-user mr-1.5"></i> Role 2: Pemilik Hewan
                        </span>
                    @endif
                </div>
            </div>

            <!-- Scrollable Navigation Items -->
            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-6">

                <!-- Group 1: Menu Utama -->
                <div class="space-y-1">
                    <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Menu Utama</span>

                    <a href="{{ route('dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fa-solid fa-chart-pie w-5 mr-3 text-base {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('pets.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('pets.*') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fa-solid fa-dog w-5 mr-3 text-base {{ request()->routeIs('pets.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>{{ Auth::user()->isStaff() ? 'Data Pasien Hewan' : 'Hewan Peliharaan' }}</span>
                    </a>
                </div>

                <!-- Group 2: Pelayanan & Transaksi -->
                <div class="space-y-1">
                    <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Pelayanan & Kunjungan</span>

                    <a href="{{ route('appointments.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('appointments.*') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fa-solid fa-calendar-check w-5 mr-3 text-base {{ request()->routeIs('appointments.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Janji Temu (T1 & T2)</span>
                    </a>

                    <a href="{{ route('medical-records.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('medical-records.*') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fa-solid fa-file-waveform w-5 mr-3 text-base {{ request()->routeIs('medical-records.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Rekam Medis (T3)</span>
                    </a>

                    <a href="{{ route('invoices.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('invoices.*') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <i class="fa-solid fa-receipt w-5 mr-3 text-base {{ request()->routeIs('invoices.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Tagihan & Kasir (T5)</span>
                    </a>
                </div>

                <!-- Group 3: Khusus Role 1 (Staf & Dokter Klinik) -->
                @if(Auth::user()->isStaff())
                    <div class="space-y-1">
                        <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Data Master (CRUD)</span>

                        <a href="{{ route('medicines.index') }}" class="flex items-center px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('medicines.*') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-pills w-5 mr-3 text-emerald-600"></i>
                            <span>M1: Obat & Medis</span>
                        </a>

                        <a href="{{ route('medicine-categories.index') }}" class="flex items-center px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('medicine-categories.*') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-tags w-5 mr-3 text-indigo-600"></i>
                            <span>M2: Kategori Obat</span>
                        </a>

                        <a href="{{ route('services.index') }}" class="flex items-center px-3.5 py-2 rounded-xl text-sm font-semibold transition {{ request()->routeIs('services.*') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-stethoscope w-5 mr-3 text-sky-600"></i>
                            <span>M3: Layanan & Tarif</span>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <span class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Inventaris & Analitik</span>

                        <a href="{{ route('medicine-restocks.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('medicine-restocks.*') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-boxes-packing w-5 mr-3 text-base text-amber-600"></i>
                            <span>T4: Restock Obat</span>
                        </a>

                        <a href="{{ route('reports.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('reports.*') ? 'bg-brand-50 text-brand-700 font-bold border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-chart-line w-5 mr-3 text-base text-violet-600"></i>
                            <span>Laporan Rekap (T5)</span>
                        </a>
                    </div>
                @endif

            </div>

            <!-- Sidebar Footer: Logout Button -->
            <div class="p-4 border-t border-slate-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar (logout)?');" 
                        class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl text-sm font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Right Side: Header + Content Body -->
        <div class="flex-1 lg:pl-72 flex flex-col min-h-screen">

            <!-- Sleek Top Header -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs h-16 flex items-center px-4 sm:px-8 justify-between">
                <!-- Left: Hamburger toggle (mobile) & Breadcrumb -->
                <div class="flex items-center space-x-3">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 leading-tight">@yield('title', 'PawCare Clinic')</h2>
                        <span class="text-xs text-slate-400 hidden sm:block">Sistem Informasi Manajemen Klinik Hewan</span>
                    </div>
                </div>

                <!-- Right: Quick Action Buttons & Status -->
                <div class="flex items-center space-x-3">
                    @if(Auth::user()->isStaff())
                        <a href="{{ route('medical-records.create') }}" class="hidden sm:inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-50 text-brand-700 hover:bg-brand-100 border border-brand-200 transition">
                            <i class="fa-solid fa-file-circle-plus"></i>
                            <span>+ Rekam Medis (T3)</span>
                        </a>
                        <a href="{{ route('home') }}" target="_blank" class="text-xs font-semibold text-slate-500 hover:text-brand-600 hidden md:block">
                            <i class="fa-solid fa-arrow-up-right-from-square mr-1"></i> Lihat Web Publik
                        </a>
                    @else
                        <a href="{{ route('appointments.create') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-xs transition">
                            <i class="fa-solid fa-calendar-plus"></i>
                            <span>Ajukan Kunjungan (T1)</span>
                        </a>
                    @endif

                    <!-- User Initials Pill -->
                    <div class="flex items-center space-x-2 border-l border-slate-200 pl-3">
                        <span class="text-xs font-semibold text-slate-700 hidden sm:inline">{{ Auth::user()->name }}</span>
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs border border-slate-200">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Notification Messages -->
            <div class="max-w-7xl mx-auto px-4 sm:px-8 mt-4 w-full">
                @if(session('success'))
                    <div class="flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-xs" role="alert">
                        <i class="fa-solid fa-circle-check text-xl mr-3 text-emerald-600"></i>
                        <div class="text-sm font-medium flex-1">{{ session('success') }}</div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-center p-4 mb-4 text-rose-800 rounded-xl bg-rose-50 border border-rose-200 shadow-xs" role="alert">
                        <i class="fa-solid fa-circle-exclamation text-xl mr-3 text-rose-600"></i>
                        <div class="text-sm font-medium flex-1">{{ session('error') }}</div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="flex items-center p-4 mb-4 text-amber-800 rounded-xl bg-amber-50 border border-amber-200 shadow-xs" role="alert">
                        <i class="fa-solid fa-triangle-exclamation text-xl mr-3 text-amber-600"></i>
                        <div class="text-sm font-medium flex-1">{{ session('warning') }}</div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 mb-4 text-rose-800 rounded-xl bg-rose-50 border border-rose-200 shadow-xs">
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

            <!-- Page Content Body -->
            <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-8 py-6 w-full">
                @yield('content')
            </main>

            <!-- Bottom Unified Footer -->
            <footer class="bg-white border-t border-slate-200 py-4 px-4 sm:px-8 mt-auto">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-slate-800">PawCare Clinic</span>
                        <span>&bull;</span>
                        <span>Sistem Manajemen Klinik Hewan</span>
                    </div>
                    <div class="text-slate-400">
                        Tugas UAS Pengembangan Perangkat Lunak Web (Framework) &bull; Laravel {{ app()->version() }} &bull; PostgreSQL
                    </div>
                </div>
            </footer>

        </div>
    </div>

@else
    <!-- ========================================== -->
    <!-- GUEST / PUBLIC LAYOUT (TOPBAR + LANDING)  -->
    <!-- ========================================== -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 text-brand-700 hover:text-brand-800 transition">
                        <span class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center text-xl shadow-sm">
                            <i class="fa-solid fa-paw"></i>
                        </span>
                        <div>
                            <span class="font-bold text-lg text-slate-900 tracking-tight">PawCare</span>
                            <span class="text-xs block text-brand-600 font-semibold tracking-wider uppercase">Clinic</span>
                        </div>
                    </a>

                    <div class="hidden md:flex md:items-center md:space-x-1 ml-8">
                        <a href="{{ route('home') }}" class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-brand-50 text-brand-700">
                            <i class="fa-solid fa-house-chimney-medical mr-1.5"></i> Katalog Layanan Publik
                        </a>
                    </div>
                </div>

                <!-- Right: Login & Register -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 rounded-xl transition">
                        <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-xs transition">
                        <i class="fa-solid fa-user-plus mr-1.5"></i> Daftar Akun
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash notifications for Guest -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-xs">
                <i class="fa-solid fa-circle-check text-xl mr-3 text-emerald-600"></i>
                <div class="text-sm font-medium flex-1">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center p-4 mb-4 text-rose-800 rounded-xl bg-rose-50 border border-rose-200 shadow-xs">
                <i class="fa-solid fa-circle-exclamation text-xl mr-3 text-rose-600"></i>
                <div class="text-sm font-medium flex-1">{{ session('error') }}</div>
            </div>
        @endif
        @if(session('warning'))
            <div class="flex items-center p-4 mb-4 text-amber-800 rounded-xl bg-amber-50 border border-amber-200 shadow-xs">
                <i class="fa-solid fa-triangle-exclamation text-xl mr-3 text-amber-600"></i>
                <div class="text-sm font-medium flex-1">{{ session('warning') }}</div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        @yield('content')
    </main>

    <!-- Guest Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center sm:flex sm:justify-between text-xs text-slate-500">
            <div>
                <span class="font-bold text-slate-800">PawCare Clinic</span> &bull; Sistem Manajemen Klinik Hewan
            </div>
            <div class="mt-2 sm:mt-0 text-slate-400">
                Tugas UAS Pengembangan Perangkat Lunak Web (Framework) &bull; Laravel & PostgreSQL
            </div>
        </div>
    </footer>
@endauth

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
