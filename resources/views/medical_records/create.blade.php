@extends('layouts.app')

@section('title', 'Pencatatan Rekam Medis & Resep Obat (T3 Header-Detail)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-indigo-100 text-indigo-800">TRANSAKSI T3 (HEADER-DETAIL)</span>
                <span class="text-xs text-slate-400">&bull; Disimpan Atomik DB::transaction(), Mengurangi Stok Obat M1</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Form Pemeriksaan Medis & Peresepan Obat</h1>
            <p class="text-sm text-slate-500">Mencatat hasil anamnesa, tanda vital, tindakan dokter, serta pemakaian obat farmasi.</p>
        </div>
        <a href="{{ route('medical-records.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
            &larr; Kembali
        </a>
    </div>

    @if($selectedAppointment)
        <div class="p-4 rounded-2xl bg-brand-50 border border-brand-200 text-xs text-brand-900 flex items-center justify-between">
            <div>
                <span class="font-bold text-sm block">Terhubung ke Janji Temu: {{ $selectedAppointment->appointment_code }}</span>
                <span>Layanan: {{ $selectedAppointment->service->name }} &bull; Keluhan awal: "{{ $selectedAppointment->complaint }}"</span>
            </div>
            <span class="px-2.5 py-1 rounded bg-white text-brand-800 font-bold border border-brand-300">Jadwal Terdaftar</span>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('medical-records.store') }}" method="POST" id="medicalRecordForm" class="space-y-6">
            @csrf

            @if($selectedAppointment)
                <input type="hidden" name="appointment_id" value="{{ $selectedAppointment->id }}">
            @endif

            <!-- 1. HEADER DATA: Pasien, Tanggal, Dokter -->
            <div class="border-b border-slate-200 pb-5 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                    <i class="fa-solid fa-file-lines text-indigo-600"></i>
                    <span>Bagian 1: Header Rekam Medis</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="pet_id" class="block text-sm font-semibold text-slate-700 mb-1">
                            Pasien Hewan <span class="text-rose-500">*</span>
                        </label>
                        <select name="pet_id" id="pet_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 bg-white">
                            <option value="">-- Pilih Hewan Pasien --</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ (old('pet_id') == $pet->id || ($selectedPet && $selectedPet->id == $pet->id)) ? 'selected' : '' }}>
                                    {{ $pet->name }} ({{ $pet->species }} - {{ $pet->breed ?? 'Domestik' }}) &bull; Pemilik: {{ $pet->owner->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="examination_date" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tanggal Pemeriksaan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="examination_date" id="examination_date" value="{{ old('examination_date', date('Y-m-d')) }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <!-- Tanda Vital -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                    <div>
                        <label for="temperature" class="block text-xs font-semibold text-slate-600 mb-1">Suhu Tubuh (&deg;C)</label>
                        <input type="number" step="0.1" name="temperature" id="temperature" value="{{ old('temperature', 38.5) }}" min="30" max="45"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Contoh: 38.5">
                    </div>

                    <div>
                        <label for="weight" class="block text-xs font-semibold text-slate-600 mb-1">Berat Saat Ini (kg)</label>
                        <input type="number" step="0.05" name="weight" id="weight" value="{{ old('weight') }}" min="0" max="200"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Contoh: 4.10">
                    </div>

                    <div>
                        <label for="heart_rate" class="block text-xs font-semibold text-slate-600 mb-1">Detak Jantung (bpm)</label>
                        <input type="number" name="heart_rate" id="heart_rate" value="{{ old('heart_rate') }}" min="30" max="300"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500" placeholder="Contoh: 140">
                    </div>
                </div>

                <div>
                    <label for="symptoms" class="block text-sm font-semibold text-slate-700 mb-1">Gejala Klinis & Anamnesa <span class="text-rose-500">*</span></label>
                    <textarea name="symptoms" id="symptoms" rows="2" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                        placeholder="Muntah, diare, lesu, nafsu makan turun, kemerahan kulit...">{{ old('symptoms', $selectedAppointment?->complaint) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="diagnosis" class="block text-sm font-semibold text-slate-700 mb-1">Diagnosa Dokter <span class="text-rose-500">*</span></label>
                        <textarea name="diagnosis" id="diagnosis" rows="2" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                            placeholder="Contoh: Gastritis Akut / Dermatofitosis / dsb.">{{ old('diagnosis') }}</textarea>
                    </div>

                    <div>
                        <label for="treatment_actions" class="block text-sm font-semibold text-slate-700 mb-1">Tindakan Medis Dilakukan <span class="text-rose-500">*</span></label>
                        <textarea name="treatment_actions" id="treatment_actions" rows="2" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                            placeholder="Injeksi vitamin, pembersihan telinga, infus, operasi...">{{ old('treatment_actions') }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="service_fee" class="block text-sm font-semibold text-slate-700 mb-1">
                        Biaya Jasa Tindakan / Layanan (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="service_fee" id="service_fee" 
                        value="{{ old('service_fee', $selectedAppointment ? (int)$selectedAppointment->service->price : 75000) }}" 
                        min="0" step="1000" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <!-- 2. DETAIL DATA: Peresepan Obat M1 (Header-Detail yang mengurangi stok obat M1) -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                            <i class="fa-solid fa-pills text-emerald-600"></i>
                            <span>Bagian 2: Detail Peresepan Obat (M1)</span>
                        </h3>
                        <p class="text-xs text-slate-500">Obat yang ditambahkan akan otomatis memotong stok fisik M1 saat rekam medis disimpan.</p>
                    </div>
                    <button type="button" onclick="addMedicineRow()" class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 flex items-center space-x-1 shadow-xs">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Baris Obat</span>
                    </button>
                </div>

                <!-- Container Baris Obat -->
                <div id="medicineRowsContainer" class="space-y-3">
                    <!-- Default first row -->
                    <div class="medicine-row p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <div class="flex-1 w-full sm:w-auto">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Obat (M1)</label>
                            <select name="medicines[0][medicine_id]" class="medicine-select w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white focus:ring-2 focus:ring-brand-500">
                                <option value="">-- Tanpa Obat / Pilih Obat --</option>
                                @foreach($medicines as $med)
                                    <option value="{{ $med->id }}" data-price="{{ $med->selling_price }}" data-stock="{{ $med->stock }}" data-unit="{{ $med->unit }}">
                                        {{ $med->name }} (Stok: {{ $med->stock }} {{ $med->unit }}) - Rp {{ number_format($med->selling_price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full sm:w-24">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah</label>
                            <input type="number" name="medicines[0][quantity]" value="1" min="1" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-center">
                        </div>

                        <div class="flex-1 w-full sm:w-auto">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Aturan Pakai / Dosis</label>
                            <input type="text" name="medicines[0][dosage_instructions]" placeholder="Misal: 2 x sehari 1/2 tablet sesudah makan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
                        </div>

                        <div class="pt-5 sm:pt-4">
                            <button type="button" onclick="removeMedicineRow(this)" class="p-2 text-slate-400 hover:text-rose-600 transition" title="Hapus Baris">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Khusus & Submit -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1">Petunjuk Perawatan di Rumah untuk Pemilik</label>
                <textarea name="notes" id="notes" rows="2"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500"
                    placeholder="Instruksi pola makan, istirahat, tanggal kontrol kembali...">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-2 border-t border-slate-100">
                <a href="{{ route('medical-records.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-brand-600 hover:bg-brand-700 shadow-sm flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Rekam Medis, Potong Stok Obat & Terbitkan Tagihan (T3)</span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    let rowIndex = 1;
    const medicinesData = @json($medicines);

    function addMedicineRow() {
        const container = document.getElementById('medicineRowsContainer');
        const div = document.createElement('div');
        div.className = 'medicine-row p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 flex flex-col sm:flex-row items-start sm:items-center gap-3';
        
        let optionsHtml = '<option value="">-- Pilih Obat --</option>';
        medicinesData.forEach(med => {
            optionsHtml += `<option value="${med.id}" data-price="${med.selling_price}" data-stock="${med.stock}" data-unit="${med.unit}">${med.name} (Stok: ${med.stock} ${med.unit}) - Rp ${Number(med.selling_price).toLocaleString('id-ID')}</option>`;
        });

        div.innerHTML = `
            <div class="flex-1 w-full sm:w-auto">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih Obat (M1)</label>
                <select name="medicines[${rowIndex}][medicine_id]" class="medicine-select w-full px-3 py-2 rounded-lg border border-slate-300 text-xs bg-white focus:ring-2 focus:ring-brand-500">
                    ${optionsHtml}
                </select>
            </div>
            <div class="w-full sm:w-24">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah</label>
                <input type="number" name="medicines[${rowIndex}][quantity]" value="1" min="1" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-center">
            </div>
            <div class="flex-1 w-full sm:w-auto">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Aturan Pakai / Dosis</label>
                <input type="text" name="medicines[${rowIndex}][dosage_instructions]" placeholder="Misal: 2 x sehari 1 tablet sesudah makan" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
            </div>
            <div class="pt-5 sm:pt-4">
                <button type="button" onclick="removeMedicineRow(this)" class="p-2 text-slate-400 hover:text-rose-600 transition" title="Hapus Baris">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        `;
        container.appendChild(div);
        rowIndex++;
    }

    function removeMedicineRow(btn) {
        const rows = document.querySelectorAll('.medicine-row');
        if (rows.length > 1) {
            btn.closest('.medicine-row').remove();
        } else {
            alert('Minimal harus ada 1 baris input obat (bisa dikosongkan jika tidak ada resep obat).');
        }
    }
</script>
@endpush
@endsection
