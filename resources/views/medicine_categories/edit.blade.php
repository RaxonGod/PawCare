@extends('layouts.app')

@section('title', 'Ubah Kategori Obat')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Ubah Kategori: {{ $medicineCategory->name }}</h1>
            <p class="text-sm text-slate-500">Perbarui rincian kategori obat.</p>
        </div>
        <a href="{{ route('medicine-categories.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('medicine-categories.update', $medicineCategory) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $medicineCategory->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Kategori</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">{{ old('description', $medicineCategory->description) }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-2">
                <a href="{{ route('medicine-categories.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
