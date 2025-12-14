<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('contracted_treatment_installments', function (Blueprint $table) {
            $table->id();

            // 1. Definimos la columna primero como un entero sin signo
            $table->unsignedBigInteger('contracted_treatment_id');

            // 2. Definimos la llave foránea explícitamente con un nombre corto (segundo argumento)
            $table->foreign('contracted_treatment_id', 'ct_installments_ct_id_fk')
                  ->references('id')
                  ->on('contracted_treatments')
                  ->onDelete('cascade');

            $table->unsignedInteger('installment_number');
            $table->decimal('price', 10, 2);
            $table->string('status')->default('PENDING'); // PENDING, PAID
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracted_treatment_installments');
    }
};
