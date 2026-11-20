<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\Pet;
use App\Models\User;
use Tests\TestCase;

class TransactionStockTest extends TestCase
{
    protected $staffUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staffUser = User::whereHas('role', fn($q) => $q->where('name', 'staf_klinik'))->first();
    }

    /**
     * T3: Input rekam medis & resep obat mengurangi stok obat M1 dalam DB::transaction.
     */
    public function test_medical_record_decrements_medicine_stock(): void
    {
        $medicine = Medicine::where('stock', '>', 5)->first();
        $initialStock = $medicine->stock;
        $pet = Pet::first();

        $qtyUsed = 2;

        $response = $this->actingAs($this->staffUser)->post('/medical-records', [
            'pet_id' => $pet->id,
            'examination_date' => date('Y-m-d'),
            'symptoms' => 'Tes gejala klinis',
            'diagnosis' => 'Tes diagnosa dokter',
            'treatment_actions' => 'Tes tindakan injeksi',
            'service_fee' => 75000,
            'medicines' => [
                [
                    'medicine_id' => $medicine->id,
                    'quantity' => $qtyUsed,
                    'dosage_instructions' => '1 x sehari sesudah makan',
                ]
            ],
        ]);

        $response->assertSessionHas('success');
        $medicine->refresh();
        $this->assertEquals($initialStock - $qtyUsed, $medicine->stock);
    }

    /**
     * T4: Input restock obat menambah stok obat M1 dalam DB::transaction.
     */
    public function test_restock_increments_medicine_stock(): void
    {
        $medicine = Medicine::first();
        $initialStock = $medicine->stock;

        $qtyAdded = 10;

        $response = $this->actingAs($this->staffUser)->post('/medicine-restocks', [
            'supplier_name' => 'PT Distributor Farmasi Tes',
            'restock_date' => date('Y-m-d'),
            'items' => [
                [
                    'medicine_id' => $medicine->id,
                    'quantity' => $qtyAdded,
                    'purchase_price' => 50000,
                ]
            ],
        ]);

        $response->assertSessionHas('success');
        $medicine->refresh();
        $this->assertEquals($initialStock + $qtyAdded, $medicine->stock);
    }
}
