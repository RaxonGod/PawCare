<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\MedicineCategoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\MedicineRestockController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - PawCare Clinic (Sistem Manajemen Klinik Hewan)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES (GUEST, ROLE 1, ROLE 2)
// ==========================================
Route::get('/', [LandingController::class, 'index'])->name('home');

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// 2. AUTHENTICATED ROUTES (ROLE 1 & ROLE 2)
// ==========================================
Route::middleware('auth')->group(function () {
    // Dashboard (Tampilan menyesuaikan role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Master Hewan Peliharaan (Pets)
    Route::resource('pets', PetController::class);

    // Transaksi T1: Janji Temu / Kunjungan Klinik
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Transaksi T3: Rekam Medis (Read-Only untuk Pemilik Hewan)
    Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('medical-records.index');
    Route::get('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'show'])->name('medical-records.show');

    // Transaksi T5: Tagihan Kunjungan (Invoices)
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

    // ==========================================
    // 3. ROLE 1 KHUSUS: STAF & DOKTER KLINIK (staf_klinik)
    // Server-Side Middleware Authorization (HTTP 403 bila ilegal)
    // ==========================================
    Route::middleware('role:staf_klinik')->group(function () {
        // DATA MASTER CRUD LENGKAP:
        // M1: Obat & Perlengkapan Medis (medicines)
        Route::resource('medicines', MedicineController::class);

        // M2: Kategori Obat (medicine_categories)
        Route::resource('medicine-categories', MedicineCategoryController::class);

        // M3: Layanan Klinik (services CRUD)
        Route::resource('services', ServiceController::class);

        // TRANSAKSI T2: Persetujuan, Penolakan, Reschedule Janji Temu
        Route::post('/appointments/{appointment}/approve', [AppointmentController::class, 'approve'])->name('appointments.approve');
        Route::post('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])->name('appointments.reject');
        Route::post('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');

        // TRANSAKSI T3: Input Rekam Medis & Pengurangan Stok Obat (Header-Detail)
        Route::get('/medical-records-create', [MedicalRecordController::class, 'create'])->name('medical-records.create');
        Route::post('/medical-records', [MedicalRecordController::class, 'store'])->name('medical-records.store');

        // TRANSAKSI T4: Restock Obat (Header-Detail & Penambahan Stok Obat)
        Route::resource('medicine-restocks', MedicineRestockController::class)->only(['index', 'create', 'store', 'show']);

        // TRANSAKSI T5: Pembayaran Tagihan
        Route::post('/invoices/{invoice}/pay', [PaymentController::class, 'store'])->name('invoices.pay');

        // TRANSAKSI T5: Laporan Rekapitulasi (Query Builder)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
});
