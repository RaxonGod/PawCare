<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'unit',
        'stock',
        'min_stock',
        'purchase_price',
        'selling_price',
        'description',
    ];

    protected $casts = [
        'stock' => 'integer',
        'min_stock' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(MedicineCategory::class, 'category_id');
    }

    public function medicalRecordDetails()
    {
        return $this->hasMany(MedicalRecordDetail::class);
    }

    public function restockDetails()
    {
        return $this->hasMany(MedicineRestockDetail::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
}
