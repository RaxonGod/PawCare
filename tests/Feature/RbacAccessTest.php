<?php

namespace Tests\Feature;

use App\Models\MedicineCategory;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RbacAccessTest extends TestCase
{
    protected $staffUser;
    protected $ownerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staffUser = User::whereHas('role', fn($q) => $q->where('name', 'staf_klinik'))->first();
        $this->ownerUser = User::whereHas('role', fn($q) => $q->where('name', 'pemilik_hewan'))->first();
    }

    /**
     * Guest dapat melihat katalog layanan publik secara read-only.
     */
    public function test_guest_can_view_public_catalog(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('PawCare Clinic');
        $response->assertSee('Katalog Layanan & Profil Klinik Hewan');
    }

    /**
     * Guest yang mencoba membuka halaman dashboard dialihkan ke halaman login.
     */
    public function test_guest_redirected_to_login_on_protected_page(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Role 2 (Pemilik Hewan) dilarang membuka master obat (M1) dan mengembalikan respons HTTP 403.
     */
    public function test_role_2_forbidden_from_master_medicines(): void
    {
        $response = $this->actingAs($this->ownerUser)->get('/medicines');
        $response->assertStatus(403);
    }

    /**
     * Role 2 (Pemilik Hewan) dilarang membuka laporan rekap (T5) dan mengembalikan respons HTTP 403.
     */
    public function test_role_2_forbidden_from_reports(): void
    {
        $response = $this->actingAs($this->ownerUser)->get('/reports');
        $response->assertStatus(403);
    }

    /**
     * Role 1 (Staf Klinik) memiliki hak akses penuh ke data master dan laporan rekap.
     */
    public function test_role_1_can_access_master_and_reports(): void
    {
        $response = $this->actingAs($this->staffUser)->get('/medicines');
        $response->assertStatus(200);

        $responseReport = $this->actingAs($this->staffUser)->get('/reports');
        $responseReport->assertStatus(200);
    }
}
