<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_code',
        'appointment_id',
        'pet_id',
        'vet_id',
        'examination_date',
        'temperature',
        'weight',
        'heart_rate',
        'symptoms',
        'diagnosis',
        'treatment_actions',
        'service_fee',
        'total_medicine_fee',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'examination_date' => 'date',
        'temperature' => 'decimal:1',
        'weight' => 'decimal:2',
        'heart_rate' => 'integer',
        'service_fee' => 'decimal:2',
        'total_medicine_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function vet()
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function details()
    {
        return $this->hasMany(MedicalRecordDetail::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
