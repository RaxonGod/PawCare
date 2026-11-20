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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('species'); // Kucing, Anjing, Kelinci, Burung, Hamster, Lainnya
            $table->string('breed')->nullable();
            $table->string('gender', 10); // Jantan, Betina
            $table->date('birth_date')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->string('color')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
