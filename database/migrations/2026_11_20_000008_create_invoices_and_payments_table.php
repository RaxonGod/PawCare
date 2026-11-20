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
        // T5: Tagihan Kunjungan (Invoices)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code')->unique();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->foreignId('medical_record_id')->nullable()->constrained('medical_records')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pemilik Hewan
            $table->decimal('service_amount', 12, 2)->default(0);
            $table->decimal('medicine_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // T5: Pembayaran (Payments)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('payment_code')->unique();
            $table->string('payment_method'); // cash, qris, transfer_bank, debit
            $table->decimal('amount_paid', 12, 2);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->dateTime('payment_date');
            $table->foreignId('received_by')->constrained('users')->onDelete('restrict'); // Pelaku T5 (Role 1)
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
