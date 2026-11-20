<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_code',
        'appointment_id',
        'medical_record_id',
        'user_id',
        'service_amount',
        'medicine_amount',
        'discount_amount',
        'total_amount',
        'payment_status',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'service_amount' => 'decimal:2',
        'medicine_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'unpaid' => 'bg-rose-100 text-rose-800 border-rose-300',
            'cancelled' => 'bg-slate-100 text-slate-800 border-slate-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'unpaid' => 'Belum Lunas',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->payment_status),
        };
    }
}
