<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineRestockDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_restock_id',
        'medicine_id',
        'quantity',
        'purchase_price',
        'subtotal',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function restock()
    {
        return $this->belongsTo(MedicineRestock::class, 'medicine_restock_id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
