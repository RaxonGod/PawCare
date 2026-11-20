@extends('layouts.app')

@section('title', 'Rincian Tagihan: ' . $invoice->invoice_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header & Action -->
    <div class="sm:flex sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-sm font-bold text-slate-700">{{ $invoice->invoice_code }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $invoice->status_badge }}">
                    {{ $invoice->status_label }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-2">Faktur Tagihan Kunjungan Pasien</h1>
            <p class="text-sm text-slate-500">
                Penerima Tagihan: <span class="font-semibold text-slate-800">{{ $invoice->user->name }}</span> &bull; Terbit: {{ $invoice->created_at->isoFormat('D MMMM Y, HH:mm') }} WIB
            </p>
        </div>

        <div class="mt-4 sm:mt-0 flex space-x-2">
            <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                &larr; Daftar Tagihan
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200">
                <i class="fa-solid fa-print mr-1"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Client and Reference Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2 text-xs">
            <span class="font-bold text-slate-900 uppercase tracking-wider block text-[11px] text-slate-400">Penerima Tagihan (Klien)</span>
            <div class="text-slate-700 font-semibold text-sm">{{ $invoice->user->name }}</div>
            <div class="text-slate-500">{{ $invoice->user->phone ?? 'Tidak ada no telepon' }}</div>
            <div class="text-slate-500">{{ $invoice->user->email }}</div>
            <div class="text-slate-500">{{ $invoice->user->address ?? '-' }}</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2 text-xs">
            <span class="font-bold text-slate-900 uppercase tracking-wider block text-[11px] text-slate-400">Referensi Tindakan Medis</span>
            @if($invoice->medicalRecord)
                <div><span class="text-slate-400">No. Rekam Medis:</span> <a href="{{ route('medical-records.show', $invoice->medicalRecord) }}" class="font-mono font-bold text-brand-700 hover:underline">{{ $invoice->medicalRecord->record_code }}</a></div>
                <div><span class="text-slate-400">Pasien Hewan:</span> <span class="font-bold text-slate-800">{{ $invoice->medicalRecord->pet->name }} ({{ $invoice->medicalRecord->pet->species }})</span></div>
                <div><span class="text-slate-400">Dokter:</span> {{ $invoice->medicalRecord->vet->name }}</div>
            @else
                <span class="text-slate-400">Tagihan langsung tanpa rekam medis terkait.</span>
            @endif
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2 text-xs">
            <span class="font-bold text-slate-900 uppercase tracking-wider block text-[11px] text-slate-400">Status Pembayaran</span>
            <div class="text-lg font-black {{ $invoice->payment_status === 'paid' ? 'text-emerald-700' : 'text-rose-600' }}">
                {{ $invoice->status_label }}
            </div>
            @if($invoice->payment_status === 'paid')
                <div class="text-emerald-700"><i class="fa-solid fa-circle-check mr-1"></i> Dibayar pada {{ $invoice->paid_at?->isoFormat('D MMM Y, HH:mm') }} WIB</div>
            @else
                <div class="text-rose-600"><i class="fa-solid fa-clock mr-1"></i> Jatuh tempo: {{ Carbon\Carbon::parse($invoice->due_date)->isoFormat('D MMM Y') }}</div>
            @endif
        </div>
    </div>

    <!-- Itemized Billing Table -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Rincian Komponen Tagihan</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Deskripsi Item</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-right">Tarif Satuan</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <!-- Service Fee Row -->
                    <tr>
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 block">Jasa Layanan Medis & Pemeriksaan Dokter</span>
                            <span class="text-xs text-slate-400">
                                @if($invoice->appointment)
                                    Layanan: {{ $invoice->appointment->service->name }} ({{ $invoice->appointment->service->category }})
                                @else
                                    Konsultasi, pemeriksaan fisik umum, dan tindakan veteriner.
                                @endif
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">1 Sesi</td>
                        <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($invoice->service_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">Rp {{ number_format($invoice->service_amount, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Prescribed Medicines Rows -->
                    @if($invoice->medicalRecord && $invoice->medicalRecord->details->count() > 0)
                        @foreach($invoice->medicalRecord->details as $medItem)
                            <tr>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $medItem->medicine->name }}</span>
                                    <span class="text-xs text-slate-400 font-mono">{{ $medItem->medicine->code }} &bull; Aturan: {{ $medItem->dosage_instructions }}</span>
                                </td>
                                <td class="py-3 px-4 text-center font-mono">{{ $medItem->quantity }} {{ $medItem->medicine->unit }}</td>
                                <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($medItem->unit_price, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">Rp {{ number_format($medItem->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot class="bg-slate-50 font-bold text-slate-800 border-t border-slate-200">
                    <tr>
                        <td colspan="3" class="py-2.5 px-4 text-right">Total Biaya Layanan:</td>
                        <td class="py-2.5 px-4 text-right font-mono">Rp {{ number_format($invoice->service_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="py-2.5 px-4 text-right">Total Biaya Obat (M1):</td>
                        <td class="py-2.5 px-4 text-right font-mono">Rp {{ number_format($invoice->medicine_amount, 0, ',', '.') }}</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                        <tr>
                            <td colspan="3" class="py-2.5 px-4 text-right text-emerald-700">Potongan Diskon:</td>
                            <td class="py-2.5 px-4 text-right font-mono text-emerald-700">-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="text-base text-brand-900 bg-brand-50/80">
                        <td colspan="3" class="py-3.5 px-4 text-right uppercase tracking-wider font-black">Grand Total Pembayaran:</td>
                        <td class="py-3.5 px-4 text-right font-mono font-black text-brand-900 text-lg">
                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- T5: Process Payment Form (Role 1 Staff Kasir) -->
    @if(Auth::user()->isStaff() && $invoice->payment_status === 'unpaid')
        <div class="bg-white p-6 rounded-2xl border-2 border-emerald-300 shadow-sm space-y-4">
            <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800">KASIR / ROLE 1 (T5)</span>
                <h3 class="font-bold text-slate-900 text-base">Proses Pembayaran Kasir</h3>
            </div>
            <p class="text-xs text-slate-500">Pilih metode pembayaran dan masukkan nominal uang yang diterima dari klien.</p>

            <form action="{{ route('invoices.pay', $invoice) }}" method="POST" class="space-y-4 pt-2">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="payment_method" class="block text-xs font-semibold text-slate-700 mb-1">
                            Metode Pembayaran <span class="text-rose-500">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white focus:ring-2 focus:ring-brand-500">
                            <option value="cash">Tunai / Cash</option>
                            <option value="qris">QRIS Dinamis</option>
                            <option value="transfer_bank">Transfer Bank</option>
                            <option value="debit">Kartu Debit</option>
                        </select>
                    </div>

                    <div>
                        <label for="amount_paid" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nominal Diterima (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="amount_paid" id="amount_paid" 
                            value="{{ old('amount_paid', (int)$invoice->total_amount) }}" 
                            min="{{ (int)$invoice->total_amount }}" step="1000" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm font-mono font-bold text-slate-900 focus:ring-2 focus:ring-brand-500">
                        <span class="text-[11px] text-slate-400 block mt-0.5">Min: Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Staf</label>
                        <input type="text" name="notes" id="notes" placeholder="No. referensi transfer / catatan..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" onclick="return confirm('Proses pelunasan tagihan ini?');" class="px-6 py-2.5 rounded-xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm flex items-center space-x-2">
                        <i class="fa-solid fa-money-bill-wave"></i>
                        <span>Konfirmasi Pembayaran Lunas (T5)</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Payment Receipts History -->
    @if($invoice->payments->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center space-x-2">
                <i class="fa-solid fa-receipt text-emerald-600"></i>
                <span>Riwayat Bukti Pembayaran Kasir (T5)</span>
            </h3>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach($invoice->payments as $pay)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-mono font-bold text-slate-800">{{ $pay->payment_code }}</span> &bull;
                            <span class="font-semibold text-brand-700">{{ $pay->method_label }}</span>
                            <span class="text-slate-400 block">Diterima oleh {{ $pay->receiver->name }} pada {{ $pay->payment_date->isoFormat('D MMMM Y, HH:mm') }} WIB</span>
                            @if($pay->notes)
                                <span class="text-slate-500 italic block mt-0.5">"{{ $pay->notes }}"</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold text-emerald-700 text-sm block">Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}</span>
                            @if($pay->change_amount > 0)
                                <span class="text-[11px] text-slate-500 block">Kembalian: Rp {{ number_format($pay->change_amount, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
