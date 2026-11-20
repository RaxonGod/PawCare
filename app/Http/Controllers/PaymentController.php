<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    // T5: Pembayaran Tagihan Kunjungan (Role 1 Staf / Kasir)
    public function store(Request $request, Invoice $invoice)
    {
        if ($invoice->payment_status === 'paid') {
            return back()->with('error', 'Tagihan ini sudah berstatus lunas sebelumnya.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:cash,qris,transfer_bank,debit'],
            'amount_paid' => ['required', 'numeric', 'min:' . $invoice->total_amount],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran yang dipilih tidak valid.',
            'amount_paid.required' => 'Nominal pembayaran wajib diisi.',
            'amount_paid.min' => 'Nominal pembayaran kurang dari total tagihan (' . number_format($invoice->total_amount, 0, ',', '.') . ').',
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            $yearMonth = Carbon::now()->format('Ym');
            $count = Payment::where('payment_code', 'like', "PAY-{$yearMonth}-%")->count();
            $payCode = 'PAY-' . $yearMonth . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            $amountPaid = (float) $validated['amount_paid'];
            $changeAmount = $amountPaid - (float)$invoice->total_amount;

            // 1. Simpan Transaksi Pembayaran (Pelaku T5: Auth::id())
            Payment::create([
                'invoice_id' => $invoice->id,
                'payment_code' => $payCode,
                'payment_method' => $validated['payment_method'],
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'payment_date' => Carbon::now(), // Waktu kejadian T5
                'received_by' => Auth::id(), // Catat pelaku kasir / staf Role 1
                'notes' => $validated['notes'] ?? null,
            ]);

            // 2. Perbarui status tagihan menjadi lunas (paid)
            $invoice->update([
                'payment_status' => 'paid',
                'paid_at' => Carbon::now(),
            ]);
        });

        return back()->with('success', "Pembayaran tagihan {$invoice->invoice_code} berhasil diproses! Status tagihan kini LUNAS.");
    }
}
