@extends('layouts.app')

@section('title', 'Daftar Akun Pemilik Hewan')

@section('content')
<div class="max-w-xl mx-auto my-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 sm:p-8 bg-sky-50 border-b border-sky-100 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-600 text-white flex items-center justify-center text-3xl mx-auto shadow-md mb-3">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-900">Pendaftaran Akun Baru</h2>
            <p class="text-sm text-slate-600 mt-1">Daftar sebagai Pemilik Hewan (Role 2) untuk mengelola hewan dan janji temu klinik</p>
        </div>

        <div class="p-6 sm:p-8">
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                        placeholder="Contoh: Helga Dianova Ardhani">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                            placeholder="nama@email.com">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                            placeholder="0812xxxxxxxx">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal <span class="text-rose-500">*</span></label>
                    <textarea name="address" id="address" rows="2" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                        placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota">{{ old('address') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" id="password" required minlength="6"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                            placeholder="Min. 6 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Ulangi Kata Sandi <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm transition"
                            placeholder="Ulangi kata sandi">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl text-white font-semibold bg-brand-600 hover:bg-brand-700 transition shadow-sm flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-user-check"></i>
                        <span>Daftarkan Akun Pemilik Hewan</span>
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-slate-600 mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700 underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
