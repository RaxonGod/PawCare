<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_code',
        'user_id',
        'pet_id',
        'service_id',
        'appointment_date',
        'appointment_time',
        'complaint',
        'status',
        'handled_by',
        'staff_notes',
        'handled_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'handled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
            'rescheduled' => 'bg-sky-100 text-sky-800 border-sky-300',
            'completed' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'cancelled' => 'bg-slate-100 text-slate-800 border-slate-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'rescheduled' => 'Jadwal Ulang',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}
