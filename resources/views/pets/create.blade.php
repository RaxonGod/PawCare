@extends('layouts.app')

@section('title', 'Daftarkan Hewan Peliharaan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Daftarkan Hewan Baru</h1>
            <p class="text-sm text-slate-500">Lengkapi data pasien untuk mempermudah pemeriksaan dan riwayat medis.</p>
        </div>
        <a href="{{ route('pets.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('pets.store') }}" method="POST" class="space-y-4">
            @csrf

            @if(Auth::user()->isStaff())
                <div>
                    <label for="user_id" class="block text-sm font-semibold text-slate-700 mb-1">Pemilik Hewan (Klien) <span class="text-rose-500">*</span></label>
                    <select name="user_id" id="user_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                        <option value="">-- Pilih Pemilik Hewan --</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ old('user_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }} ({{ $owner->email }} &bull; {{ $owner->phone }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Panggilan Hewan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Contoh: Milo">
                </div>

                <div>
                    <label for="species" class="block text-sm font-semibold text-slate-700 mb-1">Spesies Hewan <span class="text-rose-500">*</span></label>
                    <select name="species" id="species" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                        <option value="">-- Pilih Spesies --</option>
                        <option value="Kucing" {{ old('species') === 'Kucing' ? 'selected' : '' }}>Kucing</option>
                        <option value="Anjing" {{ old('species') === 'Anjing' ? 'selected' : '' }}>Anjing</option>
                        <option value="Kelinci" {{ old('species') === 'Kelinci' ? 'selected' : '' }}>Kelinci</option>
                        <option value="Burung" {{ old('species') === 'Burung' ? 'selected' : '' }}>Burung</option>
                        <option value="Hamster" {{ old('species') === 'Hamster' ? 'selected' : '' }}>Hamster</option>
                        <option value="Lainnya" {{ old('species') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="breed" class="block text-sm font-semibold text-slate-700 mb-1">Ras / Varietas</label>
                    <input type="text" name="breed" id="breed" value="{{ old('breed') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Persia, Golden, Rex, Domestik">
                </div>

                <div>
                    <label for="gender" class="block text-sm font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" id="gender" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                        <option value="Jantan" {{ old('gender') === 'Jantan' ? 'selected' : '' }}>Jantan</option>
                        <option value="Betina" {{ old('gender') === 'Betina' ? 'selected' : '' }}>Betina</option>
                    </select>
                </div>

                <div>
                    <label for="weight_kg" class="block text-sm font-semibold text-slate-700 mb-1">Berat Badan (kg)</label>
                    <input type="number" name="weight_kg" id="weight_kg" value="{{ old('weight_kg') }}" step="0.05" min="0" max="150"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Contoh: 4.25">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="birth_date" class="block text-sm font-semibold text-slate-700 mb-1">Perkiraan Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" max="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="color" class="block text-sm font-semibold text-slate-700 mb-1">Warna Bulu / Ciri Khusus</label>
                    <input type="text" name="color" id="color" value="{{ old('color') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Contoh: Belang tiga / Hitam putih">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1">Catatan Tambahan / Alergi</label>
                <textarea name="notes" id="notes" rows="2"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="Riwayat vaksin masa lalu, kebiasaan, alergi makanan khusus...">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-2">
                <a href="{{ route('pets.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                    Simpan Data Hewan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
