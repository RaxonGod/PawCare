<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'payment_code',
        'payment_method',
        'amount_paid',
        'change_amount',
        'payment_date',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Tunai / Cash',
            'qris' => 'QRIS',
            'transfer_bank' => 'Transfer Bank',
            'debit' => 'Kartu Debit',
            default => strtoupper($this->payment_method),
        };
    }
}
