<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'species',
        'breed',
        'gender',
        'birth_date',
        'weight_kg',
        'color',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight_kg' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
