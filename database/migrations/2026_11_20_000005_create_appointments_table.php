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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pelaku T1 (Role 2)
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('restrict');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->text('complaint'); // Keluhan
            $table->enum('status', ['pending', 'approved', 'rejected', 'rescheduled', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null'); // Pelaku T2 (Role 1)
            $table->text('staff_notes')->nullable(); // Alasan penolakan / instruksi / alasan reschedule
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
