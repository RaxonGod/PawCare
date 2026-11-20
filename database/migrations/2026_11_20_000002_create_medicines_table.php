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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('medicine_categories')->onDelete('restrict');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('unit'); // Tablet, Botol, Ampul, Strip, Tube, Vial
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
