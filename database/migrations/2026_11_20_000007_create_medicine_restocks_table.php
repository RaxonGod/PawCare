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
        // T4 Header: Restock Obat
        Schema::create('medicine_restocks', function (Blueprint $table) {
            $table->id();
            $table->string('restock_code')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Pelaku T4 (Role 1)
            $table->string('supplier_name');
            $table->string('invoice_number')->nullable();
            $table->date('restock_date');
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // T4 Detail: Item Obat yang direstock
        Schema::create('medicine_restock_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_restock_id')->constrained('medicine_restocks')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines')->onDelete('restrict'); // M1
            $table->integer('quantity');
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_restock_details');
        Schema::dropIfExists('medicine_restocks');
    }
};
