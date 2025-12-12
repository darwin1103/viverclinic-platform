<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treatment_installments', function (Blueprint $table) {
            $table->id();
            // Relación con el paquete específico
            $table->foreignId('branch_treatment_id')->constrained('branch_treatment')->onDelete('cascade');
            $table->unsignedInteger('installment_number'); // Cuota 1, Cuota 2, etc.
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treatment_installments');
    }
};
