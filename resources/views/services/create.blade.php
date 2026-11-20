@extends('layouts.app')

@section('title', 'Tambah Layanan Klinik Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Layanan Klinik (M3)</h1>
            <p class="text-sm text-slate-500">Daftarkan paket pemeriksaan atau tindakan medis baru.</p>
        </div>
        <a href="{{ route('services.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('services.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="block text-sm font-semibold text-slate-700 mb-1">Kode Layanan <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $suggestedCode) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-mono text-sm focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="category" class="block text-sm font-semibold text-slate-700 mb-1">Kategori Layanan <span class="text-rose-500">*</span></label>
                    <input type="text" name="category" id="category" value="{{ old('category', 'Konsultasi & Pemeriksaan') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Konsultasi, Vaksinasi, Tindakan & Bedah, dll.">
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Layanan <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="Contoh: Scaling Gigi & Dental Care">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-semibold text-slate-700 mb-1">Tarif Layanan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="1000" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Contoh: 150000">
                </div>

                <div>
                    <label for="estimated_duration" class="block text-sm font-semibold text-slate-700 mb-1">Estimasi Durasi (Menit) <span class="text-rose-500">*</span></label>
                    <input type="number" name="estimated_duration" id="estimated_duration" value="{{ old('estimated_duration', 30) }}" min="5" step="5" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Layanan</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="Rincian prosedur yang dilakukan dokter atau fasilitas yang didapatkan...">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded text-brand-600">
                <label for="is_active" class="text-sm font-semibold text-slate-700">Aktifkan layanan ini di katalog publik</label>
            </div>

            <div class="pt-4 flex justify-end space-x-2">
                <a href="{{ route('services.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                    Simpan Layanan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
