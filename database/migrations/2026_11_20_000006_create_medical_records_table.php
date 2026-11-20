<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // T3 Header
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_code')->unique();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->foreignId('pet_id')->constrained('pets')->onDelete('restrict');
            $table->foreignId('vet_id')->constrained('users')->onDelete('restrict'); // Pelaku T3 (Role 1 Dokter)
            $table->date('examination_date');
            $table->decimal('temperature', 4, 1)->nullable(); // Suhu tubuh (°C)
            $table->decimal('weight', 5, 2)->nullable(); // Berat badan saat periksa (kg)
            $table->integer('heart_rate')->nullable(); // bpm
            $table->text('symptoms'); // Gejala klinis
            $table->text('diagnosis'); // Hasil diagnosa
            $table->text('treatment_actions'); // Tindakan medis yang dilakukan
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('total_medicine_fee', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['draft', 'completed'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // T3 Detail: Pemakaian Obat
        Schema::create('medical_record_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines')->onDelete('restrict'); // M1
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->string('dosage_instructions'); // Aturan pemakaian obat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_record_details');
        Schema::dropIfExists('medical_records');
    }
};
