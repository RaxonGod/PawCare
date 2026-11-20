<?php

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\User;
use Tests\TestCase;

class MasterCrudValidationTest extends TestCase
{
    protected $staffUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staffUser = User::whereHas('role', fn($q) => $q->where('name', 'staf_klinik'))->first();
    }

    /**
     * Validasi form request menolak penambahan obat tanpa input wajib.
     */
    public function test_validation_rejects_empty_medicine_data(): void
    {
        $response = $this->actingAs($this->staffUser)->post('/medicines', [
            'name' => '',
            'code' => '',
            'category_id' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'code', 'category_id', 'unit', 'purchase_price', 'selling_price']);
    }

    /**
     * Pencegahan hapus data master kategori obat (M2) jika masih memuat data obat (M1).
     */
    public function test_prevents_deleting_category_with_existing_medicines(): void
    {
        $category = MedicineCategory::whereHas('medicines')->first();
        $this->assertNotNull($category);

        $response = $this->actingAs($this->staffUser)->delete("/medicine-categories/{$category->id}");
        
        $response->assertRedirect('/medicine-categories');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('medicine_categories', ['id' => $category->id]);
    }
}
