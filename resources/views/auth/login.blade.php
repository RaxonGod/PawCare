@extends('layouts.app')

@section('title', 'Masuk Akun')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 sm:p-8 bg-brand-50 border-b border-brand-100 text-center">
            <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center text-3xl mx-auto shadow-md mb-3">
                <i class="fa-solid fa-paw"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">Masuk ke PawCare Clinic</h2>
            <p class="text-sm text-slate-600 mt-1">Silakan masuk menggunakan akun terdaftar</p>
        </div>

        <div class="p-6 sm:p-8">
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                            placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-brand-600 border-slate-300 focus:ring-brand-500">
                        <span class="ml-2">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl text-white font-semibold bg-brand-600 hover:bg-brand-700 transition shadow-sm flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block mb-3 text-center">Akun Demo Praktikum UAS</span>
                <div class="grid grid-cols-1 gap-2 text-xs">
                    <button type="button" onclick="fillLogin('drh.sarah@pawcare.com', 'password123')"
                        class="p-2.5 text-left rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 transition flex items-center justify-between">
                        <div>
                            <span class="font-bold block">Role 1: drh. Sarah Amelia (Staf / Dokter)</span>
                            <span class="text-slate-600">drh.sarah@pawcare.com &bull; password123</span>
                        </div>
                        <span class="text-emerald-700 font-semibold px-2 py-1 bg-white rounded shadow-xs">Pilih</span>
                    </button>

                    <button type="button" onclick="fillLogin('budi.santoso@gmail.com', 'password123')"
                        class="p-2.5 text-left rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-900 border border-sky-200 transition flex items-center justify-between">
                        <div>
                            <span class="font-bold block">Role 2: Budi Santoso (Pemilik Hewan)</span>
                            <span class="text-slate-600">budi.santoso@gmail.com &bull; password123</span>
                        </div>
                        <span class="text-sky-700 font-semibold px-2 py-1 bg-white rounded shadow-xs">Pilih</span>
                    </button>
                </div>
            </div>

            <p class="text-center text-sm text-slate-600 mt-6">
                Belum memiliki akun pemilik hewan?
                <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-700 underline">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillLogin(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endpush
@endsection
