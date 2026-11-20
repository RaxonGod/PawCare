<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    // T5: Daftar Tagihan Kunjungan
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        $search = $request->query('search');
        $date = $request->query('date');

        $query = Invoice::with(['user', 'appointment.pet', 'medicalRecord.pet', 'payments']);

        if ($user->isPetOwner()) {
            $query->where('user_id', $user->id);
        }

        if ($status) {
            $query->where('payment_status', $status);
        }

        if ($date) {
            $query->whereDate('due_date', $date);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_code', 'ilike', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'ilike', "%{$search}%"));
            });
        }

        $invoices = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('invoices.index', compact('invoices', 'status', 'search', 'date'));
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();
        if ($user->isPetOwner() && $invoice->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses melihat tagihan ini.');
        }

        $invoice->load([
            'user',
            'appointment.pet',
            'medicalRecord.pet',
            'medicalRecord.details.medicine',
            'medicalRecord.vet',
            'payments.receiver',
        ]);

        return view('invoices.show', compact('invoice'));
    }
}
