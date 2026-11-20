@extends('layouts.app')

@section('title', 'Rincian Rekam Medis: ' . $medicalRecord->record_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-sm font-bold text-brand-700">{{ $medicalRecord->record_code }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                    Selesai Diperiksa
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-2">Pasien: {{ $medicalRecord->pet->name }} ({{ $medicalRecord->pet->species }})</h1>
            <p class="text-sm text-slate-500">
                Pemeriksaan pada {{ Carbon\Carbon::parse($medicalRecord->examination_date)->isoFormat('dddd, D MMMM Y') }} &bull; Dokter: {{ $medicalRecord->vet->name }}
            </p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('medical-records.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                &larr; Riwayat Rekam Medis
            </a>
            @if($medicalRecord->invoice)
                <a href="{{ route('invoices.show', $medicalRecord->invoice) }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 shadow-xs">
                    <i class="fa-solid fa-receipt mr-1"></i> Buka Tagihan (T5)
                </a>
            @endif
        </div>
    </div>

    <!-- Clinical Findings & Vitals -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Suhu Tubuh</span>
            <span class="text-2xl font-black text-slate-800 mt-1 block">
                {{ $medicalRecord->temperature ? $medicalRecord->temperature . ' °C' : '-' }}
            </span>
            <span class="text-xs text-slate-400">Normal kucing/anjing: 38 - 39.2 °C</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Berat Badan Pasien</span>
            <span class="text-2xl font-black text-slate-800 mt-1 block">
                {{ $medicalRecord->weight ? $medicalRecord->weight . ' kg' : '-' }}
            </span>
            <span class="text-xs text-slate-400">Saat pemeriksaan dilakukan</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-400 font-semibold uppercase block">Detak Jantung</span>
            <span class="text-2xl font-black text-slate-800 mt-1 block">
                {{ $medicalRecord->heart_rate ? $medicalRecord->heart_rate . ' bpm' : '-' }}
            </span>
            <span class="text-xs text-slate-400">Frekuensi denyut nadi</span>
        </div>
    </div>

    <!-- Diagnosis & Treatment Summary -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider text-slate-400">Hasil Diagnosa & Tindakan Medis (Header T3)</h3>

        <div class="space-y-3 text-sm">
            <div>
                <span class="font-bold text-slate-800 block">Gejala Klinis:</span>
                <p class="text-slate-600 bg-slate-50 p-3 rounded-xl mt-1">{{ $medicalRecord->symptoms }}</p>
            </div>

            <div>
                <span class="font-bold text-slate-800 block">Diagnosa Dokter:</span>
                <p class="text-brand-900 font-semibold bg-brand-50 p-3 rounded-xl mt-1 border border-brand-200">{{ $medicalRecord->diagnosis }}</p>
            </div>

            <div>
                <span class="font-bold text-slate-800 block">Tindakan Medis Dilakukan:</span>
                <p class="text-slate-600 bg-slate-50 p-3 rounded-xl mt-1">{{ $medicalRecord->treatment_actions }}</p>
            </div>

            @if($medicalRecord->notes)
                <div>
                    <span class="font-bold text-slate-800 block">Instruksi Dokter untuk Pemilik:</span>
                    <p class="text-slate-600 bg-amber-50 p-3 rounded-xl mt-1 border border-amber-200">{{ $medicalRecord->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- T3 Detail Items: Medicines Dispensed & Stock Reduction -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center space-x-2">
                    <i class="fa-solid fa-pills text-emerald-600"></i>
                    <span>Detail Pemakaian Obat Farmasi (Detail T3)</span>
                </h3>
                <p class="text-xs text-slate-500">Daftar obat yang diresepkan dan mengurangi stok obat M1.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                {{ $medicalRecord->details->count() }} Jenis Obat
            </span>
        </div>

        @if($medicalRecord->details->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Kode & Nama Obat (M1)</th>
                            <th class="py-2.5 px-3">Kategori (M2)</th>
                            <th class="py-2.5 px-3 text-center">Jumlah</th>
                            <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                            <th class="py-2.5 px-3 text-right">Subtotal</th>
                            <th class="py-2.5 px-3">Aturan Pakai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($medicalRecord->details as $item)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2.5 px-3">
                                    <span class="font-mono text-xs font-bold text-slate-500 block">{{ $item->medicine->code }}</span>
                                    <span class="font-semibold text-slate-900">{{ $item->medicine->name }}</span>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-700">
                                        {{ $item->medicine->category->name }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-800">
                                    {{ $item->quantity }} {{ $item->medicine->unit }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono">
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                                <td class="py-2.5 px-3 text-xs text-brand-700 font-medium">
                                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> {{ $item->dosage_instructions }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold text-slate-800 border-t border-slate-200">
                        <tr>
                            <td colspan="4" class="py-2.5 px-3 text-right">Total Biaya Obat:</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-brand-700">
                                Rp {{ number_format($medicalRecord->total_medicine_fee, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="py-2.5 px-3 text-right">Jasa Layanan Dokter:</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800">
                                Rp {{ number_format($medicalRecord->service_fee, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                        <tr class="text-base text-brand-800 bg-brand-50/60">
                            <td colspan="4" class="py-3 px-3 text-right uppercase tracking-wider">Grand Total Pemeriksaan:</td>
                            <td class="py-3 px-3 text-right font-mono font-black text-brand-800">
                                Rp {{ number_format($medicalRecord->total_amount, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <p class="text-xs text-slate-400 text-center py-4">Pemeriksaan ini tidak disertai resep obat.</p>
        @endif
    </div>

</div>
@endsection
