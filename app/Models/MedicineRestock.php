<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineRestock extends Model
{
    use HasFactory;

    protected $fillable = [
        'restock_code',
        'user_id',
        'supplier_name',
        'invoice_number',
        'restock_date',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'restock_date' => 'date',
        'total_cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(MedicineRestockDetail::class);
    }
}
