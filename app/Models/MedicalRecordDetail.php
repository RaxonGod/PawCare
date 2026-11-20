<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecordDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id',
        'medicine_id',
        'quantity',
        'unit_price',
        'subtotal',
        'dosage_instructions',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
